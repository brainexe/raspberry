<?php

namespace Homie\IFTTT;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Homie\IFTTT\Event\TriggerEvent;
use InvalidArgumentException;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @Service("IFTTT.ExpressionLanguage", tags={{"name"="expression_language"}}, public=false)
 */
class ExpressionLanguage implements ExpressionFunctionProviderInterface
{

    use EventDispatcherTrait;

    /**
     * @return ExpressionFunction[]
     */
    public function getFunctions()
    {
        $trigger = new ExpressionFunction('triggerIFTTT', function () {
            throw new InvalidArgumentException('triggerIFTTT() is not available in this context');
        }, function (array $variables, $eventName, $value1 = null, $value2 = null, $value3 = null) {
            unset($variables);
            $event = new TriggerEvent($eventName, $value1, $value2, $value3);

            $this->dispatchEvent($event);
        });

        return [$trigger];
    }
}