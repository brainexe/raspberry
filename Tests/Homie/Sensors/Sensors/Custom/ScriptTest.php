<?php

namespace Tests\Homie\Sensors\Sensors\Misc;

use Homie\Client\LocalClient;
use Homie\Sensors\Definition;
use Homie\Sensors\Sensors\Misc\Script;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Console\Output\NullOutput;

/**
 * @covers Homie\Sensors\Sensors\Misc\Script
 */
class ScriptTest extends TestCase
{

    /**
     * @var Script
     */
    private $subject;

    /**
     * @var LocalClient|MockObject
     */
    private $client;

    public function setUp()
    {
        $this->client  = $this->getMock(LocalClient::class, [], [], '', false);
        $this->subject = new Script($this->client);
    }

    public function testGetValue()
    {
        $parameter = 'sh foo.sh';
        $value = 10;

        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->with($parameter)
            ->willReturn($value);

        $actual = $this->subject->getValue($parameter);

        $this->assertEquals($value, $actual);
    }

    public function testIsSupported()
    {
        $parameter = null;

        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->with($parameter)
            ->willReturn(1);

        $output = new NullOutput();
        $actual = $this->subject->isSupported($parameter, $output);

        $this->assertTrue($actual);
    }

    public function testGetDefinition()
    {
        $actual = $this->subject->getDefinition();
        $this->assertInstanceOf(Definition::class, $actual);
    }
}
