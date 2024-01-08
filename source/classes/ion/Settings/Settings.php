<?php

/*
 * See license information at the package root in LICENSE.md
 */

namespace Ion\Settings;

/**
 * Description of PackageSettings
 *
 * @author Justus
 */

use \ArrayAccess;
use \Ion\Settings\Settings;
use \Ion\Settings\SettingsInterface;
use \Ion\Settings\SettingsExceptionInterface;
use \Ion\Settings\SettingsException;

class Settings implements SettingsInterface, ArrayAccess {
    
    private $settings = [];
    
    final public function __construct(array $settings = []) {
        
        $this->settings = [];

        foreach(array_keys($settings) as $key) {

            if($settings[$key] == null) {

                continue;
            }

            $this->settings[$key] = $settings[$key];
        }        
    }
    
    final protected function get(string $name, $default = null) {
        
        if(!array_key_exists($name, $this->settings)) {
            
            return $default;
        }
        
        return $this->settings[$name];
    }
    
    final protected function set(string $name, $value = null): SettingsInterface {

        $this->settings[$name] = $value;
        return $this;
    }

    final public function has(string $name): bool {

        return ($this->get($name, null) !== null ? true : false);
    }
    
    final public function getAsBool(string $name, bool $default = false): bool {
        
        return boolval($this->get($name, $default));
    }
    
    final public function getAsString(string $name, string $default = ''): string {
        
        return (string) ($this->get($name, $default));
    }    
    
    final public function getAsInt(string $name, int $default = 0): int {
        
        return intval($this->get($name, $default));
    }    
    
    final public function getAsFloat(string $name, float $default = 0.0): float {
        
        return floatval($this->get($name, $default));
    }        
    
    final public function getAsArray(string $name, array $default = []): array {
        
        $value = $this->get($name, null);
        
        if($value === null) {
            
            return $default;
        }
        
        if(is_array($value)) {
            
            return $value;
        }
        
        return [ $value ];
    }

    final public function getSection(string $name): SettingsInterface {

        return new Settings($this->getAsArray($name, []));
    }
    
    final public function toArray(): array {
        
        return $this->settings;
    }
    
    private static function offsetToKey($offset, array $settings): ?string {
        
        if(is_string($offset)) {
            
            return $offset;
        }
            
        $keys = array_keys($settings);

        if(count($keys) > intval($offset)) {

            return $keys[$offset];
        }                        
               
        return null;
    }
    
    public function offsetExists($offset): bool {
    
        $key = static::offsetToKey($offset, $this->settings);
        
        if($key === null) {
            
            return false;
        }
        
        return array_key_exists($key, $this->settings);
    }
    
    public function offsetGet($offset): mixed {
        
        $key = static::offsetToKey($offset, $this->settings);
        
        if($key === null) {
            
            return null;
        }
        
        return $this->settings[$key];
    }
    
    public function offsetSet($offset, $value): void {
  
        throw new SettingsException("Settings cannot be changed once loaded (attempted to change '{$offset}').");
    }
    
    public function offsetUnset($offset): void {
        
        $key = static::offsetToKey($offset, $this->settings);
        
        if($key === null) {
                     
            return;
        }

        $this->settings[$key] = null;
        
        return;
    }
}
