<?php

namespace Homie\Dashboard\Widgets;

use BrainExe\Annotations\Annotations\Service;
use Homie\Dashboard\AbstractWidget;

/**
 * @Service(public=false, tags={{"name" = "widget"}})
 */
class StatusWidget extends AbstractWidget
{
    const TYPE = 'status';

    /**
     * @return WidgetMetadataVo
     */
    public function getMetadata()
    {
        return new WidgetMetadataVo(
            $this->getId(),
            gettext('Status'),
            gettext('Show internal information')
        );
    }
}