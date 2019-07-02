<?php


namespace Spencer\Traits;


use Spencer\Application;

trait SingletonBindable
{
    public static function register()
    {
        Application::instance()->singleton(self::ALIAS, self::class);
    }
}