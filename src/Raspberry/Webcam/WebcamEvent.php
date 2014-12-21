<?php

namespace Raspberry\Webcam;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\PushViaWebsocketInterface;

class WebcamEvent extends AbstractEvent implements PushViaWebsocketInterface
{

    const TAKE_PHOTO = 'webcam.take_photo';
    const TOOK_PHOTO = 'webcam.took_photo';

    /**
     * @var string
     */
    public $name;

    /**
     * @param string $name
     * @param string $eventType self::*
     */
    public function __construct($name, $eventType)
    {
        $this->event_name = $eventType;
        $this->name = $name;
    }
}
