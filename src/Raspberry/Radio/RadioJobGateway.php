<?php

namespace Raspberry\Radio;

use Matze\Core\Traits\RedisTrait;

/**
 * @Service(public=false)
 */
class RadioJobGateway {

	use RedisTrait;

	const REDIS_QUEUE = 'radio_jobs';

	/**
	 * @return array[]
	 */
	public function getJobs() {
		return $this->_getJobs('0', '+inf');
	}

	/**
	 * @param integer $now
	 * @return array[]
	 */
	public function getPendingJobs($now) {
		return $this->_getJobs('0', $now);
	}

	/**
	 * @param integer $from
	 * @param integer $to
	 * @return array[]
	 */
	private function _getJobs($from, $to) {
		$jobs = [];

		$redis_result = $this->getPredis()->ZRANGEBYSCORE(self::REDIS_QUEUE, $from, $to, 'WITHSCORES');
		foreach ($redis_result as $job) {
			list ($radio_id, $status) = $job[0];
			$timestamp = $job[1];

			$jobs[] = [
				'radio_id' => $radio_id,
				'status' => $status,
				'timestamp' => $timestamp,
			];
		}

		return $jobs;
	}

	/**
	 * @param integer $radio_id
	 * @param integer $timestamp
	 * @param string $status
	 */
	public function addRadioJob($radio_id, $timestamp, $status) {
		$predis = $this->getPredis();

		$redis_member = sprintf('%d-%d', $radio_id, $status);
		$predis->ZADD(self::REDIS_QUEUE, $timestamp, $redis_member);
	}
} 