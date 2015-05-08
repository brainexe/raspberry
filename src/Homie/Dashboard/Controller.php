<?php

namespace Homie\Dashboard;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Controller\ControllerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("DashboardController")
 */
class Controller implements ControllerInterface
{

    /**
     * @var Dashboard
     */
    private $dashboard;

    /**
     * @Inject("@Dashboard")
     * @param Dashboard $dashboard
     */
    public function __construct(Dashboard $dashboard)
    {
        $this->dashboard = $dashboard;
    }

    /**
     * @return array
     * @Route("/dashboard/", name="dashboard.index")
     */
    public function index()
    {
        $dashboards = $this->dashboard->getDashboards();
        $widgets = $this->dashboard->getAvailableWidgets();

        return [
            'dashboards' => $dashboards,
            'widgets'   => $widgets
        ];
    }

    /**
     * @param Request $request
     * @return DashboardVo
     * @Route("/dashboard/add/", methods="POST")
     */
    public function addWidget(Request $request)
    {
        $type        = $request->request->get('type');
        $payload     = (array)$request->request->get('payload');
        $dashboardId = $request->request->get('dashboard_id');

        return $this->dashboard->addWidget($dashboardId, $type, $payload);
    }
    /**
     * @param Request $request
     * @return array
     * @Route("/dashboard/update/", methods="POST")
     */
    public function updateDashboard(Request $request)
    {
        $dashboardId = $request->request->get('dashboard_id');
        $payload     = (array)$request->request->get('payload');

        return $this->dashboard->updateDashboard($dashboardId, $payload);
    }

    /**
     * @param Request $request
     * @return array
     * @Route("/dashboard/widget/update/", methods="POST")
     */
    public function updateWidget(Request $request)
    {
        $dashboardId = $request->request->get('dashboard_id');
        $widgetId    = $request->request->get('widget_id');
        $payload     = (array)$request->request->get('payload');

        $this->dashboard->updateWidget($dashboardId, $widgetId, $payload);

        return $this->dashboard->getDashboard($dashboardId);
    }

    /**
     * @param Request $request
     * @return array
     * @Route("/dashboard/widget/delete/", methods="POST")
     */
    public function deleteWidget(Request $request)
    {
        $widgetId    = $request->request->getInt('widget_id');
        $dashboardId = $request->request->getInt('dashboard_id');

        $this->dashboard->deleteWidget($dashboardId, $widgetId);

        $dashboard = $this->dashboard->getDashboard($dashboardId);

        return $dashboard;
    }
    /**
     * @param Request $request
     * @return bool
     * @Route("/dashboard/delete/", methods="POST")
     */
    public function deleteDashboard(Request $request)
    {
        $dashboardId = $request->request->getInt('dashboard_id');

        $this->dashboard->delete($dashboardId);

        return true;
    }
}
