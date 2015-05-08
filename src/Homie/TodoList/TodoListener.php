<?php

namespace Homie\TodoList;

use BrainExe\Core\Annotations\EventListener;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Homie\Espeak\EspeakEvent;
use Homie\Espeak\EspeakVO;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener
 */
class TodoListener implements EventSubscriberInterface
{

    use EventDispatcherTrait;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
        TodoListEvent::ADD => 'handleAddEvent'
        ];
    }

    /**
     * @param TodoListEvent $event
     */
    public function handleAddEvent(TodoListEvent $event)
    {
        if ($event->itemVo->deadline) {
            $espeakVo = new EspeakVO(sprintf('Erinnerung: %s', $event->itemVo->name));
            $espeakEvent = new EspeakEvent($espeakVo);
            $this->dispatchInBackground($espeakEvent, $event->itemVo->deadline);
        }
    }
}
