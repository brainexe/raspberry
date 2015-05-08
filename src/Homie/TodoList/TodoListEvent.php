<?php

namespace Homie\TodoList;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\PushViaWebsocket;
use Homie\TodoList\VO\TodoItemVO;

class TodoListEvent extends AbstractEvent implements PushViaWebsocket
{
    const ADD    = 'todo.add';
    const REMOVE = 'todo.remove';
    const EDIT   = 'todo.edit';

    /**
     * @var TodoItemVO
     */
    public $itemVo;

    /**
     * @param TodoItemVO $itemVo
     * @param string $eventName
     */
    public function __construct(TodoItemVO $itemVo, $eventName)
    {
        parent::__construct($eventName);
        $this->itemVo = $itemVo;
    }
}
