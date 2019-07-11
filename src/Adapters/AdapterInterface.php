<?php

namespace horror\ExpressionCalculatorBundle\Adapters;

use horror\ExpressionCalculatorBundle\Exception\ExpressionParsingException;

interface AdapterInterface
{
    public function __construct();

    /**
     * @param string $expression
     *
     * @throws ExpressionParsingException
     *
     * @return float
     */
    public function calc(string $expression): float;
}
