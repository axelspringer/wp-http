<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd021828b391b2ad6981f8a1d416bbda4
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'Asse\\' => 5,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Asse\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'D' => 
        array (
            'Detection' => 
            array (
                0 => __DIR__ . '/..' . '/mobiledetect/mobiledetectlib/namespaced',
            ),
        ),
    );

    public static $classMap = array (
        'Mobile_Detect' => __DIR__ . '/..' . '/mobiledetect/mobiledetectlib/Mobile_Detect.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitd021828b391b2ad6981f8a1d416bbda4::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitd021828b391b2ad6981f8a1d416bbda4::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitd021828b391b2ad6981f8a1d416bbda4::$prefixesPsr0;
            $loader->classMap = ComposerStaticInitd021828b391b2ad6981f8a1d416bbda4::$classMap;

        }, null, ClassLoader::class);
    }
}
