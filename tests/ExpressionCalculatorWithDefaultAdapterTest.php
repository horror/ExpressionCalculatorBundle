<?php

namespace horror\ExpressionCalculatorBundle\Tests;

use horror\ExpressionCalculatorBundle\Adapters\DefaultAdapter;
use horror\ExpressionCalculatorBundle\Exception\ExpressionParsingException;
use horror\ExpressionCalculatorBundle\ExpressionCalculator;
use PHPUnit\Framework\TestCase;

/**
 * Class ExpressionCalculatorWithDefaultAdapterTest.
 */
class ExpressionCalculatorWithDefaultAdapterTest extends TestCase
{
    /**
     * @var ExpressionCalculator
     */
    private $calculatorWithDefaultAdapter;

    /**
     * ExpressionCalculatorWithDefaultAdapterTest constructor.
     *
     * @param null   $name
     * @param array  $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->calculatorWithDefaultAdapter = new ExpressionCalculator(new DefaultAdapter());
    }

    public function testEmptyExpressionParsingException(): void
    {
        $caughtParsingException = false;

        try {
            $this->calculatorWithDefaultAdapter->calc('');
        } catch (ExpressionParsingException $e) {
            $caughtParsingException = true;
            $this->assertEquals('Unexpected empty expression string', $e->getMessage());
        }

        $this->assertTrue($caughtParsingException);
    }

    public function testCharsInExpressionParsingException(): void
    {
        $caughtParsingException = false;

        try {
            $this->calculatorWithDefaultAdapter->calc('6adsda3');
        } catch (ExpressionParsingException $e) {
            $caughtParsingException = true;
            $this->assertEquals('Unexpected term `adsda`', $e->getMessage());
        }

        $this->assertTrue($caughtParsingException);
    }

    public function testInvalidExpression(): void
    {
        $caughtParsingException = false;

        try {
            $this->calculatorWithDefaultAdapter->calc('999++43');
        } catch (ExpressionParsingException $e) {
            $caughtParsingException = true;
            $this->assertEquals('Unexpected term `+`', $e->getMessage());
        }

        $this->assertTrue($caughtParsingException);
    }

    public function testDivisionByZero(): void
    {
        $caughtParsingException = false;

        try {
            $this->calculatorWithDefaultAdapter->calc('9/0');
        } catch (ExpressionParsingException $e) {
            $caughtParsingException = true;
            $this->assertEquals('Division by zero', $e->getMessage());
        }

        $this->assertTrue($caughtParsingException);
    }

    public function testEndByOperatorExpression(): void
    {
        $caughtParsingException = false;

        try {
            $this->calculatorWithDefaultAdapter->calc('9/5+');
        } catch (ExpressionParsingException $e) {
            $caughtParsingException = true;
            $this->assertEquals('Expression must end with digit', $e->getMessage());
        }

        $this->assertTrue($caughtParsingException);
    }

    public function testSimpleExpression(): void
    {
        $this->assertEquals(4., $this->calculatorWithDefaultAdapter->calc('2+2'));
    }

    public function testMinusFirstCharExpression(): void
    {
        $this->assertEquals(0., $this->calculatorWithDefaultAdapter->calc('-9+9'));
    }

    public function testPriorityExpression(): void
    {
        $this->assertEquals(6., $this->calculatorWithDefaultAdapter->calc('2+2*2'));
    }

    public function testAllOperatorsExpression(): void
    {
        $this->assertEquals(.6, $this->calculatorWithDefaultAdapter->calc('1+2-3*4/5'));
    }

    public function testAllOperatorsWithWhiteSpacesExpression(): void
    {
        $this->assertEquals(.6, $this->calculatorWithDefaultAdapter->calc('1    + 2-   3*4/ 5'));
    }

    public function testOneDigitExpression(): void
    {
        $this->assertEquals(1., $this->calculatorWithDefaultAdapter->calc('1'));
    }

    public function testOneDigitExpression2(): void
    {
        $this->assertEquals(-1.2, $this->calculatorWithDefaultAdapter->calc('-1.2'));
    }

    public function testAllOperatorsFloatDigitExpression(): void
    {
        $this->assertEquals(1.1823529411764704, $this->calculatorWithDefaultAdapter->calc('1.5+2.4-3.3*4.2/5.1'));
    }
}
