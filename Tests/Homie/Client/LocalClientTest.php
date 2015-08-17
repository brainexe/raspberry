<?php

namespace Tests\Homie\Client\LocalClient;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Client\LocalClient;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use RuntimeException;

/**
 * @covers Homie\Client\LocalClient
 */
class LocalClientTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var ProcessBuilder|MockObject
     */
    private $processBuilder;

    /**
     * @var LocalClient
     */
    private $subject;

    public function setUp()
    {
        $this->processBuilder = $this->getMock(ProcessBuilder::class, [], [], '', false);

        $this->subject = new LocalClient($this->processBuilder);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage error
     */
    public function testExecuteWithReturnAndFail()
    {
        $command = 'command';

        $process = $this->getMock(Process::class, [], [], '', false);

        $this->processBuilder
            ->expects($this->once())
            ->method('setPrefix')
            ->with([$command])
            ->willReturnSelf();

        $this->processBuilder
            ->expects($this->once())
            ->method('setArguments')
            ->with(['foo'])
            ->willReturnSelf();

        $this->processBuilder
            ->expects($this->once())
            ->method('setTimeout')
            ->with(LocalClient::TIMEOUT)
            ->willReturnSelf();

        $this->processBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($process);

        $process
            ->expects($this->once())
            ->method('run');

        $process
            ->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(false);

        $process
            ->expects($this->once())
            ->method('getErrorOutput')
            ->willReturn('error');

        $this->subject->executeWithReturn($command, ['foo']);
    }

    public function testExecuteWithReturn()
    {
        $command = 'command';
        $output  = 'output';
        $process = $this->getMock(Process::class, [], [], '', false);

        $this->processBuilder
            ->expects($this->once())
            ->method('setPrefix')
            ->with([$command])
            ->willReturnSelf();

        $this->processBuilder
            ->expects($this->once())
            ->method('setArguments')
            ->with([])
            ->willReturnSelf();

        $this->processBuilder
            ->expects($this->once())
            ->method('setTimeout')
            ->with(LocalClient::TIMEOUT)
            ->willReturnSelf();

        $this->processBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($process);

        $process
            ->expects($this->once())
            ->method('run');

        $process
            ->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(true);

        $process
            ->expects($this->once())
            ->method('getOutput')
            ->willReturn($output);

        $actualResult = $this->subject->executeWithReturn($command);
        $this->assertEquals($output, $actualResult);
    }

    public function testExecute()
    {
        $command = 'command';
        $output  = 'output';
        $process = $this->getMock(Process::class, [], [], '', false);

        $this->processBuilder
            ->expects($this->once())
            ->method('setPrefix')
            ->with([$command])
            ->willReturnSelf();

        $this->processBuilder
            ->expects($this->once())
            ->method('setArguments')
            ->with([])
            ->willReturnSelf();

        $this->processBuilder
            ->expects($this->once())
            ->method('setTimeout')
            ->with(LocalClient::TIMEOUT)
            ->willReturnSelf();

        $this->processBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($process);

        $process
            ->expects($this->once())
            ->method('run');

        $process
            ->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(true);

        $process
            ->expects($this->once())
            ->method('getOutput')
            ->willReturn($output);

        $this->subject->execute($command);
    }
}
