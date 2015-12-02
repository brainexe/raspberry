<?php

namespace Tests\Homie\Espeak;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Espeak\Controller;
use Homie\Espeak\EspeakEvent;
use Homie\Espeak\EspeakVO;
use Symfony\Component\HttpFoundation\Request;
use Homie\Espeak\Espeak;
use BrainExe\Core\Util\TimeParser;
use BrainExe\Core\EventDispatcher\EventDispatcher;

/**
 * @covers Homie\Espeak\Controller
 */
class ControllerTest extends TestCase
{

    /**
     * @var Controller
     */
    private $subject;

    /**
     * @var Espeak|MockObject
     */
    private $espeak;

    /**
     * @var TimeParser|MockObject
     */
    private $timeParser;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->espeak     = $this->getMock(Espeak::class, [], [], '', false);
        $this->timeParser = $this->getMock(TimeParser::class, [], [], '', false);
        $this->dispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new Controller($this->espeak, $this->timeParser);
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testSpeakers()
    {
        $speakers = ['speakers'];
        $jobs = ['jobs'];

        $this->espeak
            ->expects($this->once())
            ->method('getSpeakers')
            ->willReturn($speakers);

        $actualResult = $this->subject->speakers();

        $expectedResult = [
            'speakers' => $speakers,
        ];

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testSpeak()
    {
        $request   = new Request();
        $speaker   = 'speaker';
        $text      = 'text';
        $volume    = 120;
        $speed     = 80;
        $delayRaw  = 'delay_row';
        $timestamp = 10;

        $request->request->set('speaker', $speaker);
        $request->request->set('text', $text);
        $request->request->set('volume', $volume);
        $request->request->set('speed', $speed);
        $request->request->set('delay', $delayRaw);

        $this->timeParser
            ->expects($this->once())
            ->method('parseString')
            ->with($delayRaw)
            ->willReturn($timestamp);

        $espeakVo = new EspeakVO($text, $volume, $speed, $speaker);
        $event    = new EspeakEvent($espeakVo);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event, $timestamp);

        $actual = $this->subject->speak($request);

        $this->assertTrue($actual);
    }
}