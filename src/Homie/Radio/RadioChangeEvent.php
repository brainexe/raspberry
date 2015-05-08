<?php

namespace Homie\Radio;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use Homie\Radio\VO\RadioVO;

class RadioChangeEvent extends AbstractEvent
{

    const CHANGE_RADIO = 'radio.change';

    /**
     * @var RadioVO
     */
    public $radioVo;

    /**
     * @var boolean
     */
    public $status;

    /**
     * @var boolean
     */
    public $isJob;

    /**
     * @param RadioVO $radioVo
     * @param boolean $status
     */
    public function __construct(RadioVO $radioVo, $status)
    {
        $this->event_name = self::CHANGE_RADIO;
        $this->radioVo = $radioVo;
        $this->status     = $status;
    }
}
