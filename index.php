<?php

/* 
 * See license information at the package root in LICENSE.md
 */

(new class {

    private const VENDOR = "ion";
    private const PROJECT = "packaging";
    private const CLASSNAME = "\ion\Package";
    private const BUILDS_DIR = "builds";
    private const SOURCE_DIR = "source";
    private const SOURCE_CLASSES_DIR = "classes";
    private const SOURCE_TRAITS_DIR = "traits";
    private const SOURCE_INTERFACES_DIR = "interfaces";
    private const FORWARD_SLASH = "/";
    private const BACKWARD_SLASH = "\\";
    private const PHP_VERSION_SEPARATOR = ".";
    private const PHP_FILE_EXTENSION = ".php";

    public static function create(): object {

        try {

            return (self::CLASSNAME)::create(
                
                self::VENDOR, 
                self::PROJECT, 
                true, 
            
                function() {
            
                    self::load();
                }, 

                __FILE__
            );
        }
        catch(\Throwable $throwable) {

            if(!self::load()) {

                throw $throwable;
            }

            return self::create();
        }
    }

    private static function load(): bool {

        if(class_exists(self::CLASSNAME)) {

            return false;
        }

        spl_autoload_register(function(string $className) {
        
            $dirs = [
                
                self::SOURCE_DIR . DIRECTORY_SEPARATOR . self::SOURCE_CLASSES_DIR . DIRECTORY_SEPARATOR,
                self::SOURCE_DIR . DIRECTORY_SEPARATOR . self::SOURCE_TRAITS_DIR . DIRECTORY_SEPARATOR,
                self::SOURCE_DIR . DIRECTORY_SEPARATOR . self::SOURCE_INTERFACES_DIR . DIRECTORY_SEPARATOR,
                self::BUILDS_DIR . DIRECTORY_SEPARATOR . PHP_MAJOR_VERSION . self::PHP_VERSION_SEPARATOR . PHP_MINOR_VERSION . DIRECTORY_SEPARATOR,
                self::BUILDS_DIR . DIRECTORY_SEPARATOR . PHP_MAJOR_VERSION . DIRECTORY_SEPARATOR,
            ];
    
            foreach($dirs as $dir) {
            
                $classPath = __DIR__ 

                    . DIRECTORY_SEPARATOR 
                    . str_replace(self::FORWARD_SLASH, DIRECTORY_SEPARATOR, $dir) 
                    . DIRECTORY_SEPARATOR 
                    . str_replace(self::BACKWARD_SLASH, DIRECTORY_SEPARATOR, $className) 
                    . self::PHP_FILE_EXTENSION;
    
                $classPath = realPath($classPath);
    
                if (file_exists($classPath)) {
                    
                    require_once($classPath);
                    break;
                }
            }
            
        }, true, false);  
        
        return true;
    }
    
})::create();
