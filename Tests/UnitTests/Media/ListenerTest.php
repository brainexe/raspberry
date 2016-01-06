<?php

namespace Tests\Homie\Media;

use Homie\Media\Listener;
use Homie\Media\Sound;
use Homie\Media\SoundEvent;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class ListenerTest extends TestCase
{

    /**
     * @var Listener
     */
    private $subject;

    /**
     * @var Sound|MockObject
     */
    private $sound;

    public function setUp()
    {
        $this->sound   = $this->getMock(Sound::class, [], [], '', false);
        $this->subject = new Listener($this->sound);
    }

    public function testGetSubscribedEvents()
    {
        $actualResult = $this->subject->getSubscribedEvents();
        $this->assertInternalType('array', $actualResult);
    }

    public function testHandleEvent()
    {
        $fileName  = 'file.mp3';
        $event = new SoundEvent($fileName);

        $this->sound
            ->expects($this->once())
            ->method('playSound')
            ->with($fileName);

        $this->subject->handleEvent($event);
    }
}