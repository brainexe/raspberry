<?php

namespace Homie\Radio;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\EventListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener
 */
class RadioJobListener implements EventSubscriberInterface
{

    /**
     * @var RadioController
     */
    private $controller;

    /**
     * @param RadioController $radioController
     * @Inject("@RadioController")
     */
    public function __construct(RadioController $radioController)
    {
        $this->controller = $radioController;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            RadioChangeEvent::CHANGE_RADIO => 'handleChangeEvent'
        ];
    }

    /**
     * @param RadioChangeEvent $event
     */
    public function handleChangeEvent(RadioChangeEvent $event)
    {
        $this->controller->setStatus(
            $event->radioVo,
            $event->status
        );
    }
}
