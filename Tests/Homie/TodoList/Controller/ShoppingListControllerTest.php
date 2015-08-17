<?php

namespace Tests\Homie\TodoList\Controller;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\TodoList\Controller\ShoppingListController;
use Homie\TodoList\ShoppingList;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers Homie\TodoList\Controller\ShoppingListController
 */
class ShoppingListControllerTest extends TestCase
{

    /**
     * @var ShoppingListController
     */
    private $subject;

    /**
     * @var ShoppingList|MockObject
     */
    private $shoppingList;


    public function setUp()
    {
        $this->shoppingList         = $this->getMock(ShoppingList::class, [], [], '', false);

        $this->subject = new ShoppingListController(
            $this->shoppingList
        );
    }

    public function testIndex()
    {
        $shoppingList = 'shoppingList';

        $this->shoppingList
            ->expects($this->once())
            ->method('getShoppingListItems')
            ->willReturn($shoppingList);

        $actualResult   = $this->subject->index();
        $expectedResult = new JsonResponse(
            [
                'shoppingList' => $shoppingList
            ]
        );

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testAddShoppingListItem()
    {
        $name = 'name';

        $request = new Request();
        $request->request->set('name', $name);

        $this->shoppingList
            ->expects($this->once())
            ->method('addShoppingListItem')
            ->with($name);

        $actualResult = $this->subject->addShoppingListItem($request);

        $expectedResult = new JsonResponse(true);
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testRemoveShoppingListItem()
    {
        $name = 'name';

        $request = new Request();
        $request->request->set('name', $name);

        $this->shoppingList
            ->expects($this->once())
            ->method('removeShoppingListItem')
            ->with($name);

        $actualResult = $this->subject->removeShoppingListItem($request);

        $expectedResult = new JsonResponse(true);
        $this->assertEquals($expectedResult, $actualResult);
    }
}
