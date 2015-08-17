<?php

namespace Tests\Homie\Display;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Redis\Predis;
use BrainExe\Core\Util\IdGenerator;
use BrainExe\Tests\RedisMockTrait;
use Homie\Display\Controller;
use Homie\Display\Event\Redraw;
use Homie\Display\Gateway;
use Homie\Display\Renderer;
use Homie\Display\Settings;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\HttpFoundation\Request;

class ControllerTest extends TestCase
{

    /**
     * @var Controller
     */
    private $subject;

    /**
     * @var Renderer|MockObject
     */
    private $renderer;

    /**
     * @var Gateway|MockObject
     */
    private $gateway;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->gateway    = $this->getMock(Gateway::class, [], [], '', false);
        $this->renderer   = $this->getMock(Renderer::class, [], [], '', false);
        $this->dispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
        $this->subject    = new Controller($this->gateway, $this->renderer);
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testAddDisplay()
    {
        $request = new Request();
        $request->request->set('lines', $lines = 4);
        $request->request->set('columns', $columns = 5);
        $request->request->set('content', $content = ['content']);

        $rendered = ['rendered'];

        $settings = new Settings();
        $settings->lines    = $lines;
        $settings->columns  = $columns;
        $settings->content  = $content;
        $settings->rendered = $rendered;

        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->willReturn($rendered);

        $this->gateway
            ->expects($this->once())
            ->method('addDisplay')
            ->with($settings);

        $actual = $this->subject->add($request);

        $this->assertEquals($settings, $actual);
    }

    public function testRedraw()
    {

        $displayId = 11880;

        $request = new Request();

        $event = new Redraw();

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event);

        $actual = $this->subject->redraw($request, $displayId);

        $this->assertTrue($actual);
    }
}
