<?php

namespace Homie\Display;

use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\RedisTrait;
use Generator;

class Gateway
{
    const KEY = 'displays';

    use RedisTrait;
    use IdGeneratorTrait;

    /**
     * @param Settings $setting
     */
    public function addDisplay(Settings $setting)
    {
        $setting->id = $this->generateRandomNumericId();

        $this->getRedis()->hset(self::KEY, $setting->id, serialize($setting));
    }

    /**
     * @return Generator|Settings[]
     */
    public function getall()
    {
        $displays = $this->getRedis()->hgetall(self::KEY);

        foreach ($displays as $screenId => $display) {
            yield $screenId => unserialize($display);
        }
    }

    /**
     * @param int $displayId
     */
    public function delete($displayId)
    {
        $this->getRedis()->hdel(self::KEY, $displayId);
    }

}
