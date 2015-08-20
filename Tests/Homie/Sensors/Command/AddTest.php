<?php

namespace Tests\Homie\Sensors\Command;

use Homie\Sensors\Interfaces\Searchable;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Homie\Sensors\Command\Add;
use Homie\Sensors\Interfaces\Parameterized;
use Homie\Sensors\Interfaces\Sensor;
use Homie\Sensors\SensorGateway;
use Homie\Sensors\SensorBuilder;
use Homie\Sensors\SensorVO;
use SebastianBergmann\RecursionContext\Exception;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Tester\CommandTester;

abstract class TestSensor implements Sensor, Parameterized {}
abstract class SearchableTestSensor implements Sensor, Searchable {}

/**
 * @covers Homie\Sensors\Command\Add
 */
class AddTest extends TestCase
{

    /**
     * @var Add
     */
    private $subject;

    /**
     * @var SensorGateway|MockObject
     */
    private $gateway;

    /**
     * @var SensorBuilder|MockObject
     */
    private $builder;

    public function setUp()
    {
        $this->gateway = $this->getMock(SensorGateway::class, [], [], '', false);
        $this->builder = $this->getMock(SensorBuilder::class, [], [], '', false);

        $this->subject = new Add(
            $this->gateway,
            $this->builder
        );
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Parameter "mockparameter" is not supported
     */
    public function testExecuteWhenSensorNotSupportedAndAbort()
    {
        $application = new Application();
        $application->add($this->subject);
        $tester = new CommandTester($this->subject);

        $sensor1 = $this->getMock(TestSensor::class);
        $sensor2 = $this->getMock(TestSensor::class);

        $sensors = [
            $sensorType1 = 'type_1' => $sensor1,
            'type_2' => $sensor2,
        ];

        $output = $this->isInstanceOf(OutputInterface::class);
        $input  = $this->isInstanceOf(InputInterface::class);

        $this->builder
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn($sensors);

        /** @var HelperSet|MockObject $helperSet */
        $helperSet = $this->getMock(HelperSet::class);
        $this->subject->setHelperSet($helperSet);

        /** @var QuestionHelper|MockObject $helper_set */
        $questionHelper = $this->getMock(QuestionHelper::class);

        $helperSet
            ->expects($this->once())
            ->method('get')
            ->with('question')
            ->willReturn($questionHelper);

        $questionHelper
            ->expects($this->at(0))
            ->method('ask')
            ->with(
                $input,
                $output,
                new ChoiceQuestion("Sensor Type", array_keys($sensors))
            )
            ->willReturn($sensorType1);

        $parameter = 'mockparameter';
        $questionHelper
            ->expects($this->at(1))
            ->method('ask')
            ->with($input, $output, new Question("Parameter?\n"))
            ->willReturn($parameter);

        $sensor1
            ->expects($this->once())
            ->method('isSupported')
            ->with($parameter, $output)
            ->willReturn(false);

        $input = ['--force'];
        $tester->execute($input);
    }

    public function testExecute()
    {
        $application = new Application();
        $application->add($this->subject);
        $commandTester = new CommandTester($this->subject);

        $sensor1 = $this->getMock(TestSensor::class);
        $sensor2 = $this->getMock(TestSensor::class);

        $sensors = [
            'type_1' => $sensor1,
            $sensorType2 = 'type_2' => $sensor2,
        ];

        $this->builder
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn($sensors);

        /** @var HelperSet|MockObject $helperSet */
        $helperSet = $this->getMock(HelperSet::class);
        $this->subject->setHelperSet($helperSet);

        $sensor2
            ->expects($this->exactly(3))
            ->method('getSensorType')
            ->willReturn('type_2');

        /** @var QuestionHelper|MockObject $helper_set */
        $helper = $this->getMock(QuestionHelper::class);

        $name        = 'name';
        $description = 'description';
        $parameter   = 'pin';
        $interval    = 12;
        $node        = 2;

        $helperSet
            ->expects($this->once())
            ->method('get')
            ->with('question')
            ->willReturn($helper);

        $output = $this->isInstanceOf(OutputInterface::class);
        $input  = $this->isInstanceOf(InputInterface::class);

        $helper
            ->expects($this->at(0))
            ->method('ask')
            ->with(
                $input,
                $output,
                new ChoiceQuestion("Sensor Type", array_keys($sensors))
            )
            ->willReturn($sensorType2);

        $helper
            ->expects($this->at(1))
            ->method('ask')
            ->with($input, $output, new Question("Parameter?\n"))
            ->willReturn($parameter);

        $helper
            ->expects($this->at(2))
            ->method('ask')
            ->with($input, $output, new Question("Sensor name? (default: Type_2)\n", $sensorType2))
            ->willReturn($name);

        $helper
            ->expects($this->at(3))
            ->method('ask')
            ->with($input, $output, new Question("Description (optional)?\n"))
            ->willReturn($description);

        $helper
            ->expects($this->at(4))
            ->method('ask')
            ->with($input, $output, new Question("Interval in minutes (default: 5)\n", 5))
            ->willReturn($interval);

        $helper
            ->expects($this->at(5))
            ->method('ask')
            ->with($input, $output, new Question("Node? (only for advanced users needed)\n"))
            ->willReturn($node);

        $sensor2
            ->expects($this->once())
            ->method('isSupported')
            ->with($parameter, $output)
            ->willReturn(true);

        $sensor2
            ->expects($this->once())
            ->method('getValue')
            ->with($parameter)
            ->willReturn(null);

        $expectedVo              = new SensorVO();
        $expectedVo->name        = $name;
        $expectedVo->type        = $sensorType2;
        $expectedVo->description = $description;
        $expectedVo->pin         = $parameter;
        $expectedVo->interval    = $interval;
        $expectedVo->node        = $node;
        $expectedVo->color       = '#b06893';

        $this->gateway
            ->expects($this->once())
            ->method('addSensor')
            ->with($expectedVo);

        $input = ['--force'];
        $commandTester->execute($input);

        $display = $commandTester->getDisplay();
        $this->assertEquals(
            "Sensor is supported\nSensor returned invalid data.\n",
            $display
        );
    }

    public function testAddNoParametrized()
    {
        /** @var Sensor $sensor */
        $sensor = $this->getMock(Sensor::class);

        $actual = $this->subject->getParameter($sensor);

        $this->assertNull($actual);
    }

    public function testAddWithInput()
    {
        $parameter = 'parameter';
        /** @var TestSensor $sensor */
        $sensor    = $this->getMock(TestSensor::class);

        /** @var OutputInterface|MockObject $output */
        $output = $this->getMock(OutputInterface::class);
        /** @var InputInterface|MockObject $input */
        $input  = $this->getMock(InputInterface::class);

        $input
            ->expects($this->exactly(2))
            ->method('getArgument')
            ->with('parameter')
            ->willReturn($parameter);

        $this->subject->setInputOutput($input, $output);

        $actual = $this->subject->getParameter($sensor);

        $this->assertEquals($parameter, $actual);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage No possible sensor found
     */
    public function testAddWithoutSearch()
    {
        /** @var SearchableTestSensor|MockObject $sensor */
        $sensor    = $this->getMock(SearchableTestSensor::class);

        /** @var OutputInterface|MockObject $output */
        $output = $this->getMock(OutputInterface::class);
        /** @var InputInterface|MockObject $input */
        $input  = $this->getMock(InputInterface::class);

        $sensor
            ->expects($this->once())
            ->method('search')
            ->willReturn([]);

        $input
            ->expects($this->once())
            ->method('getArgument')
            ->with('parameter')
            ->willReturn(null);

        $this->subject->setInputOutput($input, $output);

        $this->subject->getParameter($sensor);
    }
}
