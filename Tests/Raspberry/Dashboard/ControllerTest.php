<?php

namespace Tests\Raspberry\Dashboard;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Dashboard\Controller;
use Symfony\Component\HttpFoundation\Request;
use Raspberry\Dashboard\Dashboard;

/**
 * @Covers Raspberry\Dashboard\Controller
 */
class ControllerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Controller
     */
    private $subject;

    /**
     * @var Dashboard|MockObject
     */
    private $dashboard;

    public function setUp()
    {
        $this->dashboard = $this->getMock(Dashboard::class, [], [], '', false);
        $this->subject   = new Controller($this->dashboard);
    }

    public function testIndex()
    {
        $dashboards = ['dashboards'];
        $widgets    = ['widgets'];

        $this->dashboard
            ->expects($this->once())
            ->method('getDashboards')
            ->willReturn($dashboards);

        $this->dashboard
            ->expects($this->once())
            ->method('getAvailableWidgets')
            ->willReturn($widgets);

        $request = new Request();
        $actualResult = $this->subject->index($request);

        $expectedResult = [
            'dashboards' => $dashboards,
            'widgets'   => $widgets
        ];

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testAddWidget()
    {
        $type          = 'type';
        $dashboardId   = 0;

        $payload   = ['payload'];
        $dashboard = ['dashboard'];

        $request = new Request();
        $request->request->set('type', $type);
        $request->request->set('payload', $payload);

        $this->dashboard
            ->expects($this->once())
            ->method('addWidget')
            ->with($dashboardId, $type, $payload);

        $this->dashboard
            ->expects($this->once())
            ->method('getDashboard')
            ->with($dashboardId)
            ->willReturn($dashboard);

        $actualResult = $this->subject->addWidget($request);

        $this->assertEquals($dashboard, $actualResult);
    }

    public function testUpdateDashboard()
    {
        $dashboardId   = 1212;
        $name          = 'name';

        $dashboard = 'dashboard';

        $request = new Request();
        $request->request->set('dashboard_id', $dashboardId);
        $request->request->set('name', $name);

        $this->dashboard
            ->expects($this->once())
            ->method('updateDashboard')
            ->with($dashboardId, $name)
            ->willReturn($dashboard);

        $actualResult = $this->subject->updateDashboard($request);

        $this->assertEquals($dashboard, $actualResult);
    }

    public function testDeleteWidget()
    {
        $widgetId    = 12;
        $dashboardId = 0;

        $dashboard = ['dashboard'];

        $request = new Request();
        $request->request->set('widget_id', $widgetId);

        $this->dashboard
            ->expects($this->once())
            ->method('deleteWidget')
            ->with($dashboardId, $widgetId)
            ->willReturn($dashboard);

        $this->dashboard
            ->expects($this->once())
            ->method('getDashboard')
            ->with($dashboardId)
            ->willReturn($dashboard);

        $actualResult = $this->subject->deleteWidget($request);

        $expectedResult = $dashboard;
        $this->assertEquals($expectedResult, $actualResult);
    }
}