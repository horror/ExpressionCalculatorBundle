<?php

namespace horror\ExpressionCalculatorBundle\Adapters;

use horror\ExpressionCalculatorBundle\Exception\ExpressionParsingException;

/**
 * Class DefaultAdapter.
 */
class DefaultAdapter implements AdapterInterface
{
    private const UNEXPECTED_CHARS_PATTERN = '/[^\d\.\+\-\/\*]+/';
    private const TERM_PATTERN = '/(?<terms>[\+\-\/\*]|\d+(?:\.\d+|))/';
    private const WHITESPACES_PATTERN = '/\s+/';

    private const PRIORITY_OPERATORS = ['*', '/'];

    /**
     * @var callable[]
     */
    private $operatorToCallback;

    /**
     * DefaultAdapter constructor.
     */
    public function __construct()
    {
        $this->operatorToCallback = [
            '+' => function ($left, $right) {
                return $left + $right;
            },
            '-' => function ($left, $right) {
                return $left - $right;
            },
            '*' => function ($left, $right) {
                return $left * $right;
            },
            '/' => function ($left, $right) {
                if (0.0 === $right) {
                    throw new ExpressionParsingException('Division by zero');
                }

                return $left / $right;
            },
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function calc(string $expression): float
    {
        $terms = $this->parse($expression);

        return $this->evaluate($terms);
    }

    /**
     * @param array $terms
     *
     * @return float
     */
    private function evaluate(array $terms): float
    {
        $termsWithComputedPrioritized = [];
        $skipNextTerm = false;

        // compute priority operators
        foreach ($terms as $idx => $term) {
            if ($skipNextTerm) {
                $skipNextTerm = false;

                continue;
            }

            if (in_array($term, self::PRIORITY_OPERATORS, true)) {
                $callback = $this->operatorToCallback[$term];

                $lastComputedIdx = count($termsWithComputedPrioritized) - 1;
                $termsWithComputedPrioritized[$lastComputedIdx] = $callback(
                    end($termsWithComputedPrioritized), $terms[$idx + 1]
                );

                $skipNextTerm = true;
                continue;
            }

            $termsWithComputedPrioritized[] = $term;
        }

        // compute simple operators
        $terms = $termsWithComputedPrioritized;
        $result = 0;
        foreach ($terms as $idx => $term) {
            if (0 === $idx && $this->validateDigit($term)) {
                $result = $term;
            }

            if (!$this->validateOperator($term)) {
                continue;
            }

            $callback = $this->operatorToCallback[$term];
            $result = $callback($result, $terms[$idx + 1]);
        }

        return $result;
    }

    /**
     * @param string $expression
     *
     * @return array
     */
    private function parse(string $expression): array
    {
        if ('' === $expression) {
            throw new ExpressionParsingException('Unexpected empty expression string');
        }

        $expression = preg_replace(self::WHITESPACES_PATTERN, '', $expression);

        if (false !== preg_match(self::UNEXPECTED_CHARS_PATTERN, $expression, $matches) && count($matches) > 0) {
            throw new ExpressionParsingException("Unexpected term `$matches[0]`");
        }

        if (
            false === preg_match_all(self::TERM_PATTERN, $expression, $matches) ||
            !is_array($matches['terms'] ?? null) ||
            0 === count($terms = $matches['terms'])
        ) {
            throw new ExpressionParsingException('Unknown parsing error');
        }

        $prevIsDigit = null;
        foreach ($terms as $idx => $term) {
            if ($this->validateDigit($term)) {
                if (true === $prevIsDigit) {
                    throw new ExpressionParsingException("Unexpected term `$term`");
                }
                $terms[$idx] = (float) $term;
                $prevIsDigit = true;

                continue;
            }

            if ($this->validateOperator($term)) {
                if (false === $prevIsDigit) {
                    throw new ExpressionParsingException("Unexpected term `$term`");
                }
                $prevIsDigit = false;

                continue;
            }

            throw new ExpressionParsingException("Unexpected term `$term`");
        }

        if (!$prevIsDigit) {
            throw new ExpressionParsingException('Expression must end with digit');
        }

        return $terms;
    }

    /**
     * @param string $digit
     *
     * @return bool
     */
    private function validateDigit(string $digit): bool
    {
        return is_numeric($digit);
    }

    /**
     * @param string $operator
     *
     * @return bool
     */
    private function validateOperator(string $operator): bool
    {
        return isset($this->operatorToCallback[$operator]);
    }
}
