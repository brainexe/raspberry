<?php

namespace Tests\Homie\Espeak;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\Espeak\Command;
use Homie\Espeak\EspeakEvent;
use Homie\Espeak\EspeakVO;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers Homie\Espeak\Command
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
    private $dispatcher;

    public function setUp()
    {
        $this->dispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
        $this->subject = new Command();
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testExecute()
    {
        $text = 'nice text';

        $application = new Application();
        $application->add($this->subject);

        $commandTester = new CommandTester($this->subject);

        $espeakVo = new EspeakVO($text);
        $event = new EspeakEvent($espeakVo);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        $commandTester->execute(['text' => $text]);
    }
}
