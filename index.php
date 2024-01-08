<?php

/* 
 * See license information at the package root in LICENSE.md
 */

(new class {

    private const VENDOR = "ion";
    private const PROJECT = "packaging";
    private const CLASS_NAME = "\\ion\\Package";
    private const INTERFACE_SUFFIX = "Interface";
    private const BUILDS_DIR = "builds";
    private const SOURCE_DIR = "source";
    private const SOURCE_CLASSES_DIR = "classes";
    private const SOURCE_INTERFACES_DIR = "interfaces";
    private const BACKWARD_SLASH = "\\";
    private const PHP_VERSION_SEPARATOR = ".";
    private const PHP_FILE_EXTENSION = ".php";
    
    private const DEFINITIONS = [

        "ion\\Package" => [ "ion\\PackageInterface" ],
        "ion\\PackageException" => [ "ion\\PackageExceptionInterface" ],
        "ion\\SemVer" => [ "ion\\SemVerInterface" ],

        "ion\\Settings\\Settings" => [ "ion\\Settings\\SettingsInterface" ],
        "ion\\Settings\\SettingsException" => [ "ion\\Settings\\SettingsExceptionInterface" ],        
        "ion\\Settings\\SettingsProvider" => [ "ion\\Settings\\SettingsProviderInterface" ],
        "ion\\Settings\\SettingsProviderException" => [ "ion\\Settings\\SettingsProviderExceptionInterface" ],              
        "ion\\Settings\\Providers\\ArraySettingsProvider" => [ "ion\\Settings\\Providers\\ArraySettingsProviderInterface" ],              
        "ion\\Settings\\Providers\\JsonFileSettingsProvider" => [ "ion\\Settings\\Providers\\JsonFileSettingsProviderInterface" ],
    ];

    public static function create(): void {

        if(!self::load()) {

            return;
        }

        (self::CLASS_NAME)::create(
            
            self::VENDOR, 
            self::PROJECT,             
        
            function() {
        
                if(!self::load()) {

                    return;
                }

                return;
            }, 

            __FILE__,
            true
        );

        
        return;
    }

    private static function load(): bool {

        if(class_exists(self::CLASS_NAME)) {

            return false;
        }

        $dirs = [];

        if(is_dir(self::SOURCE_DIR)) {

            $dirs[] = self::SOURCE_DIR . DIRECTORY_SEPARATOR . self::SOURCE_CLASSES_DIR . DIRECTORY_SEPARATOR;
            $dirs[] = self::SOURCE_DIR . DIRECTORY_SEPARATOR . self::SOURCE_INTERFACES_DIR . DIRECTORY_SEPARATOR;
        }
        else {

            $dirs[] = self::BUILDS_DIR . DIRECTORY_SEPARATOR . PHP_MAJOR_VERSION . self::PHP_VERSION_SEPARATOR . PHP_MINOR_VERSION . DIRECTORY_SEPARATOR;
            $dirs[] = self::BUILDS_DIR . DIRECTORY_SEPARATOR . PHP_MAJOR_VERSION . DIRECTORY_SEPARATOR;
            $dirs[] = self::BUILDS_DIR . DIRECTORY_SEPARATOR;
        }
        
        $depCnt = 0;

        foreach(array_keys(self::DEFINITIONS) as $class) {  

            $depCnt += 1 + count(self::DEFINITIONS[$class]);
        }

        $interfaces = [];
        $classes = [];

        foreach($dirs as $dir) {

            foreach(array_keys(self::DEFINITIONS) as $class) {

                foreach(self::DEFINITIONS[$class] as $interface) {

                    $interfaceFile = realpath( __DIR__ . DIRECTORY_SEPARATOR . $dir . str_replace(self::BACKWARD_SLASH, DIRECTORY_SEPARATOR, $interface) . self::PHP_FILE_EXTENSION);

                    if(empty($interfaceFile)) {

                        continue;
                    }

                    $interfaces[] = $interfaceFile;
                }

                $classFile = realpath( __DIR__ . DIRECTORY_SEPARATOR . $dir . str_replace(self::BACKWARD_SLASH, DIRECTORY_SEPARATOR, $class) . self::PHP_FILE_EXTENSION );

                if(empty($classFile)) {

                    continue;
                }                            

                $classes[] = $classFile;
            }
        }

        $files = array_merge($interfaces, $classes);

        // Bail, if we haven't been able to find the exact amount of expected files / dependencies.

        if(count($files) !== $depCnt) {

            return false;
        }

        // Start requiring... We should be good to go.

        foreach($files as $file) {

            require_once $file;
        }

        return true;
    }

})::create();

