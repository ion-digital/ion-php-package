<?php

/*
 * See license information at the package root in LICENSE.md
 */

namespace ion\Settings\Providers;

/**
 * Description of PackageSettings
 *
 * @author Justus
 */

 
 use \ion\PackageInterface;
 use \ion\Settings\Settings;
 use \ion\Settings\SettingsProvider;
 use \ion\Settings\SettingsInterface;
 use \ion\Settings\SettingsProviderException;
 
class JsonFileSettingsProvider extends SettingsProvider implements JsonFileSettingsProviderInterface {
    
    private const DEFAULT_SETTINGS_BASENAME = "settings";
    private const DEFAULT_SETTINGS_EXTENSION = ".json";
    private const DEFAULT_SETTINGS_FILENAME = self::DEFAULT_SETTINGS_BASENAME . self::DEFAULT_SETTINGS_EXTENSION;
    
    private static function parseJson(string $data): array {
        
        $json = json_decode($data, true);
        
        if(is_array($json)) {
            
            return $json;
        }
        
        throw new SettingsProviderException("Invalid settings data - could not parse JSON:\n\n{$data}");
    }

    private $settingsFilename;
    private $required;

    public function __construct(string $settingsFilename = self::DEFAULT_SETTINGS_FILENAME, bool $required = false) 
    {
        $this->settingsFilename = $settingsFilename;
        $this->required = $required;
    }

    public function load(PackageInterface $package, array $values = []): SettingsInterface {

        $data = null;                
        
        $path1 = $package->getProjectRootDirectory() . $this->settingsFilename;
        $path2 = $package->getProjectRootDirectory() . $this->settingsFilename . self::DEFAULT_SETTINGS_EXTENSION;

        $path = $path1;

        if(!file_exists($path)) {

            $path = $path2;
        }

        if(!file_exists($path)) {

            if($this->required)
                throw new SettingsProviderException("Could not load required JSON settings file (I tried '{$path1}' and '{$path2}').");
        }        

        if(file_exists($path)) {

            $data = file_get_contents($path);            
        }        
        
        if(empty($data)) {

            return new Settings($values);
        }
        
        return new Settings(array_merge($values, self::parseJson($data)));
    }
}