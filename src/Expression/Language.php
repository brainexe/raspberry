<?php

namespace Homie\Expression;

use BrainExe\Core\Annotations\Service;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * @Service("Expression.Language")
 */
class Language extends ExpressionLanguage
{
    /**
     * @var array
     */
    public $lazyLoad = [];

    public function __construct()
    {
        parent::__construct();

        $this->registerNativeFunctions();
    }

    /**
     * @param string $functionName
     * @param callable $functions
     */
    public function lazyRegister(string $functionName, callable $functions)
    {
        $this->lazyLoad[$functionName] = $functions;

        $this->register($functionName, function (...$params) use ($functionName) {
            return $this->getFunction($functionName)['compiler'](...$params);
        }, function (...$params) use ($functionName) {
            return $this->getFunction($functionName)['evaluator'](...$params);
        });
    }

    /**
     * @param string $functionName
     * @return array
     */
    private function getFunction(string $functionName): array
    {
        if (isset($this->lazyLoad[$functionName])) {
            $functions = $this->lazyLoad[$functionName];
            foreach ($functions() as $function) {
                /** @var ExpressionFunction $function */
                unset($this->lazyLoad[$function->getName()]);
                $this->addFunction($function);
            }
        }

        return $this->functions[$functionName];
    }

    /**
     * @param string|Expression $expression
     * @param array $values
     * @return string
     */
    public function evaluate($expression, $values = array())
    {
        if (empty($expression)) {
            return '';
        }

        return parent::evaluate($expression, $values);
    }

    /**
     * @return array[]
     */
    public function getFunctions()
    {
        return $this->functions;
    }

    /**
     * @return string[]
     */
    public function getParameterNames()
    {
        return [
            'event',
            'eventName',
        ];
    }

    private function registerNativeFunctions()
    {
        $functions = [
            'sprintf',
            'date',
            'time',
            'microtime',
            'rand',
            'round',
            'sleep',
            'preg_match',
            'json_decode',
            'json_encode',
        ];

        foreach ($functions as $function) {
            $this->register($function, function (...$parameters) use ($function) {
                return sprintf(
                    '%s(%s)',
                    $function,
                    implode(', ', $parameters)
                );
            }, function (array $parameters, ...$params) use ($function) {
                unset($parameters);
                return $function(...$params);
            });
        }
    }

    public function loadAll()
    {
        foreach (array_keys($this->lazyLoad) as $functionName) {
            $this->getFunction($functionName);
        }
    }
}
