<?php

namespace horror\ExpressionCalculatorBundle;

use horror\ExpressionCalculatorBundle\Adapters\AdapterInterface;
use horror\ExpressionCalculatorBundle\Exception\ExpressionParsingException;

/**
 * Class ExpressionCalculator.
 */
class ExpressionCalculator
{
    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * ExpressionCalculator constructor.
     *
     * @param AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param string $expression
     *
     * @throws ExpressionParsingException
     *
     * @return float
     */
    public function calc(string $expression): float
    {
        return $this->adapter->calc($expression);
    }
}
