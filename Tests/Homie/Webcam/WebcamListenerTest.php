<?php

namespace Tests\Homie\Webcam\WebcamListener;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Webcam\WebcamEvent;
use Homie\Webcam\WebcamListener;
use Homie\Webcam\Webcam;

/**
 * @covers Homie\Webcam\WebcamListener
 */
class WebcamListenerTest extends TestCase
{

    /**
     * @var WebcamListener
     */
    private $subject;

    /**
     * @var Webcam|MockObject
     */
    private $webcam;

    public function setUp()
    {
        $this->webcam  = $this->getMock(Webcam::class, [], [], '', false);
        $this->subject = new WebcamListener($this->webcam);
    }

    public function testGetSubscribedEvents()
    {
        $actualResult = $this->subject->getSubscribedEvents();
        $this->assertInternalType('array', $actualResult);
    }

    public function testHandleWebcamEvent()
    {
        $name = 'shoot 123';
        $event = new WebcamEvent($name, WebcamEvent::TAKE_PHOTO);

        $this->webcam
            ->expects($this->once())
            ->method('takePhoto')
            ->with($name);

        $this->subject->handleWebcamEvent($event);
    }
}
