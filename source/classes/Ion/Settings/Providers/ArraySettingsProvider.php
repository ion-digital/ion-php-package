<?php

/*
 * See license information at the package root in LICENSE.md
 */

namespace Ion\Settings\Providers;

/**
 * Description of PackageSettings
 *
 * @author Justus
 */
 
 use \Ion\PackageInterface;
 use \Ion\Settings\Settings;
 use \Ion\Settings\SettingsProvider;
 use \Ion\Settings\SettingsInterface;
 use \Ion\Settings\SettingsProviderException;
 
class ArraySettingsProvider extends SettingsProvider implements ArraySettingsProviderInterface {
    
    private $values = [];

    public function __construct(array $values = []) 
    {
        $this->values = $values;
    }

    public function load(PackageInterface $package, array $values = []): SettingsInterface {

        return new Settings(array_merge($values, $this->values));
    }
}