<?php
namespace Ion\Settings\Providers;

use Ion\PackageInterface;
use Ion\Settings\SettingsInterface;
interface JsonFileSettingsProviderInterface
{
    function load(PackageInterface $package, array $values = []) : SettingsInterface;
}