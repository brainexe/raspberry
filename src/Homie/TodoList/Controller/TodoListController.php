<?php

namespace Homie\TodoList\Controller;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Authentication\DatabaseUserProvider;
use Homie\TodoList\TodoList;
use Homie\TodoList\VO\TodoItemVO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @todo repeatable tasks
 * @Controller("ToDoListController")
 */
class TodoListController
{

    /**
     * @var TodoList
     */
    private $todo;

    /**
     * @var DatabaseUserProvider
     */
    private $userProvider;

    /**
     * @Inject({"@TodoList", "@DatabaseUserProvider"})
     * @param TodoList $todo
     * @param DatabaseUserProvider $userProvider
     */
    public function __construct(
        TodoList $todo,
        DatabaseUserProvider $userProvider
    ) {
        $this->todo         = $todo;
        $this->userProvider = $userProvider;
    }

    /**
     * @Route("/todo/", name="todo.index", methods="GET")
     * @return array
     */
    public function index()
    {
        $list = $this->todo->getList();
        $states = [
            TodoItemVO::STATUS_PENDING => ['name' => _('Pending'), 'next' => [TodoItemVO::STATUS_OPEN, 'delete']],
            TodoItemVO::STATUS_OPEN => ['name' => _('Open'), 'next' => [TodoItemVO::STATUS_PROGRESS, TodoItemVO::STATUS_COMPLETED, 'delete']],
            TodoItemVO::STATUS_PROGRESS => ['name' => _('In Progress'), 'next' => [TodoItemVO::STATUS_COMPLETED, 'delete']],
            TodoItemVO::STATUS_COMPLETED => ['name' => _('Completed'), 'next' => ['delete']],
        ];

        return [
            'list'   => $list,
            'states' => $states
        ];
    }

    /**
     * @param Request $request
     * @Route("/todo/", name="todo.add", methods="POST")
     * @return JsonResponse
     */
    public function addItem(Request $request)
    {
        $itemVo                 = new TodoItemVO();
        $itemVo->name           = $request->request->get('name');
        $itemVo->description    = $request->request->get('description');
        $itemVo->status         = $request->request->get('status');
        $itemVo->deadline       = strtotime($request->request->get('deadline'));
        $itemVo->cronExpression = $request->request->get('cronExpression');

        $user = $request->attributes->get('user');

        $this->todo->addItem($user, $itemVo);

        return new JsonResponse($itemVo);
    }

    /**
     * @param Request $request
     * @Route("/todo/", name="todo.edit", methods="PUT")
     * @return JsonResponse
     */
    public function setItemStatus(Request $request)
    {
        $itemId  = $request->request->getInt('id');
        $changes = $request->request->get('changes');

        $itemVo = $this->todo->editItem($itemId, $changes);

        return new JsonResponse($itemVo);
    }

    /**
     * @param Request $request
     * @Route("/todo/assign/", name="todo.assign", methods="POST")
     * @return JsonResponse
     */
    public function setAssignee(Request $request)
    {
        $itemId = $request->request->getInt('id');
        $userId = $request->request->getInt('userId');

        $user = $this->userProvider->loadUserById($userId);

        $itemVo = $this->todo->editItem($itemId, [
            'userId'   => $userId,
            'userName' => $user->username,
        ]);
        return new JsonResponse($itemVo);
    }

    /**
     * @param Request $request
     * @param int $itemId
     * @Route("/todo/{itemId}/", name="todo.delete")
     * @return JsonResponse
     */
    public function deleteItem(Request $request, $itemId)
    {
        unset($request);

        $this->todo->deleteItem($itemId);

        return new JsonResponse(true);
    }
}
