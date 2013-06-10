<?php

/**
 * JsonSerializable
 *
 * Provides forward compatibility with PHP 5.4
 *
 * @see http://php.net/manual/en/class.jsonserializable.php
 */
interface JsonSerializable
{
    /**
     * Specify data which should be serialized to JSON.
     *
     * @return mixed
     */
    public function jsonSerialize();
}
