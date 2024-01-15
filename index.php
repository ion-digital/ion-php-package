<?php

/* 
 * See license information at the package root in LICENSE.md
 */

(new class {

    private const CLASS_FILENAME = "PackagingBootStrap.php";
    private const CLASS_PATHS = [

        "source" . DIRECTORY_SEPARATOR . "classes" . DIRECTORY_SEPARATOR . "Ion" . DIRECTORY_SEPARATOR
    ];

    public static function create() {

        foreach(self::CLASS_PATHS as $classPath) {

            $path = realpath( __DIR__ . DIRECTORY_SEPARATOR . $classPath . DIRECTORY_SEPARATOR . self::CLASS_FILENAME );

            if(empty($path))
                continue;

            require_once($path);
        }

        \Ion\PackageLoader::create(__DIR__);
    }

})::create();




