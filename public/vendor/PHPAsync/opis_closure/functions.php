<?php
/* ===========================================================================
 * Copyright (c) 2018-2021 Zindex Software
 *
 * Licensed under the MIT License
 * =========================================================================== */

 
require_once realpath(ABSPATH . "public/vendor/PHPAsync/opis_closure/SerializableClosure.php");

use Opis\Closure\SerializableClosure;
/**
 * Serialize
 *
 * @param mixed $data
 * @return string
 */
function myserialize($data)
{
    SerializableClosure::enterContext();
    SerializableClosure::wrapClosures($data);
    $data = \serialize($data);
    SerializableClosure::exitContext();
    return $data;
}

/**
 * Unserialize
 *
 * @param string $data
 * @param array|null $options
 * @return mixed
 */
function myunserialize($data, array $options = null)
{
    SerializableClosure::enterContext();
    $data = ($options === null || \PHP_MAJOR_VERSION < 7)
        ? \unserialize($data)
        : \unserialize($data, $options);
    SerializableClosure::unwrapClosures($data);
    SerializableClosure::exitContext();
    return $data;
}
