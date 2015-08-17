<?php

namespace Homie\Sensors\Sensors\Aggregate;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\RedisTrait;

/**
 * @Service("Sensor.Sensor.Aggregated.Aggregated", public=false)
 */
class Aggregated
{
    const REDIS_KEY = 'sensor:aggregate';

    use RedisTrait;

    /**
     * @param $identifier
     * @param float $value
     */
    public function addValue($identifier, $value)
    {
        $this->getRedis()->hincrbyfloat(self::REDIS_KEY, $identifier, $value);
    }

    /**
     * @param $identifier
     * @return string
     */
    public function getCurrent($identifier)
    {
        $redis = $this->getRedis();
        $current = $redis->hget(self::REDIS_KEY, $identifier);
        if ($current) {
            $redis->hdel(self::REDIS_KEY, [$identifier]);
        }

        return $current;
    }
}
