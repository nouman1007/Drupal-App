<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit500c7d32978a342bfcbfaa0e10603576
{
    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit500c7d32978a342bfcbfaa0e10603576::$classMap;

        }, null, ClassLoader::class);
    }
}
