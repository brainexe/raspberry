<?php

namespace Homie\TodoList\InputControl;

use BrainExe\InputControl\Annotations\InputControlInterface;
use BrainExe\InputControl\Annotations\InputControl as InputControlAnnotation;
use BrainExe\Annotations\Annotations\Inject;
use Homie\TodoList\TodoList as TodoListModel;

/**
 * @InputControlAnnotation(name="TodoList.TodoList")
 */
class TodoList implements InputControlInterface
{

    /**
     * @var TodoListModel
     */
    private $todoList;

    /**
     * @Inject("@TodoList")
     * @param TodoListModel $shoppingList
     */
    public function __construct(TodoListModel $shoppingList)
    {
        $this->todoList = $shoppingList;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            '/^add item (.*)/i' => 'add',
            '/^delete item (\d+)/i' => 'delete',
            '/^assign item (\d+) to (\s+)/i' => 'assign',
            '/^set item status (\d+) to (\d+)/i' => 'setStatus',
        ];
    }

    // TODO
}
