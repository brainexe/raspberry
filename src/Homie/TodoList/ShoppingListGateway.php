<?php

namespace Homie\TodoList;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\RedisTrait;

/**
 * @Service(public=false)
 */
class ShoppingListGateway
{
    use RedisTrait;

    const REDIS_KEY = 'shopping_list';

    /**
     * @return string[]
     */
    public function getShoppingListItems()
    {
        return $this->getRedis()->sMembers(self::REDIS_KEY);
    }

    /**
     * @param string $name
     */
    public function addShoppingListItem($name)
    {
        $this->getRedis()->sAdd(self::REDIS_KEY, $name);
    }

    /**
     * @param string $name
     */
    public function removeShoppingListItem($name)
    {
        $this->getRedis()->sRem(self::REDIS_KEY, $name);
    }
}