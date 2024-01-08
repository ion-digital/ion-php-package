<?php

namespace Ion\Settings;

use \Ion\PackageInterface;
use \Ion\Settings\SettingsInterface;


/**
 * Description of PackageSettings
 *
 * @author Justus
 */
interface SettingsProviderInterface {

    function load(PackageInterface $package, array $values = []): SettingsInterface;

}
