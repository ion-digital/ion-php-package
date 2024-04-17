<?php
/*
 * See license information at the package root in LICENSE.md
 */
namespace Ion\Settings;

use Ion\PackageInterface;
use Ion\Settings\SettingsInterface;
use Ion\Settings\SettingsProviderInterface;
/**
 * Description of PackageSettings
 *
 * @author Justus
 */
abstract class SettingsProvider implements SettingsProviderInterface
{
    public abstract function load(PackageInterface $package, array $values = []) : SettingsInterface;
}