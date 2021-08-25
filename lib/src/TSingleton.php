<?php


namespace Lib;


trait TSingleton
{
    private static $instance;

    /**
     * @return static
     */
    public static function instance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }
}