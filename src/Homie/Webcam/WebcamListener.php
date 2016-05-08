<?php

namespace Homie\Webcam;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\EventListener;
use BrainExe\Core\Annotations\Listen;

/**
 * @EventListener("Webcam.WebcamListener")
 */
class WebcamListener
{

    /**
     * @var Recorder
     */
    private $recorder;

    /**
     * @Inject("@Webcam.Recorder")
     * @param Recorder $recorder
     */
    public function __construct(Recorder $recorder)
    {
        $this->recorder = $recorder;
    }

    /**
     * @Listen(WebcamEvent::TAKE_PHOTO)
     * @param WebcamEvent $event
     */
    public function handlePictureEvent(WebcamEvent $event)
    {
        $this->recorder->takePhoto($event->getName());
    }

    /**
     * @Listen(WebcamEvent::TAKE_VIDEO)
     * @param WebcamEvent $event
     */
    public function handleVideoEvent(WebcamEvent $event)
    {
        $this->recorder->takeVideo($event->getName(), $event->getDuration());
    }

    /**
     * @Listen(WebcamEvent::TAKE_SOUND)
     * @param WebcamEvent $event
     */
    public function handleSoundEvent(WebcamEvent $event)
    {
        $this->recorder->takeSound($event->getName(), $event->getDuration());
    }
}
