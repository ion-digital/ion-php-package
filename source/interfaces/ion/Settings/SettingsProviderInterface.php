<?php

namespace ion\Settings;

use \ion\PackageInterface;
use \ion\Settings\SettingsInterface;


/**
 * Description of PackageSettings
 *
 * @author Justus
 */
interface SettingsProviderInterface {

    function load(PackageInterface $package, array $values = []): SettingsInterface;

}
