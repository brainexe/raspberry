<?php

namespace Tests\Raspberry\Controller\IndexController;

use BrainExe\Core\Authentication\UserVO;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Controller\IndexController;
use Symfony\Component\HttpFoundation\Request;
use BrainExe\Template\TwigEnvironment;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Covers Raspberry\Controller\IndexController
 */
class IndexControllerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var IndexController
     */
    private $subject;

    /**
     * @var TwigEnvironment|MockObject
     */
    private $mockTwigEnvironment;

    public function setUp()
    {
        $this->mockTwigEnvironment = $this->getMock(TwigEnvironment::class, [], [], '', false);

        $this->subject = new IndexController();
        $this->subject->setTwig($this->mockTwigEnvironment);
    }

    public function testIndex()
    {
        $user = new UserVO();
        $text = 'text';

        $request = new Request();
        $request->attributes->set('user', $user);

        $this->mockTwigEnvironment
            ->expects($this->once())
            ->method('render')
            ->with('layout.html.twig', [
                'current_user' => $user
            ])
            ->willReturn($text);

        $actualResult = $this->subject->index($request);

        $expectedResult = new Response($text);

        $this->assertEquals($expectedResult, $actualResult);
    }
}
