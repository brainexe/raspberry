<?php

namespace Homie\Radio;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Redis\PhpRedis;
use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\RedisTrait;
use Homie\Radio\VO\RadioVO;

/**
 * @Service(public=false)
 */
class RadioGateway
{

    use RedisTrait;
    use IdGeneratorTrait;

    const REDIS_RADIO     = 'radios:%s';
    const REDIS_RADIO_IDS = 'radio_ids';

    /**
     * @return array[]
     */
    public function getRadios()
    {
        $radioIds = $this->getRadioIds();

        $pipeline = $this->getRedis()->pipeline();

        foreach ($radioIds as $radioId) {
            $pipeline->HGETALL(self::getRadioKey($radioId));
        }

        return $pipeline->execute();
    }

    /**
     * @param integer $radioId
     * @return array
     */
    public function getRadio($radioId)
    {
        return $this->getRedis()->HGETALL($this->getRadioKey($radioId));
    }

    /**
     * @return integer[]
     */
    public function getRadioIds()
    {
        $radioIds = $this->getRedis()->SMEMBERS(self::REDIS_RADIO_IDS);

        sort($radioIds);

        return $radioIds;
    }

    /**
     * @param RadioVO $radioVo
     * @return integer $radioId
     */
    public function addRadio(RadioVO $radioVo)
    {
        $newId = $this->generateRandomId();

        $pipeline = $this->getRedis()->pipeline();

        $key = $this->getRadioKey($newId);
        $pipeline->HMSET($key, [
            'radioId' => $newId,
            'name' => $radioVo->name,
            'description' => $radioVo->description,
            'pin' => $radioVo->pin,
            'code' => $radioVo->code,
            'status' => $radioVo->status,
        ]);

        $this->getRedis()->SADD(self::REDIS_RADIO_IDS, $newId);

        $pipeline->execute();

        $radioVo->radioId = $newId;

        return $newId;
    }

    /**
     * @param RadioVO $radioVo
     */
    public function editRadio(RadioVO $radioVo)
    {
        $key = $this->getRadioKey($radioVo->radioId);

        $redis = $this->getRedis();

        $redis->hMset($key, (array)$radioVo);
    }

    /**
     * @param integer $radioId
     */
    public function deleteRadio($radioId)
    {
        $redis = $this->getRedis();

        $redis->SREM(self::REDIS_RADIO_IDS, $radioId);
        $redis->DEL(self::getRadioKey($radioId));
    }

    /**
     * @param integer $radioId
     * @return string
     */
    private function getRadioKey($radioId)
    {
        return sprintf(self::REDIS_RADIO, $radioId);
    }
}
