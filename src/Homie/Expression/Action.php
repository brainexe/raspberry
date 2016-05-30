<?php

namespace Homie\Expression;

use Exception;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;

class Action extends ExpressionFunction
{
    /**
     * @param string $name
     * @param callable $evaluator
     */
    public function __construct(string $name, callable $evaluator)
    {
        $compiler = function () use ($name) {
            throw new Exception(sprintf('Function "%s" is not allowed as trigger', $name));
        };

        parent::__construct($name, $compiler, $evaluator);
    }
}