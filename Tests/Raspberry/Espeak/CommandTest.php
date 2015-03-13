<?php

namespace Tests\Raspberry\Espeak;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use Raspberry\Espeak\Command;
use Raspberry\Espeak\EspeakEvent;
use Raspberry\Espeak\EspeakVO;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @Covers Raspberry\Espeak\Command
 */
class CommandTest extends TestCase
{

    /**
     * @var Command
     */
    private $subject;

    /**
     * @var EventDispatcher|MockObject
     */
    private $mockEventDispatcher;

    public function setUp()
    {
        $this->mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
        $this->subject = new Command();
        $this->subject->setEventDispatcher($this->mockEventDispatcher);
    }

    public function testExecute()
    {
        $text = 'nice text';

        $application = new Application();
        $application->add($this->subject);

        $commandTester = new CommandTester($this->subject);

        $espeakVo = new EspeakVO($text);
        $event = new EspeakEvent($espeakVo);

        $this->mockEventDispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        $commandTester->execute(['text' => $text]);
    }
}