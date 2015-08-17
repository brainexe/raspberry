<?php

namespace Homie\Help;

use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Guest;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Traits\RedisTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("HelpController")
 */
class Controller
{

    use RedisTrait;

    const KEY = 'help';

    /**
     * @return string[]
     * @Route("/help/", name="help.all", methods="GET")
     * @Guest
     */
    public function all()
    {
        return $this->getRedis()->hgetall(self::KEY);
    }

    /**
     * @param Request $request
     * @param string $type
     * @Route("/help/{type}/", name="help.save", methods="POST")
     * @return true
     * @Guest
     */
    public function save(Request $request, $type)
    {
        $content = (string)$request->request->get('content');
        $this->getRedis()->hset(self::KEY, $type, $content);

        return true;
    }
    /**
     * @param Request $request
     * @return bool
     * @param string $type
     * @Route("/help/{type}/", name="help.delete", methods="DELETE")
     * @Guest
     */
    public function delete(Request $request, $type)
    {
        unset($request);

        $this->getRedis()->hdel(self::KEY, $type);

        return true;
    }
}
