<?php

namespace Tests\Raspberry\Webcam\WebcamListener;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Webcam\WebcamEvent;
use Raspberry\Webcam\WebcamListener;
use Raspberry\Webcam\Webcam;

/**
 * @Covers Raspberry\Webcam\WebcamListener
 */
class WebcamListenerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var WebcamListener
     */
    private $subject;

    /**
     * @var Webcam|MockObject
     */
    private $mockWebcam;

    public function setUp()
    {
        parent::setUp();

        $this->mockWebcam = $this->getMock(Webcam::class, [], [], '', false);

        $this->subject = new WebcamListener($this->mockWebcam);
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

        $this->mockWebcam
        ->expects($this->once())
        ->method('takePhoto')
        ->with($name);

        $this->subject->handleWebcamEvent($event);
    }
}
