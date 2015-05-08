<?php

namespace Homie\Admin;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Role;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Authentication\DatabaseUserProvider;
use BrainExe\Core\Authentication\UserVO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @todo move into Core
 * @ControllerAnnotation("AdminController")
 */
class Controller
{

	/**
	 * @var DatabaseUserProvider
	 */
	private $userProvider;

	/**
     * @Inject("@DatabaseUserProvider")
     * @param DatabaseUserProvider $userProvider
     */
    public function __construct(DatabaseUserProvider $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/admin/users/", name="admin.users")
     * @Role("admin")
     */
    public function index(Request $request)
    {
        unset($request);
        $userIds = $this->userProvider->getAllUserNames();
        $users = [];

        foreach ($userIds as $id) {
            $user = $this->userProvider->loadUserById($id);
            $users[$id] = [
                'userId' => $user->id,
                'username' => $user->username,
                'roles' => $user->roles,
                'email' => $user->email,
                'hasOneTimToken' => $user->one_time_secret,
                'avatar' => $user->avatar,
            ];
        }

        return [
            'users' => $users,
            'rights' => [
                UserVO::ROLE_ADMIN,
                UserVO::ROLE_USER,
            ]
        ];
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/admin/users/edit/", name="admin.users.save")
     * @Role("admin")
     */
    public function save(Request $request)
    {
        $userId    = $request->request->getInt('userId');
        $email     = $request->request->get('email');
        $roles     = $request->request->get('roles');
        $password  = $request->request->get('password');

        $user = $this->userProvider->loadUserById($userId);

        if ($email && $email !== $user->email) {
            $user->email = $email;
            $this->userProvider->setUserProperty($user, 'email');
        }

        if ($roles && $roles !== $user->roles) {
            $user->roles = $roles;
            $this->userProvider->setUserProperty($user, 'roles');
        }

        if ($password) {
            $this->userProvider->changePassword($user, $password);
        }

        return $user;
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/admin/user/delete/", name="admin.user.delete")
     * @Role("admin")
     */
    public function delete(Request $request)
    {
        $userId = $request->request->getInt('userId');

        $this->userProvider->deleteUser($userId);

        return true;
    }
}
