<?php

namespace Raspberry\Gpio;

use Matze\Core\Traits\PDOTrait;
use PDO;
use Matze\Annotations\Annotations as DI;

/**
 * @DI\Service(public=false)
 */
class PinGateway {
	use PDOTrait;

	/**
	 * @return array[]
	 */
	public function getPinDescriptions() {
		$query = 'SELECT id, description FROM pins';

		$stm = $this->getPDO()->prepare($query);
		$stm->execute();

		return $stm->fetchAll(PDO::FETCH_KEY_PAIR);
	}

} 