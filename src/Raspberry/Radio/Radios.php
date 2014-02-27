<?php

namespace Raspberry\Radio;

use InvalidArgumentException;

/**
 * @Service(public=false)
 */
class Radios {

	public static $radio_pins = [
		'A' => 1,
		'B' => 2,
		'C' => 3,
		'D' => 4,
		'E' => 5,
	];

	/**
	 * @var RadioGateway
	 */
	private $_radio_gateway;

	/**
	 * @Inject("@RadioGateway")
	 */
	public function __construct(RadioGateway $radio_gateway) {
		$this->_radio_gateway = $radio_gateway;
	}

	/**
	 * @param integer|string $pin
	 * @return integer
	 * @throws InvalidArgumentException
	 */
	public function getRadioPin($pin) {
		if (is_int($pin)) {
			$flipped = array_flip(self::$radio_pins);
			if (!isset($flipped[$pin])) {
				throw new InvalidArgumentException(sprintf("Invalid pin: %s", $pin));
			}
			return $pin;
		}
		$pin = strtoupper($pin);
		if (empty(self::$radio_pins[$pin])) {
			throw new InvalidArgumentException(sprintf("Invalid pin: %s", $pin));
		}

		return self::$radio_pins[$pin];
	}

	/**
	 * @param integer $radio_id
	 * @return array
	 */
	public function getRadio($radio_id) {
		return $this->_radio_gateway->getRadio($radio_id);
	}

	/**
	 * @return array[]
	 */
	public function getRadios() {
		return $this->_radio_gateway->getRadios();
	}

	/**
	 * @param string $name
	 * @param string $description
	 * @param string $code
	 * @param integer $pin
	 * @return integer $radio_id
	 */
	public function addRadio($name, $description, $code, $pin) {
		return $this->_radio_gateway->addRadio($name, $description, $code, $pin);
	}

	/**
	 * @param integer $radio_id
	 */
	public function deleteRadio($radio_id) {
		$this->_radio_gateway->deleteRadio($radio_id);
	}

}
