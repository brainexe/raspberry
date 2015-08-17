<?php

namespace Tests\Homie\Sensors\Sensors\Humid;

use Homie\Client\ClientInterface;
use Homie\Sensors\Sensors\Humid\DHT11;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @covers Homie\Sensors\Sensors\Humid\DHT11
 */
class DHT11Test extends TestCase
{

    /**
     * @var DHT11
     */
    private $subject;

    /**
     * @var ClientInterface|MockObject
     */
    private $client;

    /**
     * @var Filesystem|MockObject
     */
    private $fileSystem;

    public function setUp()
    {
        $this->client     = $this->getMock(ClientInterface::class, [], [], '', false);
        $this->fileSystem = $this->getMock(Filesystem::class, [], [], '', false);

        $this->subject = new DHT11($this->client, $this->fileSystem, '/ada/');
    }

    public function testGetSensorType()
    {
        $actualResult = $this->subject->getSensorType();

        $this->assertEquals(DHT11::TYPE, $actualResult);
    }

    public function testGetValueWitValidOutput()
    {
        $humid     = 70;
        $parameter = 3;
        $output    = "Hum = $humid %";

        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->willReturn($output);

        $actualResult = $this->subject->getValue($parameter);

        $this->assertEquals($humid, $actualResult);
    }

    public function testIsSupported()
    {
        $file = 'mockFile';

        $this->fileSystem
            ->expects($this->once())
            ->method('exists')
            ->with($file)
            ->willReturn(true);

        $output = new DummyOutput();
        $actualResult = $this->subject->isSupported($file, $output);

        $this->assertTrue($actualResult);
    }

    public function testIsSupportedWhenNotSupported()
    {
        $file = 'mockFile';

        $this->fileSystem
            ->expects($this->once())
            ->method('exists')
            ->with($file)
            ->willReturn(false);

        $output = new DummyOutput();
        $actualResult = $this->subject->isSupported($file, $output);

        $this->assertFalse($actualResult);
    }
}
