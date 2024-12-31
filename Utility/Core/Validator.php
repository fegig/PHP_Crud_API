<?php

declare(strict_types=1);

namespace Utility\Core;

use InvalidArgumentException;

class Validator
{
    public static function validate(array $data, array $rules): void
    {
        foreach ($rules as $field => $ruleString) {
            $fieldRules = explode('|', $ruleString);
            $value = $data[$field] ?? null;

            foreach ($fieldRules as $rule) {
                $ruleParts = explode(':', $rule);
                $ruleName = $ruleParts[0];
                $ruleParam = $ruleParts[1] ?? null;

                $methodName = 'validate' . ucfirst($ruleName);
                if (method_exists(self::class, $methodName)) {
                    self::$methodName($field, $value, $ruleParam);
                }
            }
        }
    }

    private static function validateRequired(string $field, $value): void
    {
        if ($value === null || $value === '') {
            throw new InvalidArgumentException("The {$field} field is required.");
        }
    }

    private static function validateString(string $field, $value): void
    {
        if ($value !== null && !is_string($value)) {
            throw new InvalidArgumentException("The {$field} must be a string.");
        }
    }

    private static function validateInteger(string $field, $value): void
    {
        if ($value !== null && !is_int($value) && !ctype_digit($value)) {
            throw new InvalidArgumentException("The {$field} must be an integer.");
        }
    }

    private static function validateBoolean(string $field, $value): void
    {
        if ($value !== null && !is_bool($value) && !in_array($value, [0, 1, '0', '1', 'true', 'false'], true)) {
            throw new InvalidArgumentException("The {$field} must be a boolean.");
        }
    }

    private static function validateEmail(string $field, $value): void
    {
        if ($value !== null && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("The {$field} must be a valid email address.");
        }
    }
    private static function validateArray(string $field, $value): void
    {
        if ($value !== null && !is_array($value)) {
            throw new InvalidArgumentException("The {$field} must be an array.");
        }
    }

}