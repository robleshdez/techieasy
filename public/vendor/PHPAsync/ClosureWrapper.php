<?php

require_once realpath(__DIR__.'/../../../core/Load.php');

require_once realpath(ABSPATH . "public/vendor/PHPAsync/opis_closure/SerializableClosure.php");


use Opis\Closure\SerializableClosure;

class ClosureWrapper
{
    public static function wrap(Closure $closure)
    {
        $wrapped = new SerializableClosure($closure);
        return $wrapped;
    }

    public static function unwrap($serializedClosure)
    {   
        if (!$serializedClosure instanceof SerializableClosure) {
        throw new \InvalidArgumentException('El objeto proporcionado no es una instancia de SerializableClosure.');
        }
        $closure = $serializedClosure->getClosure();
        return $closure;
    }

    public static function serialize($closure)
    {
        $errorReporting = error_reporting();
        error_reporting(0);

        
        try {
                $serialized = serialize(self::wrap($closure));
            } catch (\Throwable $e) {
                error_reporting($errorReporting);
                throw $e;
            }               

        error_reporting($errorReporting);

        return $serialized;
    }

    public static function unserialize($serialized)
    {
        $errorReporting = error_reporting();
        error_reporting(0);

        $unserialized = self::unwrap(unserialize($serialized));

        error_reporting($errorReporting);

        return $unserialized;
    }
}