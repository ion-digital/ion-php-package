<?php

/*
 * See license information at the package root in LICENSE.md
 */

namespace ion;

/**
 * A class that describes a package.
 *
 * @author Justus
 */

final class Package implements PackageInterface {

    private const PHP_VERSION_SEPARATOR = '.';
    private const COMPOSER_AUTOLOAD_PATH = 'vendor/autoload.php';
    private const SCRIPT_FILENAME_KEY = 'SCRIPT_FILENAME';

    public const COMPOSER_FILENAME = 'composer.json';
    public const VERSION_FILENAME = 'version.json';    

    private static $instances = [];

    /**
     * 
     * Create a package instance.
     * 
     * @param string $vendor The vendor name (__vendor__/project).
     * @param string $project The project name (vendor/__project__).
     * @param bool $requireOnly If __true__, throw an exception if the calling script is accessed directly - of __false__, allow direct calls.  
     * @param callable $loadingHandler The callback that will handle the loading of class files - defaults to requiring Composer's 'vendor/autoload.php' script.
     * @param string $projectRootFile An optional parameter to override the project root script - defaults to the calling script.
     * @param SemVerInterface $version The current package version - will be loaded from the file, if __NULL__ and if a version definition file exists, or a Composer version tag is available (in _composer.json_).
     * @param int $requiredPhpMajorVersion The minimum required PHP major version. If __NULL__, it will be determined via the "require" section in composer.json, or disregarded if missing.
     * @param int $requiredPhpMinorVersion The minimum required PHP minor version. If __NULL__, it will be determined via the "require" section in composer.json, or disregarded if __$requiredPhpMajorVersion__ is missing; otherwise it will be set to 0.
     * @return PackageInterface Returns the new package instance.
     */    
    
    public static function create(
            
            string $vendor,
            string $project,
            bool $requireOnly = true,
            callable $loadingHandler = null,
            string $projectRootFile = null,
            SemVerInterface $version = null,
            int $requiredPhpMajorVersion = null,
            int $requiredPhpMinorVersion = null
            
        ): PackageInterface {

        return new static(
                
            $vendor, 
            $project, 
            $requireOnly,

            $loadingHandler ?? function(PackageInterface $package): void {

                echo $package->getProjectRootDirectory() . Package::COMPOSER_AUTOLOAD_PATH . "\n";

                $f = $package->getProjectRootDirectory() . Package::COMPOSER_AUTOLOAD_PATH;

                if(empty(realpath($f))) {

                    throw new PackageException("The composer autoloader script ('{$f}') for package '{$vendor}/{$project}' does not exist.");
                }

                require_once(realpath($f));
            },      
            
            $projectRootFile ?? static::getCallingFile(),

            $version,
            $requiredPhpMajorVersion,
            $requiredPhpMinorVersion
        );
    }
    
    /**
     * 
     * Return all registered package instances.
     * 
     * @return array An array containing all registered package instances.
     * 
     */    

    public static function getInstances(): array {
        
        return static::$instances;
    }
    
    /**
     * 
     * Check if a package has been registered.
     * 
     * @param string $vendorName The package vendor name.
     * @param string $projectName The package project name.
     * 
     * @return bool Returns __true__ if the package as been registered, __false__ if not.
     * 
     */

    public static function hasInstance(string $vendorName, string $projectName): bool {

        return (bool) array_key_exists($vendorName . '/' . $projectName, static::$instances);
    }
    
    /**
     * 
     * Get a package instance by package name.
     * 
     * @param string $vendorName The package vendor name.
     * @param string $projectName The package project name.
     * 
     * @return PackageInterface Returns the registered package instance.
     * 
     */    
    
    public static function getInstance(string $vendorName, string $projectName): ?PackageInterface {
        
        if(!static::hasInstance($vendorName, $projectName)) {

            return null;
        }
    
        return static::$instances[$vendorName . '/' . $projectName];
    }

    protected static function destroyInstance(self $instance): void {
        
        unset(static::$instances[$instance->getName()]);
    }
    
    protected static function registerInstance(self $instance): void {

        if ($instance->getVersion() !== null) {
            
            if (array_key_exists($instance->getName(), static::$instances) === true) {

                $tmp = static::$instances[$instance->getName()];
                
                if ($tmp->getVersion() !== null) {
                    
                    if ($instance->getVersion()->isLowerThan($tmp->getVersion())) {
                        
                        static::$instances[$instance->getName()]->destroy();
                    }
                }
            }
        }

        static::$instances[$instance->getName()] = $instance;
        
        return;
    }

    /**
     * 
     * Get the PHP script file that contained the last function/method call (or further, depending on $back).
     * 
     * @param int $back The number of times / steps to trace back.
     * 
     * @return string Return the resulting script.
     * 
     */    
    
    public static function getCallingFile(int $back = 1): string {

        $trace = debug_backtrace();

        if ($back > count($trace)) {
            
            $back = count($trace) - 1;
        }

        for ($i = 0; $i < $back; $i++) {
            
            array_shift($trace);
        }

        $trace = array_values($trace);

        return realpath($trace[array_search(__FUNCTION__, array_column($trace, 'function'))]['file']) . DIRECTORY_SEPARATOR;
    }

    private $vendor = null;
    private $project = null;
    private $version = null;
    private $requireOnly = null;
    private $name = null;
    private $projectRootFile = null;
    private $projectRootDirectory = null;
    private $requiredPhpMajorVersion = null;
    private $requiredPhpMinorVersion = null;

    protected function __construct(
        
            string $vendor, 
            string $project,
            bool $requireOnly,            
            callable $loadingHandler, 
            string $projectRootFile,
            SemVerInterface $version = null,
            int $requiredPhpMajorVersion = null,
            int $requiredPhpMinorVersion = null

        ) {

        $this->requireOnly = $requireOnly;

        $this->vendor = $vendor;
        $this->project = $project;
        $this->name = $vendor . '/' . $project;
        
        $this->projectRootFile = realpath($projectRootFile);
        
        if(empty($this->projectRootFile)) {
            
            throw new PackageException("Project root script '{$projectRootFile}' for package '{$vendor}/{$project}' is invalid.");
        }
        
        if(is_dir($this->projectRootFile)) {

            throw new PackageException("Specified project root script '{$this->projectRootFile}' for package '{$vendor}/{$project}' is a directory.");
        }

        if ($requireOnly && $this->projectRootFile == $_SERVER[self::SCRIPT_FILENAME_KEY]) {

            throw new PackageException("'{$this->projectRootFile}' for package '{$vendor}/{$project}' cannot be accessed directly.");
        }

        $this->projectRootDirectory = pathinfo($this->projectRootFile, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR;
        
        $this->version = $version;
        
        if($this->version === null) {
            
            $this->version = $this->loadVersion();
        }          
        
        static::registerInstance($this);

        $loadingHandler($this);
    }
    
    protected function isDependency(): ?bool { // NULL = Possibly, not sure; TRUE = Definitely yes; FALSE = Definitely no.
        
        //.gitignore? .git? composer.json? /vendor ? version.json? .hg? .hgignore?
        
        if(strstr($this->projectRoot, DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR)) {
            
            return true;
        }
        
        return null;
    }        
    
    protected function hasRepository(): bool {
        
        $repos = [ '.git', '.hg' ];
        
        foreach($repos as $repo) {
            
            if(is_dir($this->projectRoot . DIRECTORY_SEPARATOR . $repo)) {
                
                return true;
            }
        }
        
        return false;
    }
  
    /**
     * 
     * Destroy an instance.
     *
     * @return void
     * 
     */    

    public function destroy(): void {
        
        static::destroyInstance($this);        
        return;
    }
        
    protected function getVendorRootDirectory(string $includePath, int $phpMajorVersion = null, int $phpMinorVersion = null): string {

        if ($phpMajorVersion !== null || ($phpMajorVersion !== null && $phpMinorVersion !== null)) {

            if ($phpMinorVersion !== null) {
                return $includePath . DIRECTORY_SEPARATOR . $phpMajorVersion . static::PHP_VERSION_SEPARATOR . $phpMinorVersion . DIRECTORY_SEPARATOR . $this->vendor . DIRECTORY_SEPARATOR;
            }

            return $includePath . DIRECTORY_SEPARATOR . $phpMajorVersion . DIRECTORY_SEPARATOR . $this->vendor . DIRECTORY_SEPARATOR;
        }

        return $includePath . DIRECTORY_SEPARATOR . $this->vendor . DIRECTORY_SEPARATOR;
    }
      
    protected function loadVersion(): ?SemVerInterface {
        
        // if(defined(static::ION_PACKAGE_IGNORE_VERSION) && (constant(static::ION_PACKAGE_IGNORE_VERSION) === true)) {
            
        //     return null;
        // }

        $path = $this->getProjectRootDirectory() . static::VERSION_FILENAME;
        
        if(file_exists($path)) {
        
            $data = file_get_contents($path);
            
            if($data !== false) {
            
                $version = SemVer::parsePackageJson($data);

                if($version !== null) {
                    return $version;
                }
            }
        }
        
        $path = $this->getProjectRootDirectory() . static::COMPOSER_FILENAME;

        if(file_exists($path)) {   

            $data = file_get_contents($path);

            if($data !== false) {
                
                return SemVer::parseComposerJson($data);
            }
        }        
        
        return null;
    }

    /**
     * 
     * Get the package version.
     * 
     * @return ?SemVerInterface Returns the specified version of the package, or null if not specified.
     * 
     */    

    public function getVersion(): ?SemVerInterface {
        
        return $this->version;
    }

    /**
     * 
     * Get the package vendor name.
     * 
     * @return string Returns the vendor name.
     * 
     */    
    
    public function getVendor(): string {
        
        return $this->vendor;
    }

    /**
     * 
     * Get the package project name.
     *
     * @return string Returns the project name.
     * 
     */   
    
    public function getProject(): string {
        
        return $this->project;
    }
    
    /**
     * Get the package name (in the format vendor/project).
     *
     * @return string Returns the package name (in the format vendor/project).
     * 
     */  

    public function getName(): string {
        
        return $this->name;
    }

    /**
     * Get the project root directory.
     *
     * @return string Returns the project root directory.
     * 
     */    
    
    public function getProjectRootDirectory(): string {
        
        return $this->projectRootDirectory;
    }
    
    /**
     * 
     * Get the project root file.
     * 
     */
    
    public function getProjectRootFile(): string {
        
        return $this->projectRootFile;
    }
          
}
