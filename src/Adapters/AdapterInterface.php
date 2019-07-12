<?php

namespace horror\ExpressionCalculatorBundle\Adapters;

use horror\ExpressionCalculatorBundle\Exception\ExpressionParsingException;

/**
 * Interface AdapterInterface.
 */
interface AdapterInterface
{
    /**
     * AdapterInterface constructor.
     */
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
