<?php


namespace Spencer\Traits;


use Spencer\Application;

trait Bindable
{
    public static function register()
    {
        Application::instance()->bind(self::ALIAS, self::class);
    }
}