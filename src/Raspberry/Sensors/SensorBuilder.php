<?php

namespace Raspberry\Sensors;

use Raspberry\Sensors\Sensors\SensorInterface;
use Matze\Annotations\Annotations as DI;

/**
 * @DI\Service(public=false)
 */
class SensorBuilder {

	/**
	 * @var SensorInterface[]
	 */
	private $_sensors;

	/**
	 * @return SensorInterface[]
	 */
	public function getSensors() {
		return $this->_sensors;
	}

	/**
	 * @param string $type
	 * @param SensorInterface $sensor
	 */
	public function addSensor($type, SensorInterface $sensor) {
		$this->_sensors[$type] = $sensor;
	}

	/**
	 * @param string$sensor_type
	 * @throws \Exception
	 * @return SensorInterface
	 */
	public function build($sensor_type) {
		if (!empty($this->_sensors[$sensor_type])) {
			return $this->_sensors[$sensor_type];
		}

		throw new \Exception(sprintf('Invalid sensor type: %s', $sensor_type));
	}

}