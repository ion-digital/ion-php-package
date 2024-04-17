<?php
namespace Ion;

use Ion\Settings\SettingsInterface;
use Ion\Settings\SettingsProviderInterface;
use Ion\DisposableInterface;
use Ion\SemVerInterface;
interface PackageInterface
{
    /**
     *
     * Attaches a object to be disposed along with the package.
     *
     * @param DisposableInterface $disposable The DisposableInterface instance to attach.
     *
     * @return PackageInterface Returns the package.
     *
     */
    function attachDisposable(DisposableInterface $disposable) : PackageInterface;
    /**
     *
     * Get the package version.
     *
     * @return ?SemVerInterface Returns the specified version of the package, or null if not specified.
     *
     */
    function getVersion() : ?SemVerInterface;
    /**
     *
     * Get the package vendor name.
     *
     * @return string Returns the vendor name.
     *
     */
    function getVendor() : string;
    /**
     *
     * Get the package project name.
     *
     * @return string Returns the project name.
     *
     */
    function getProject() : string;
    /**
     * Get the package name (in the format vendor/project).
     *
     * @return string Returns the package name (in the format vendor/project).
     *
     */
    function getName() : string;
    /**
     * Get the project root directory.
     *
     * @return string Returns the project root directory.
     *
     */
    function getProjectRootDirectory() : string;
    /**
     *
     * Get the project root file.
     *
     */
    function getProjectRootFile() : string;
    /**
     *
     * Adds a settings provider.
     *
     * @return PackageInterface Returns the calling instance of the package.
     *
     */
    function addSettingsProvider(SettingsProviderInterface $provider) : PackageInterface;
    /**
     *
     * Clears the registered settings providers.
     *
     * @return PackageInterface Returns the calling instance of the package.
     *
     */
    function clearSettingsProviders() : PackageInterface;
    /**
     *
     * Returns the registered settings providers.
     *
     * @return array An array of settings providers.
     *
     */
    function getSettingsProviders() : array;
    /**
     *
     * Get the the package settings.
     *
     * @return SettingsInterface Returns all configuration settings.
     *
     */
    function getSettings() : SettingsInterface;
}