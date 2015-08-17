<?php

namespace Tests\Homie\Webcam;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\Webcam\InputControl;
use Homie\Webcam\WebcamEvent;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Homie\Webcam\InputControl
 */
class InputControlTest extends TestCase
{

    /**
     * @var InputControl
     */
    private $subject;
    /**
     * @var EventDispatcher|MockObject
     */
    private $eventDispatcher;

    public function setUp()
    {
        $this->eventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
        $this->subject         = new InputControl();
        $this->subject->setEventDispatcher($this->eventDispatcher);
    }

    public function testGetSubscribedEvents()
    {
        $actual = $this->subject->getSubscribedEvents();
        $this->assertInternalType('array', $actual);
    }

    public function testTakeShot()
    {
        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($this->isInstanceOf(WebcamEvent::class));

        $this->subject->takeShot();
    }
}
