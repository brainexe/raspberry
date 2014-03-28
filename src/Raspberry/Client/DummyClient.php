<?php

namespace Raspberry\Client;

use Matze\Core\Traits\LoggerTrait;

/**
 * @Service("RaspberryClient.Dummy", public=false)
 */
class DummyClient implements ClientInterface {

	use LoggerTrait;

	/**
	 * @param string $command
	 */
	public function execute($command) {
		$this->info($command);
	}
}