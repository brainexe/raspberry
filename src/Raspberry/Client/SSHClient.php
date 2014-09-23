<?php

namespace Raspberry\Client;

use Exception;

/**
 * @Service("RaspberryClient.SSH", public=false)
 */
class SSHClient implements ClientInterface {

	/**
	 * {@inheritdoc}
	 */
	public function execute($command) {
		throw new Exception("SSH client is not implemented");
	}

	/**
	 * {@inheritdoc}
	 */
	public function executeWithReturn($command) {
		throw new Exception("SSH client is not implemented");

	}
}