<?php

namespace Tests\Raspberry\Controller\DashboardController;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Controller\DashboardController;
use Symfony\Component\HttpFoundation\Request;
use Raspberry\Dashboard\Dashboard;

/**
 * @Covers Raspberry\Controller\DashboardController
 */
class DashboardControllerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var DashboardController
	 */
	private $_subject;

	/**
	 * @var Dashboard|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockDashboard;

	public function setUp() {
		$this->_mockDashboard = $this->getMock(Dashboard::class, [], [], '', false);
		$this->_subject = new DashboardController($this->_mockDashboard);
	}

	public function testIndex() {
		$user_id = 0;

		$dashboard = ['dashboard'];
		$widgets   = ['widgets'];

		$this->_mockDashboard
			->expects($this->once())
			->method('getDashboard')
			->with($user_id)
			->will($this->returnValue($dashboard));

		$this->_mockDashboard
			->expects($this->once())
			->method('getAvailableWidgets')
			->will($this->returnValue($widgets));

		$request = new Request();
		$actual_result = $this->_subject->index($request);


		$expected_result = [
			'dashboard' => $dashboard,
			'widgets' => $widgets
		];

		$this->assertEquals($expected_result, $actual_result);
	}

	public function testAddWidget() {
		$type = 'type';
		$user_id   = 0;

		$payload = ['payload'];
		$dashboard = ['dashboard'];

		$request = new Request();
		$request->request->set('type', $type);
		$request->request->set('payload', json_encode($payload));

		$this->_mockDashboard
			->expects($this->once())
			->method('addWidget')
			->with($user_id, $type, $payload);

		$this->_mockDashboard
			->expects($this->once())
			->method('getDashboard')
			->with($user_id)
			->will($this->returnValue($dashboard));

		$actual_result = $this->_subject->addWidget($request);

		$this->assertEquals($dashboard, $actual_result);
	}

	public function testDeleteWidget() {
		$widget_id = 12;
		$user_id   = 0;

		$dashboard = ['dashboard'];

		$request = new Request();
		$request->request->set('widget_id', $widget_id);

		$this->_mockDashboard
			->expects($this->once())
			->method('deleteWidget')
			->with($user_id, $widget_id)
			->will($this->returnValue($dashboard));

		$this->_mockDashboard
			->expects($this->once())
			->method('getDashboard')
			->with($user_id)
			->will($this->returnValue($dashboard));

		$actual_result = $this->_subject->deleteWidget($request);

		$expected_result = $dashboard;
		$this->assertEquals($expected_result, $actual_result);
	}

}