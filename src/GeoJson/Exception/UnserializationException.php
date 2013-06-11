<?php

namespace GeoJson\Exception;

class UnserializationException extends \RuntimeException implements Exception
{
    /**
     * Creates an UnserializationException for a value with an invalid type.
     *
     * @param string $context
     * @param mixed $value
     * @param string $expectedType
     * @return UnserializationException
     */
    public static function invalidValue($context, $value, $expectedType)
    {
        return new self(sprintf(
            '%s expected value of type %s, %s given',
            $context,
            $expectedType,
            is_object($value) ? get_class($value) : gettype($value)
        ));
    }

    /**
     * Creates an UnserializationException for a property with an invalid type.
     *
     * @param string $context
     * @param string $property
     * @param mixed $value
     * @param string $expectedType
     * @return UnserializationException
     */
    public static function invalidProperty($context, $property, $value, $expectedType)
    {
        return new self(sprintf(
            '%s expected "%s" property of type %s, %s given',
            $context,
            $property,
            $expectedType,
            is_object($value) ? get_class($value) : gettype($value)
        ));
    }

    /**
     * Creates an UnserializationException for a missing property.
     *
     * @param string $context
     * @param string $property
     * @param string $expectedType
     * @return UnserializationException
     */
    public static function missingProperty($context, $property, $expectedType)
    {
        return new self(sprintf(
            '%s expected "%s" property of type %s, none given',
            $context,
            $property,
            $expectedType
        ));
    }

    /**
     * Creates an UnserializationException for an unsupported "type" property.
     *
     * @param string $context
     * @param string $value
     * @return UnserializationException
     */
    public static function unsupportedType($context, $value)
    {
        return new self(sprintf('Invalid %s type "%s"', $context, $value));
    }
}
