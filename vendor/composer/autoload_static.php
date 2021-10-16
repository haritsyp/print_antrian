<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit2d5dfd026e7cc11df116e450aaf8fb6a
{
    public static $prefixLengthsPsr4 = array (
        '\\' => 
        array (
            '\\' => 1,
        ),
        'M' => 
        array (
            'Mike42\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        '\\' => 
        array (
            0 => __DIR__ . '/../..' . '/',
        ),
        'Mike42\\' => 
        array (
            0 => __DIR__ . '/..' . '/mike42/escpos-php/src/Mike42',
            1 => __DIR__ . '/..' . '/mike42/gfx-php/src/Mike42',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit2d5dfd026e7cc11df116e450aaf8fb6a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit2d5dfd026e7cc11df116e450aaf8fb6a::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
