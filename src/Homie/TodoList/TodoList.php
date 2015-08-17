<?php

namespace Homie\TodoList;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\TimeTrait;
use Homie\TodoList\VO\TodoItemVO;

/**
 * @Service(public=false)
 */
class TodoList
{
    use EventDispatcherTrait;
    use IdGeneratorTrait;
    use TimeTrait;

    /**
     * @var TodoListGateway
     */
    private $gateway;

    /**
     * @var Builder
     */
    private $builder;

    /**
     * @Inject({"@TodoListGateway", "@TodoVoBuilder"})
     * @param TodoListGateway $gateway
     * @param Builder $builder
     */
    public function __construct(TodoListGateway $gateway, Builder $builder)
    {
        $this->gateway = $gateway;
        $this->builder = $builder;
    }

    /**
     * @param UserVO $user
     * @param TodoItemVO $itemVo
     * @return TodoItemVO
     */
    public function addItem(UserVO $user, TodoItemVO $itemVo)
    {
        $now = $this->now();

        $itemVo->todoId    = $this->generateRandomNumericId();
        $itemVo->userId    = $user->id;
        $itemVo->userName  = $user->username;
        $itemVo->createdAt = $itemVo->lastChange = $now;
        $itemVo->status    = TodoItemVO::STATUS_OPEN;
        if ($itemVo->deadline < $now) {
            $itemVo->deadline = 0;
        }

        $this->gateway->addItem($itemVo);

        $event = new TodoListEvent($itemVo, TodoListEvent::ADD);
        $this->dispatchEvent($event);

        return $itemVo;
    }

    /**
     * @return TodoItemVO[]
     */
    public function getList()
    {
        $list = [];
        $rawList = $this->gateway->getList();

        foreach ($rawList as $item) {
            $itemVo = $this->builder->build($item);
            $list[] = $itemVo;
        }

        return $list;
    }

    /**
     * @param integer $itemId
     * @return null|TodoItemVO
     */
    public function getItem($itemId)
    {
        $raw = $this->gateway->getRawItem($itemId);

        if (empty($raw)) {
            return null;
        }

        return $this->builder->build($raw);
    }

    /**
     * @param int $itemId
     * @param array $changes
     * @return TodoItemVO|null
     */
    public function editItem($itemId, array $changes)
    {
        $itemVo = $this->getItem($itemId);
        if (empty($itemVo)) {
            return null;
        }

        $this->gateway->editItem($itemId, $changes);

        $event = new TodoListEvent($itemVo, TodoListEvent::EDIT);
        $this->dispatchEvent($event);

        return $itemVo;
    }

    /**
     * @param int $itemId
     */
    public function deleteItem($itemId)
    {
        $itemVo = $this->getItem($itemId);

        if (empty($itemVo)) {
            return;
        }

        $this->gateway->deleteItem($itemId);

        $event = new TodoListEvent($itemVo, TodoListEvent::REMOVE);
        $this->dispatchEvent($event);
    }
}
