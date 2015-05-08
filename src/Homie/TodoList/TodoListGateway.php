<?php

namespace Homie\TodoList;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Redis\PhpRedis;
use BrainExe\Core\Traits\RedisTrait;
use BrainExe\Core\Traits\TimeTrait;
use Homie\TodoList\VO\TodoItemVO;

/**
 * @Service(public=false)
 */
class TodoListGateway
{

    use RedisTrait;
    use TimeTrait;

    const TODO_KEY = 'todo:%d';
    const TODO_IDS = 'todo_ids';

    /**
     * @param TodoItemVO $itemVo
     */
    public function addItem(TodoItemVO $itemVo)
    {
        $redis = $this->getRedis();

        $raw = (array)$itemVo;

        $redis->HMSET($this->getRedisKey($itemVo->todoId), $raw);

        $redis->sAdd(self::TODO_IDS, $itemVo->todoId);
    }

    /**
     * @return array[]
     */
    public function getList()
    {
        $itemIds = $this->getRedis()->sMembers(self::TODO_IDS);

        $redis = $this->getRedis()->pipeline();
        foreach ($itemIds as $itemId) {
            $redis->HGETALL($this->getRedisKey($itemId));
        }

        return $redis->execute();
    }

    /**
     * @param integer $itemId
     * @return array
     */
    public function getRawItem($itemId)
    {
        return $this->getRedis()->HGETALL($this->getRedisKey($itemId));
    }

    /**
     * @param int $itemId
     * @param array $changes
     */
    public function editItem($itemId, array $changes)
    {
        $key = $this->getRedisKey($itemId);

        $changes['lastChange'] = $this->now();

        $this->getRedis()->hMSet($key, $changes);
    }

    /**
     * @param integer $itemId
     */
    public function deleteItem($itemId)
    {
        $key = $this->getRedisKey($itemId);

        $this->getRedis()->del($key);
        $this->getRedis()->sRem(self::TODO_IDS, $itemId);
    }

    /**
     * @param integer $itemId
     * @return string
     */
    private function getRedisKey($itemId)
    {
        return sprintf(self::TODO_KEY, $itemId);
    }
}
