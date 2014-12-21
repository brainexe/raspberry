<?php

namespace Tests\Raspberry\Controller\DashboardController;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Controller\DashboardController;
use Symfony\Component\HttpFoundation\Request;
use Raspberry\Dashboard\Dashboard;

/**
 * @Covers Raspberry\Controller\DashboardController
 */
class DashboardControllerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var DashboardController
     */
    private $subject;

    /**
     * @var Dashboard|MockObject
     */
    private $mockDashboard;

    public function setUp()
    {
        $this->mockDashboard = $this->getMock(Dashboard::class, [], [], '', false);
        $this->subject = new DashboardController($this->mockDashboard);
    }

    public function testIndex()
    {
        $userId = 0;

        $dashboard = ['dashboard'];
        $widgets   = ['widgets'];

        $this->mockDashboard
        ->expects($this->once())
        ->method('getDashboard')
        ->with($userId)
        ->will($this->returnValue($dashboard));

        $this->mockDashboard
        ->expects($this->once())
        ->method('getAvailableWidgets')
        ->will($this->returnValue($widgets));

        $request = new Request();
        $actualResult = $this->subject->index($request);

        $expectedResult = [
        'dashboard' => $dashboard,
        'widgets'   => $widgets
        ];

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testAddWidget()
    {
        $type = 'type';
        $userId   = 0;

        $payload   = ['payload'];
        $dashboard = ['dashboard'];

        $request = new Request();
        $request->request->set('type', $type);
        $request->request->set('payload', $payload);

        $this->mockDashboard
        ->expects($this->once())
        ->method('addWidget')
        ->with($userId, $type, $payload);

        $this->mockDashboard
        ->expects($this->once())
        ->method('getDashboard')
        ->with($userId)
        ->will($this->returnValue($dashboard));

        $actualResult = $this->subject->addWidget($request);

        $this->assertEquals($dashboard, $actualResult);
    }

    public function testDeleteWidget()
    {
        $widget_id = 12;
        $userId   = 0;

        $dashboard = ['dashboard'];

        $request = new Request();
        $request->request->set('widget_id', $widget_id);

        $this->mockDashboard
        ->expects($this->once())
        ->method('deleteWidget')
        ->with($userId, $widget_id)
        ->will($this->returnValue($dashboard));

        $this->mockDashboard
        ->expects($this->once())
        ->method('getDashboard')
        ->with($userId)
        ->will($this->returnValue($dashboard));

        $actualResult = $this->subject->deleteWidget($request);

        $expectedResult = $dashboard;
        $this->assertEquals($expectedResult, $actualResult);
    }
}
