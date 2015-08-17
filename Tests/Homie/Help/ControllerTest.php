<?php

namespace Tests\Homie\Help;

use BrainExe\Core\Redis\Predis;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Homie\Help\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers Homie\Help\Controller
 */
class ControllerTest extends TestCase
{

    use RedisMockTrait;

    /**
     * @var Controller
     */
    private $subject;

    /**
     * @var Predis|MockObject
     */
    private $redis;

    public function setUp()
    {
        $this->redis = $this->getRedisMock();

        $this->subject = new Controller();
        $this->subject->setRedis($this->redis);
    }

    public function testAll()
    {
        $all = ['all'];

        $this->redis
            ->expects($this->once())
            ->method('hGetall')
            ->with(Controller::KEY)
            ->willReturn($all);

        $actual = $this->subject->all();

        $this->assertEquals($all, $actual);
    }
    public function testSave()
    {
        $type    = 'mockType';
        $content = 'mockContent';

        $this->redis
            ->expects($this->once())
            ->method('hset')
            ->with(Controller::KEY, $type, $content);

        $request = new Request();
        $request->request->set('content', $content);
        $actual = $this->subject->save($request, $type);

        $this->assertTrue($actual);
    }
    public function testDelete()
    {
        $type = 'mockType';

        $this->redis
            ->expects($this->once())
            ->method('hdel')
            ->with(Controller::KEY, $type);

        $request = new Request();
        $actual = $this->subject->delete($request, $type);

        $this->assertTrue($actual);
    }
}
