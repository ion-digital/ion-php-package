<?php

/*
 * See license information at the package root in LICENSE.md
 */

namespace Ion;

final class PackageLoader {

    private const VENDOR = "ion";
    private const PROJECT = "packaging";
    private const CLASS_NAME = "\\Ion\\Package";
    private const INTERFACE_SUFFIX = "Interface";
    private const BUILDS_DIR = "builds";
    private const SOURCE_DIR = "source";
    private const SOURCE_CLASSES_DIR = "classes";
    private const SOURCE_INTERFACES_DIR = "interfaces";
    private const BACKWARD_SLASH = "\\";
    private const PHP_VERSION_SEPARATOR = ".";
    private const PHP_FILE_EXTENSION = ".php";
    private const COMPOSER_AUTOLOAD_FILENAME = "autoload.php";
	
	private const COMPOSER_AUTOLOAD_PATHS = [
	
		".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR,
        "vendor" . DIRECTORY_SEPARATOR
	];

    private const CLASS_DEFINITIONS = [

        "Ion\\Disposable" => [ "Ion\\DisposableInterface" ],
        "Ion\\Package" => [ "Ion\\PackageInterface" ],
        "Ion\\PackageException" => [ "Ion\\PackageExceptionInterface" ],
        "Ion\\SemVer" => [ "Ion\\SemVerInterface" ],

        "Ion\\Settings\\Settings" => [ "Ion\\Settings\\SettingsInterface" ],
        "Ion\\Settings\\SettingsException" => [ "Ion\\Settings\\SettingsExceptionInterface" ],        
        "Ion\\Settings\\SettingsProvider" => [ "Ion\\Settings\\SettingsProviderInterface" ],
        "Ion\\Settings\\SettingsProviderException" => [ "Ion\\Settings\\SettingsProviderExceptionInterface" ],              
        "Ion\\Settings\\Providers\\ArraySettingsProvider" => [ "Ion\\Settings\\Providers\\ArraySettingsProviderInterface" ],              
        "Ion\\Settings\\Providers\\JsonFileSettingsProvider" => [ "Ion\\Settings\\Providers\\JsonFileSettingsProviderInterface" ],
    ];

    private static $loaded = false;

    public static function create(string $baseDir): void {        

        $loaded = self::load($baseDir);

        if(self::isLoaded()) {

            return;
        }

        self::$loaded = $loaded;

        if(\Ion\Package::hasInstance(self::VENDOR, self::PROJECT))
            return;

        \Ion\Package::create(
            
            self::VENDOR, 
            self::PROJECT,             
        
            function() use ($baseDir) {
        
                if(!self::load($baseDir)) {

                    return;
                }

                return;
            }, 

            __FILE__,
            true
        );

        return;
    }

    private static function isLoaded(): bool {

        return self::$loaded;
    }

    private static function load(string $baseDir): bool {

        if(self::isLoaded())
            return false;

		$composerPath = null;
		
		foreach(self::COMPOSER_AUTOLOAD_PATHS as $autoloaderPath) {
			
            $composerPath = realpath( $baseDir . DIRECTORY_SEPARATOR . $autoloaderPath . DIRECTORY_SEPARATOR . self::COMPOSER_AUTOLOAD_FILENAME );

			if(!empty($composerPath))
                break;
		}

        if(!empty($composerPath))
            require_once($composerPath);

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

        foreach(array_keys(self::CLASS_DEFINITIONS) as $class) {  

            $depCnt += 1 + count(self::CLASS_DEFINITIONS[$class]);
        }

        $interfaces = [];
        $classes = [];

        foreach($dirs as $dir) {

            foreach(array_keys(self::CLASS_DEFINITIONS) as $class) {

                foreach(self::CLASS_DEFINITIONS[$class] as $interface) {

                    $interfaceFile = realpath( $baseDir . DIRECTORY_SEPARATOR . $dir . str_replace(self::BACKWARD_SLASH, DIRECTORY_SEPARATOR, $interface) . self::PHP_FILE_EXTENSION);

                    if(empty($interfaceFile)) {

                        continue;
                    }

                    $interfaces[] = $interfaceFile;
                }

                $classFile = realpath( $baseDir . DIRECTORY_SEPARATOR . $dir . str_replace(self::BACKWARD_SLASH, DIRECTORY_SEPARATOR, $class) . self::PHP_FILE_EXTENSION );

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
}
