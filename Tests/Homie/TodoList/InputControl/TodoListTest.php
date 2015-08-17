<?php

namespace Tests\Homie\TodoList\InputControl;

use Homie\TodoList\InputControl\TodoList;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Homie\TodoList\TodoList as TodoListModel;

/**
 * @covers Homie\TodoList\InputControl\TodoList
 */
class TodoListTest extends TestCase
{

    /**
     * @var TodoList
     */
    private $subject;

    /**
     * @var TodoListModel|MockObject
     */
    private $todoList;

    public function setUp()
    {
        $this->todoList = $this->getMock(TodoListModel::class, [], [], '', false);
        $this->subject  = new TodoList($this->todoList);
    }

    public function testGetSubscribedEvents()
    {
        $actual = $this->subject->getSubscribedEvents();
        $this->assertInternalType('array', $actual);
    }
}
