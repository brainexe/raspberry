<?php

namespace Tests\Homie\TodoList\TodoListGateway;

use BrainExe\Core\Redis\Predis;
use BrainExe\Core\Util\Time;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\TodoList\TodoListGateway;
use Homie\TodoList\VO\TodoItemVO;

/**
 * @covers Homie\TodoList\TodoListGateway
 */
class TodoListGatewayTest extends PHPUnit_Framework_TestCase
{

    use RedisMockTrait;

    /**
     * @var TodoListGateway
     */
    private $subject;

    /**
     * @var Predis|MockObject
     */
    private $redis;

    /**
     * @var Time|MockObject
     */
    private $time;

    public function setUp()
    {
        $this->redis = $this->getRedisMock();
        $this->time  = $this->getMock(Time::class, [], [], '', false);

        $this->subject = new TodoListGateway();
        $this->subject->setRedis($this->redis);
        $this->subject->setTime($this->time);
    }

    public function testAddItem()
    {
        $itemVo = new TodoItemVO();
        $itemVo->todoId = $todoId = 10;

        $this->redis
            ->expects($this->once())
            ->method('HMSET')
            ->with("todo:$todoId", $this->isType('array'));

        $this->redis
            ->expects($this->once())
            ->method('sAdd')
            ->with(TodoListGateway::TODO_IDS, $itemVo->todoId);

        $this->subject->addItem($itemVo);
    }

    public function testGetList()
    {

        $itemRaw1 = [
            'id' => $item1Id = 42
        ];

        $itemRaw2 = [
              'id' => $item2Id = 43
        ];

        $this->redis
            ->expects($this->at(0))
            ->method('sMembers')
            ->with("todo_ids")
            ->willReturn([$item1Id, $item2Id]);

        $this->redis
            ->expects($this->at(1))
            ->method('pipeline')
            ->willReturn($this->redis);

        $this->redis
            ->expects($this->at(2))
            ->method('HGETALL')
            ->with("todo:$item1Id")
            ->willReturn($itemRaw1);

        $this->redis
            ->expects($this->at(3))
            ->method('HGETALL')
            ->with("todo:$item2Id")
            ->willReturn($itemRaw2);

        $this->redis
            ->expects($this->at(4))
            ->method('execute')
            ->willReturn([$itemRaw1, $itemRaw2]);

        $actualResult = $this->subject->getList();
        $this->assertEquals([$itemRaw1, $itemRaw2], $actualResult);
    }

    public function testGetRawItem()
    {
        $itemId = 10;

        $itemRaw = [
                'id' => $itemId
        ];

        $this->redis
            ->expects($this->once())
            ->method('HGETALL')
            ->with("todo:$itemId")
            ->willReturn($itemRaw);

        $actualResult = $this->subject->getRawItem($itemId);

        $this->assertEquals($itemRaw, $actualResult);
    }

    public function testEditItem()
    {
        $itemId = 10;
        $now = 1000;

        $changes = [
                'name' => 'change'
        ];

        $this->time
            ->expects($this->once())
            ->method('now')
            ->willReturn($now);

        $changes['lastChange'] = $now;
        $this->redis
            ->expects($this->once())
            ->method('hMSet')
            ->with("todo:$itemId", $changes);

        $this->subject->editItem($itemId, $changes);
    }

    public function testDeleteItem()
    {
        $itemId = 10;

        $this->redis
            ->expects($this->once())
            ->method('del')
            ->with("todo:$itemId");

        $this->redis
            ->expects($this->once())
            ->method('sRem')
            ->with(TodoListGateway::TODO_IDS, $itemId);

        $this->subject->deleteItem($itemId);
    }
}
