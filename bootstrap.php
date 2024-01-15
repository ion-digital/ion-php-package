<?php

/* 
 * See license information at the package root in LICENSE.md
 */

 (new class {

    private const COMPOSER_AUTOLOAD_FILENAME = "autoload.php";
	
	private const COMPOSER_AUTOLOAD_PATHS = [
	
		".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR,
        "vendor" . DIRECTORY_SEPARATOR
	];    

    private const LOAD_DEFINITION = "ION_PACKAGING_BOOTSTRAP";

    public static function create(string $entryFile): void {

        if(defined(self::LOAD_DEFINITION))
            return;

        $baseDir = dirname($entryFile);

        define(self::LOAD_DEFINITION, $entryFile);

		$composerPath = null;
		
		foreach(self::COMPOSER_AUTOLOAD_PATHS as $autoloaderPath) {
			
            $composerPath = realpath( $baseDir . DIRECTORY_SEPARATOR . $autoloaderPath . DIRECTORY_SEPARATOR . self::COMPOSER_AUTOLOAD_FILENAME );

			if(!empty($composerPath))
                break;            
		}

        if(!empty($composerPath))
            require_once($composerPath);
    }

})::create(__FILE__);