<?php

namespace Tests\Homie\Client\MessageQueueClientListener;

use BrainExe\Core\Redis\Predis;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Client\ExecuteCommandEvent;
use Homie\Client\MessageQueueClient;
use Homie\Client\MessageQueueClientListener;
use Homie\Client\LocalClient;

class MessageQueueClientListenerTest extends PHPUnit_Framework_TestCase
{

    use RedisMockTrait;

    /**
     * @var MessageQueueClientListener
     */
    private $subject;

    /**
     * @var LocalClient|MockObject
     */
    private $client;

    /**
     * @var Predis|MockObject
     */
    private $redis;

    public function setUp()
    {
        $this->client = $this->getMock(LocalClient::class, [], [], '', false);
        $this->redis  = $this->getRedisMock();

        $this->subject = new MessageQueueClientListener($this->client);
        $this->subject->setRedis($this->redis);
    }

    public function testGetSubscribedEvents()
    {
        $actualResult = $this->subject->getSubscribedEvents();
        $this->assertInternalType('array', $actualResult);
    }

    public function testHandleExecuteEventWithoutReturn()
    {
        $command = 'command';

        $event = new ExecuteCommandEvent($command, [], true);

        $output = 'output';

        $this->redis
            ->expects($this->once())
            ->method('lPush')
            ->with(MessageQueueClient::RETURN_CHANNEL, $output);

        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->with($command)
            ->willReturn($output);

        $this->subject->handleExecuteEvent($event);
    }
}
