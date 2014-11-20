<?php

namespace Tests\Raspberry\Sensors\Sensors\HumidDHT11Sensor;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Sensors\Sensors\AbstractDHT11Sensor;
use Raspberry\Sensors\Sensors\HumidDHT11Sensor;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @Covers Raspberry\Sensors\Sensors\HumidDHT11Sensor
 */
class HumidDHT11SensorTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var HumidDHT11Sensor
	 */
	private $_subject;

	/**
	 * @var ProcessBuilder|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockProcessBuilder;

	/**
	 * @var Filesystem|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockFileSystem;

	public function setUp() {
		$this->_mockProcessBuilder = $this->getMock(ProcessBuilder::class, [], [], '', false);
		$this->_mockFileSystem = $this->getMock(Filesystem::class, [], [], '', false);

		$this->_subject = new HumidDHT11Sensor($this->_mockProcessBuilder, $this->_mockFileSystem);
	}

	public function testGetSensorType() {
		$actual_result = $this->_subject->getSensorType();

		$this->assertEquals(HumidDHT11Sensor::TYPE, $actual_result);
	}

	public function testGetValueWitInvalidOutput() {
		$pin = 3;

		$process = $this->getMock(Process::class, [], [], '', false);

		$this->_mockProcessBuilder
			->expects($this->once())
			->method('setArguments')
			->will($this->returnValue($this->_mockProcessBuilder));

		$this->_mockProcessBuilder
			->expects($this->once())
			->method('getProcess')
			->will($this->returnValue($process));

		$process->expects($this->once())
			->method('run');

		$process->expects($this->once())
			->method('isSuccessful')
			->will($this->returnValue(false));

		$actual_result = $this->_subject->getValue($pin);

		$this->assertNull($actual_result);
	}

	public function testGetValueWitValidOutput() {
		$humid = 70;
		$pin   = 3;

		$output = "Hum = $humid %";

		$process = $this->getMock(Process::class, [], [], '', false);

		$this->_mockProcessBuilder
			->expects($this->once())
			->method('setArguments')
			->will($this->returnValue($this->_mockProcessBuilder));

		$this->_mockProcessBuilder
			->expects($this->once())
			->method('getProcess')
			->will($this->returnValue($process));

		$process->expects($this->once())
			->method('run');

		$process->expects($this->once())
			->method('isSuccessful')
			->will($this->returnValue(true));

		$process->expects($this->once())
			->method('getOutput')
			->will($this->returnValue($output));

		$actual_result = $this->_subject->getValue($pin);

		$this->assertEquals($humid, $actual_result);
	}

	/**
	 * @param float $given
	 * @param string $expected_result
	 * @dataProvider provideFormatValues
	 */
	public function testFormatValue($given, $expected_result) {
		$actual_result = $this->_subject->formatValue($given);

		$this->assertEquals($expected_result, $actual_result);
	}
	/**
	 * @param float $given
	 * @param string $expected_result
	 * @dataProvider provideEspeakText
	 */
	public function testGetEspeakText($given, $expected_result) {
		$actual_result = $this->_subject->getEspeakText($given);

		$this->assertEquals($expected_result, $actual_result);
	}

	public function testIsSupported() {
		$file = sprintf(AbstractDHT11Sensor::ADA_SCRIPT);

		$this->_mockFileSystem
			->expects($this->once())
			->method('exists')
			->with($file)
			->will($this->returnValue(true));

		$output = new DummyOutput();
		$actual_result = $this->_subject->isSupported($output);

		$this->assertTrue($actual_result);
	}

	public function testIsSupportedWhenNotSupported() {
		$file = sprintf(AbstractDHT11Sensor::ADA_SCRIPT);

		$this->_mockFileSystem
			->expects($this->once())
			->method('exists')
			->with($file)
			->will($this->returnValue(false));

		$output = new DummyOutput();
		$actual_result = $this->_subject->isSupported($output);

		$this->assertFalse($actual_result);
	}

	public function provideEspeakText() {
		return [
			[100, '100 Percent'],
			[0, '0 Percent'],
			['', '0 Percent'],
			[10.2222, '10 Percent'],
			[-10.2222, '-10 Percent'],
		];
	}

	public function provideFormatValues() {
		return [
			[100, '100%'],
			[0, '0%'],
			['', '0%'],
			[10.2222, '10%'],
			[-10.2222, '-10%'],
		];
	}

}