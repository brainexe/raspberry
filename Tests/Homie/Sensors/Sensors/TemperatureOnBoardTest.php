<?php

namespace Tests\Homie\Sensors\Sensors;

use BrainExe\Core\Util\FileSystem;
use BrainExe\Core\Util\Glob;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Sensors\Definition;
use Homie\Sensors\Formatter\Temperature;
use Homie\Sensors\Sensors\TemperatureOnBoard;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;

/**
 * @covers Homie\Sensors\Sensors\TemperatureOnBoard
 */
class TemperatureOnBoardTest extends TestCase
{

    /**
     * @var TemperatureOnBoard
     */
    private $subject;

    /**
     * @var FileSystem|MockObject
     */
    private $fileSystem;

    /**
     * @var Glob|MockObject
     */
    private $glob;

    public function setUp()
    {
        $this->fileSystem = $this->getMock(Filesystem::class, [], [], '', false);
        $this->glob       = $this->getMock(Glob::class, [], [], '', false);

        $this->subject = new TemperatureOnBoard($this->fileSystem, $this->glob);
    }

    public function testGetSensorType()
    {
        $actualResult = $this->subject->getSensorType();

        $this->assertEquals(TemperatureOnBoard::TYPE, $actualResult);
    }

    public function testGetValue()
    {
        $value     = 12200;
        $parameter = 'mockParameter';

        $this->fileSystem
            ->expects($this->once())
            ->method('fileGetContents')
            ->with($parameter)
            ->willReturn($value);

        $actualResult = $this->subject->getValue($parameter);

        $this->assertEquals(12.2, $actualResult);
    }

    public function testIsSupported()
    {
        $file = 'mockFile';

        $this->fileSystem
            ->expects($this->once())
            ->method('exists')
            ->with($file)
            ->willReturn(true);

        $output       = new DummyOutput();
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

        $output       = new DummyOutput();
        $actualResult = $this->subject->isSupported($file, $output);

        $this->assertFalse($actualResult);
    }

    public function testGetDefinition()
    {
        $definition            = new Definition();
        $definition->name      = _('Temp. Onboard');
        $definition->type      = Definition::TYPE_TEMPERATURE;
        $definition->formatter = Temperature::TYPE;

        $actual = $this->subject->getDefinition();

        $this->assertEquals($definition, $actual);
    }

    public function testSearch()
    {
        $result = ['search', 'foo/cooling'];

        $this->glob
            ->expects($this->once())
            ->method('execGlob')
            ->with('/sys/class/thermal/*/temp')
            ->willReturn($result);

        $actual = $this->subject->search();

        $this->assertEquals(['search'], $actual);
    }
}
