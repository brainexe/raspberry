<?php

namespace Homie\Radio;

use Homie\Radio\VO\SwitchVO;

interface SwitchInterface
{

    /**
     * @param SwitchVO $radioVO
     * @param int $status
     */
    public function setStatus(SwitchVO $radioVO, $status);
}
