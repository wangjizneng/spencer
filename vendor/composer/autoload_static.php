<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit98448026099d0a9bbf80574d76471cb9
{
    public static $files = array (
        'dedf52511767277b170683721a35658e' => __DIR__ . '/../..' . '/spencer/helpers.php',
        '70c2cc8e471afe501c2cdffcff4501b1' => __DIR__ . '/../..' . '/common/function.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Spencer\\' => 8,
        ),
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Spencer\\' => 
        array (
            0 => __DIR__ . '/../..' . '/spencer/lib',
        ),
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit98448026099d0a9bbf80574d76471cb9::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit98448026099d0a9bbf80574d76471cb9::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
