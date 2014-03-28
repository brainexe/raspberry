<?php

namespace Raspberry\Client;

/**
 * @Service("RaspberryClient.SSH", public=false)
 */
class SSHClient implements ClientInterface {

	/**
	 * {@inheritdoc}
	 */
	public function execute($command) {
		throw new \Exception("SSH client is not implemented");
	}
}