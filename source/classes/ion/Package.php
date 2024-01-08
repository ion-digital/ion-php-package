<?php

/*
 * See license information at the package root in LICENSE.md
 */

namespace Ion;

/**
 * A class that describes a package.
 *
 * @author Justus
 */

use \Ion\Settings\Settings;
use \Ion\Settings\SettingsInterface;
use \Ion\Settings\SettingsProviderInterface;
use \Ion\Settings\Providers\JsonFileSettingsProvider;

final class Package extends Disposable implements PackageInterface {

    private const PHP_VERSION_SEPARATOR = '.';
    private const COMPOSER_AUTOLOAD_PATH = 'vendor/autoload.php';
    private const SCRIPT_FILENAME_KEY = 'SCRIPT_FILENAME';

    public const COMPOSER_FILENAME = 'composer.json';
    public const VERSION_FILENAME = 'version.json';        

    public const IGNORE_PACKAGE_SETTINGS_DEFINITION = 'IGNORE_PACKAGE_SETTINGS';

    private static $instances = [];

    /**
     * 
     * Create a package instance.
     * 
     * @param string $vendor The vendor name (__vendor__/project).
     * @param string $project The project name (vendor/__project__).     
     * @param callable $handler The callback that will handle the loading of class files - defaults to requiring Composer's 'vendor/autoload.php' script.      
     * @param string $projectRootFile An optional parameter to override the project root script - defaults to the calling script.
     * @param bool $requireOnly If __true__, throw an exception if the calling script is accessed directly - of __false__, allow direct calls.  
     * @param SemVerInterface $version The current package version - will be loaded from the file, if __NULL__ and if a version definition file exists, or a Composer version tag is available (in _composer.json_).
     * @param int $requiredPhpMajorVersion The minimum required PHP major version. If __NULL__, it will be disregarded.
     * @param int $requiredPhpMinorVersion The minimum required PHP minor version. If __NULL__, it will be disregarded if __$requiredPhpMajorVersion__ is __NULL__; otherwise it will be set to 0.
     * @param SettingsProviderInterface ...$settingsProviders Additional settings providers (replaces the default).
     * @return PackageInterface Returns the new package instance.
     */    
    
    public static function create(

            string $vendor,
            string $project,
            callable $handler = null,
            string $projectRootFile = null,
            bool $requireOnly = true,
            SemVerInterface $version = null,
            int $requiredPhpMajorVersion = null,
            int $requiredPhpMinorVersion = null,
            SettingsProviderInterface ...$settingsProviders

        ): PackageInterface {

        return new static(
                
            $vendor, 
            $project,            

            $handler ?? function(PackageInterface $package) use ($vendor, $project): void {

                $f = $package->getProjectRootDirectory() . self::COMPOSER_AUTOLOAD_PATH;
                $rf = realpath($f);

                if(empty($rf)) {

                    throw new PackageException("The composer autoloader script ('{$f}') for package '{$vendor}/{$project}' does not exist.");
                }

                require_once($rf);
            },            

            $projectRootFile ?? static::getCallingFile(),
            $requireOnly,
            $version,
            $requiredPhpMajorVersion,
            $requiredPhpMinorVersion,
            ...$settingsProviders
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

        return realpath($trace[array_search(__FUNCTION__, array_column($trace, 'function'))]['file']);
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
    private $settingsProviders = [];

    protected function __construct(
        
            string $vendor, 
            string $project,            
            callable $handler, 
            string $projectRootFile,
            bool $requireOnly,
            SemVerInterface $version = null,
            int $requiredPhpMajorVersion = null,
            int $requiredPhpMinorVersion = null,           
            SettingsProviderInterface ...$settingsProviders

        ) {

        $this->requireOnly = $requireOnly;

        $this->vendor = $vendor;
        $this->project = $project;
        $this->name = $vendor . '/' . $project;
        
        $this->projectRootFile = realpath($projectRootFile);
        
        $this->settingsProviders = $settingsProviders;

        if(count($this->settingsProviders) === 0)
            $this->settingsProviders[] = new JsonFileSettingsProvider();

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
        
        $requiredPhpVersion = null;
        
        if($requiredPhpMajorVersion !== null) {

            $requiredPhpVersion = new SemVer(

                $requiredPhpMajorVersion, 
                $requiredPhpMinorVersion ?? 0
            );

            $phpVersion = SemVer::create(PHP_MAJOR_VERSION, PHP_MINOR_VERSION);

            if($phpVersion->isLowerThan($requiredPhpVersion)) {

                throw new PackageException("Package '{$vendor}/{$project}' requires at least PHP version {$requiredPhpVersion->getMajor()}.{$requiredPhpVersion->getMinor()}.");
            }
        }

        $this->version = $version;
        
        if($this->version === null) {
            
            $this->version = $this->loadVersion();
        }          
        
        static::registerInstance($this);

        $handler($this);
    }

    /**
     * 
     * Destroy an instance.
     *
     * @return void
     * 
     */    

    protected function dispose(bool $disposing): void {
        
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

    /**
     *
     * Adds a settings provider.
     * 
     * @return PackageInterface Returns the calling instance of the package.
     *  
     */

    public function addSettingsProvider(SettingsProviderInterface $provider): PackageInterface {

        $this->settingsProviders[] = $provider;
        return $this;
    }

    /**
     *
     * Clears the registered settings providers.
     * 
     * @return PackageInterface Returns the calling instance of the package.
     *  
     */

    public function clearSettingsProviders(): PackageInterface {

        $this->settingsProviders = [];
        return $this;
    }    

    /**
     * 
     * Returns the registered settings providers.
     * 
     * @return array An array of settings providers.
     * 
     */

     public function getSettingsProviders(): array {

        return $this->settingsProviders;
     }

    /**
     * 
     * Get the the package settings.
     * 
     * @return SettingsInterface Returns all configuration settings.
     * 
     */        
    
    public function getSettings(): SettingsInterface {

        if(defined(self::IGNORE_PACKAGE_SETTINGS_DEFINITION) && (constant(self::IGNORE_PACKAGE_SETTINGS_DEFINITION) === true))            
            return new Settings([]);
        
        if(count($this->settingsProviders) === 0)
            throw new PackageException("No settings providers have been registered.");
        
        $settings = new Settings();

        foreach($this->settingsProviders as $provider) {

            $settings = $provider->load($this, $settings->toArray());
        }

        return $settings;
    }
          
}
