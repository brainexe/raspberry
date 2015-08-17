<?php

namespace Tests\Homie\TodoList\InputControl;

use BrainExe\InputControl\Event;
use Homie\TodoList\InputControl\ShoppingList;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Homie\TodoList\ShoppingList as ShoppingListModel;

/**
 * @covers Homie\TodoList\InputControl\ShoppingList
 */
class ShoppingListTest extends TestCase
{

    /**
     * @var ShoppingList
     */
    private $subject;

    /**
     * @var ShoppingListModel|MockObject
     */
    private $shoppingList;

    public function setUp()
    {
        $this->shoppingList = $this->getMock(ShoppingListModel::class, [], [], '', false);
        $this->subject = new ShoppingList($this->shoppingList);
    }

    public function testGetSubscribedEvents()
    {
        $actual = $this->subject->getSubscribedEvents();
        $this->assertInternalType('array', $actual);
    }

    public function testAdd()
    {
        $event = new Event();
        $event->match = 'match';

        $this->shoppingList
            ->expects($this->once())
            ->method('addShoppingListItem')
            ->with('match');

        $this->subject->add($event);
    }

    public function testDelete()
    {
        $event = new Event();
        $event->match = 'match';

        $this->shoppingList
            ->expects($this->once())
            ->method('removeShoppingListItem')
            ->with('match');

        // todo event dispatcher compiler pass: check method exists

        $this->subject->delete($event);
    }
}
