<?php

namespace Tests\Raspberry\Client;

use BrainExe\Core\Redis\RedisInterface;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Client\ExecuteCommandEvent;
use Raspberry\Client\MessageQueueClient;
use BrainExe\Core\EventDispatcher\EventDispatcher;

/**
 * @covers Raspberry\Client\MessageQueueClient
 */
class MessageQueueClientTest extends PHPUnit_Framework_TestCase
{

    use RedisMockTrait;

    /**
     * @var MessageQueueClient
     */
    private $subject;

    /**
     * @var RedisInterface|MockObject
     */
    private $redis;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->redis = $this->getRedisMock();
        $this->dispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
        $this->subject = new MessageQueueClient();
        $this->subject->setRedis($this->redis);
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testExecute()
    {
        $command = 'command';

        $event = new ExecuteCommandEvent($command, false);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event, 0);

        $this->subject->execute($command);
    }

    public function testExecuteWithReturn()
    {
        $command = 'command';

        $event = new ExecuteCommandEvent($command, true);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event);

        $this->redis
            ->expects($this->once())
            ->method('brPop')
            ->with(MessageQueueClient::RETURN_CHANNEL, MessageQueueClient::TIMEOUT);

        $this->subject->executeWithReturn($command);
    }
}
