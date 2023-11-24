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
 
class MemorySettingsProvider extends SettingsProvider implements MemorySettingsProviderInterface {
    
    private $values = [];

    public function __construct(array $values = []) 
    {
        $this->values = $values;
    }

    public function load(PackageInterface $package, array $values = []): SettingsInterface {

        return new Settings(array_merge($values, $this->values));
    }
}