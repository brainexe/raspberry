<?php

namespace Homie\TodoList\Controller;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller;
use BrainExe\Core\Annotations\Route;
use Homie\TodoList\ShoppingList;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller("ShoppingListController")
 */
class ShoppingListController
{

    /**
     * @var ShoppingList
     */
    private $shoppingList;

    /**
     * @Inject({"@ShoppingList"})
     * @param ShoppingList $list
     */
    public function __construct(
        ShoppingList $list
    ) {
        $this->shoppingList = $list;
    }

    /**
     * @Route("/todo/shopping/", name="todo.shopping.index")
     * @return JsonResponse
     */
    public function index()
    {
        $shoppingList = $this->shoppingList->getShoppingListItems();

        return new JsonResponse([
            'shoppingList' => $shoppingList,
        ]);
    }

    /**
     * @param Request $request
     * @Route("/todo/shopping/add/", name="todo.shopping.add")
     * @return JsonResponse
     */
    public function addShoppingListItem(Request $request)
    {
        $name = $request->request->get('name');

        $this->shoppingList->addShoppingListItem($name);

        return new JsonResponse(true);
    }

    /**
     * @param Request $request
     * @Route("/todo/shopping/remove/", name="todo.shopping.remove")
     * @return JsonResponse
     */
    public function removeShoppingListItem(Request $request)
    {
        $name = $request->request->get('name');

        $this->shoppingList->removeShoppingListItem($name);

        return new JsonResponse(true);
    }
}