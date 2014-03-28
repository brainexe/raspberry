<?php

namespace Raspberry\Gpio;

use Matze\Core\Traits\RedisTrait;

/**
 * @codeCoverageIgnore
 * @Service(public=false)
 */
class PinGateway {
	const REDIS_PINS = 'pins';

	use RedisTrait;

	/**
	 * @return array[]
	 */
	public function getPinDescriptions() {
		$redis = $this->getRedis();

		return $redis->HGETALL(self::REDIS_PINS);
	}

} 