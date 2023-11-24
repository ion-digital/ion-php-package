<?php

/*
 * See license information at the package root in LICENSE.md
 */

namespace ion\Settings;

use \ion\PackageInterface;
use \ion\Settings\SettingsInterface;
use \ion\Settings\SettingsProviderInterface;

/**
 * Description of PackageSettings
 *
 * @author Justus
 */


abstract class SettingsProvider implements SettingsProviderInterface {

    public abstract function load(PackageInterface $package, array $values = []): SettingsInterface;
}