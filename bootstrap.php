<?php

/* 
 * See license information at the package root in LICENSE.md
 */

 (new class {

    private const PACKAGING_INCLUDE_FILENAME = "index.php";
    private const COMPOSER_AUTOLOAD_FILENAME = "autoload.php";
	
	private const COMPOSER_AUTOLOAD_PATHS = [
	
		".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR,
        "vendor" . DIRECTORY_SEPARATOR
	];    

    private const LOAD_DEFINITION = "ION_PACKAGING_BOOTSTRAP";

    public static function create(string $entryFile): void {

        try {

            if(defined(self::LOAD_DEFINITION))
                return;

            $baseDir = dirname($entryFile);

            require_once(realpath( $baseDir . DIRECTORY_SEPARATOR . self::PACKAGING_INCLUDE_FILENAME ));

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
        catch(\Exception $exception) {

            self::handleException($exception);
            exit;
        }        
        catch(\Throwable $throwable) {

            self::handleThrowable($throwable);
            exit;
        }
    }

    private static function handleException(\Exception $exception): void {

        self::handleError($exception, Exception::class);
    }    

    private static function handleThrowable(\Throwable $throwable): void {

        self::handleError($throwable, Throwable::class);
    }

    private static function handleError(\Throwable $throwable, string $string): void {

        ?>
<html>
    <head>
        <title>Bootstrapping Error</title>
    </head>
    <body>        
        <h1>Bootstrapping Error Occurred</h1>

        <div><em>(caught in <?php echo __FILE__; ?>)</em></div>

        <h2>Type</h2>
        <div><?php echo $string; ?></div>

        <h2>Message</h2>
        <div><?php echo $throwable->getMessage(); ?>.</div>

        <h2>Source</h2>
        <div><strong><?php echo $throwable->getFile(); ?></strong> on line <strong><?php echo $throwable->getLine(); ?></strong></div>                            
    </body>
</html>
        <?php

    }

})::create(__FILE__);