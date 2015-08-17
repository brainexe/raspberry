<?php

namespace Homie\Display;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Homie\Display\Event\Redraw;
use Symfony\Component\HttpFoundation\Request;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;

/**
 * @ControllerAnnotation("DisplayController")
 */
class Controller
{

    use EventDispatcherTrait;

    /**
     * @var Gateway
     */
    private $gateway;

    /**
     * @var Renderer
     */
    private $renderer;

    /**
     * @Inject({"@Display.Gateway", "@Display.Renderer"})
     * @param Gateway $gateway
     * @param Renderer $renderer
     */
    public function __construct(Gateway $gateway, Renderer $renderer)
    {
        $this->gateway  = $gateway;
        $this->renderer = $renderer;
    }

    /**
     * @Route("/display/", name="display.index", methods="GET")
     * @return array
     */
    public function index()
    {
        $screens = $this->gateway->getall();

        return [
            'screens' => iterator_to_array($screens)
        ];
    }

    /**
     * @Route("/display/", name="display.add", methods="POST")
     * @param Request $request
     * @return Settings
     */
    public function add(Request $request)
    {
        $settings = new Settings();
        $settings->lines    = $request->request->getInt('lines');
        $settings->columns  = $request->request->getInt('columns');
        $settings->content  = (array)$request->request->get('content');
        $settings->rendered = $this->renderer->render($settings);

        $this->gateway->addDisplay($settings);

        return $settings;
    }

    /**
     * @Route("/display/{displayId}/", name="display.delete", methods="DELETE")
     * @param Request $request
     * @param int $displayId
     * @return bool
     */
    public function delete(Request $request, $displayId)
    {
        return true;
    }

    /**
     * @Route("/display/{displayId}", name="display.update", methods="PUT")
     * @param Request $request
     * @param int $displayId
     * @return Settings
     */
    public function update(Request $request, $displayId)
    {
    }

    /**
     * @Route("/display/{displayId}/redraw/", name="display.redraw", methods="POST")
     * @param Request $request
     * @return bool
     */
    public function redraw(Request $request, $displayId)
    {
        $event = new Redraw();
        $this->dispatchInBackground($event);

        return true;
    }
}
