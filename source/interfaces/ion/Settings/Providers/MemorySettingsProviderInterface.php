<?php

namespace ion\Settings\Providers;

use \ion\PackageInterface;
use \ion\Settings\SettingsInterface;
use \ion\Settings\SettingsProviderInterface;

interface MemorySettingsProviderInterface extends SettingsProviderInterface {

    function load(PackageInterface $package, array $values = []): SettingsInterface;

}
