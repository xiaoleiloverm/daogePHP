<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit0f5d0eb39830408418e02ec7fa321641
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Simplon\\Mysql\\' => 14,
            'Seld\\CliPrompt\\' => 15,
        ),
        'M' => 
        array (
            'Monolog\\' => 8,
        ),
        'L' => 
        array (
            'League\\CLImate\\' => 15,
        ),
        'D' => 
        array (
            'Desarrolla2\\Test\\Cache\\' => 23,
            'Desarrolla2\\Cache\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Simplon\\Mysql\\' => 
        array (
            0 => __DIR__ . '/..' . '/simplon/mysql/src',
        ),
        'Seld\\CliPrompt\\' => 
        array (
            0 => __DIR__ . '/..' . '/seld/cli-prompt/src',
        ),
        'Monolog\\' => 
        array (
            0 => __DIR__ . '/..' . '/monolog/monolog/src/Monolog',
        ),
        'League\\CLImate\\' => 
        array (
            0 => __DIR__ . '/..' . '/league/climate/src',
        ),
        'Desarrolla2\\Test\\Cache\\' => 
        array (
            0 => __DIR__ . '/..' . '/desarrolla2/cache/test',
        ),
        'Desarrolla2\\Cache\\' => 
        array (
            0 => __DIR__ . '/..' . '/desarrolla2/cache/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'P' => 
        array (
            'Psr\\Log\\' => 
            array (
                0 => __DIR__ . '/..' . '/psr/log',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit0f5d0eb39830408418e02ec7fa321641::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit0f5d0eb39830408418e02ec7fa321641::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit0f5d0eb39830408418e02ec7fa321641::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
