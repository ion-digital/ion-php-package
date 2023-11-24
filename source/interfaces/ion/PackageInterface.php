<?php

namespace ion;

use \ion\Settings\SettingsInterface;
use \ion\Settings\SettingsProviderInterface;
use \ion\SemVerInterface;

interface PackageInterface {

    /**
     *
     * Create a package instance.
     *
     * @param string $vendor The vendor name (__vendor__/project).
     * @param string $project The project name (vendor/__project__).
     * @param bool $requireOnly If __true__, throw an exception if the calling script is accessed directly - of __false__, allow direct calls.
     * @param callable $loadingHandler The callback that will handle the loading of class files - defaults to requiring Composer's 'vendor/autoload.php' script.
     * @param string $projectRootFile An optional parameter to override the project root script - defaults to the calling script.
     * @param SemVerInterface $version The current package version - will be loaded from the file, if __NULL__ and if a version definition file exists, or a Composer version tag is available (in _composer.json_).
     * @param int $requiredPhpMajorVersion The minimum required PHP major version. If __NULL__, it will be disregarded.
     * @param int $requiredPhpMinorVersion The minimum required PHP minor version. If __NULL__, it will be disregarded if __$requiredPhpMajorVersion__ is __NULL__; otherwise it will be set to 0.
     * @param SettingsProviderInterface ...$settingsProviders Additional settings providers (replaces the default).
     * @return PackageInterface Returns the new package instance.
     */

    static function create(

        string $vendor,
        string $project,
        bool $requireOnly,
        callable $loadingHandler,
        string $projectRootFile,
        SemVerInterface $version = null,
        int $requiredPhpMajorVersion = null,
        int $requiredPhpMinorVersion = null,
        SettingsProviderInterface ...$settingsProviders

    ): PackageInterface;

    /**
     *
     * Return all registered package instances.
     *
     * @return array An array containing all registered package instances.
     *
     */

    static function getInstances(): array;

    /**
     *
     * Check if a package has been registered.
     *
     * @param string $vendorName The package vendor name.
     * @param string $projectName The package project name.
     *
     * @return bool Returns __true__ if the package as been registered, __false__ if not.
     *
     */

    static function hasInstance(string $vendorName, string $projectName): bool;

    /**
     *
     * Get a package instance by package name.
     *
     * @param string $vendorName The package vendor name.
     * @param string $projectName The package project name.
     *
     * @return PackageInterface Returns the registered package instance.
     *
     */

    static function getInstance(string $vendorName, string $projectName): ?PackageInterface;

    /**
     *
     * Get the PHP script file that contained the last function/method call (or further, depending on $back).
     *
     * @param int $back The number of times / steps to trace back.
     *
     * @return string Return the resulting script.
     *
     */

    static function getCallingFile(int $back = 1): string;

    /**
     *
     * Destroy an instance.
     *
     * @return void
     *
     */

    function destroy(): void;

    /**
     *
     * Get the package version.
     *
     * @return ?SemVerInterface Returns the specified version of the package, or null if not specified.
     *
     */

    function getVersion(): ?SemVerInterface;

    /**
     *
     * Get the package vendor name.
     *
     * @return string Returns the vendor name.
     *
     */

    function getVendor(): string;

    /**
     *
     * Get the package project name.
     *
     * @return string Returns the project name.
     *
     */

    function getProject(): string;

    /**
     * Get the package name (in the format vendor/project).
     *
     * @return string Returns the package name (in the format vendor/project).
     *
     */

    function getName(): string;

    /**
     * Get the project root directory.
     *
     * @return string Returns the project root directory.
     *
     */

    function getProjectRootDirectory(): string;

    /**
     *
     * Get the project root file.
     *
     */

    function getProjectRootFile(): string;

    /**
     *
     * Adds a settings provider.
     *
     * @return PackageInterface Returns the calling instance of the package.
     *
     */

    function addSettingsProvider(SettingsProviderInterface $provider): PackageInterface;

    /**
     *
     * Clears the registered settings providers.
     *
     * @return PackageInterface Returns the calling instance of the package.
     *
     */

    function clearSettingsProviders(): PackageInterface;

    /**
     *
     * Returns the registered settings providers.
     *
     * @return array An array of settings providers.
     *
     */

    function getSettingsProviders(): array;

    /**
     *
     * Get the the package settings.
     *
     * @return SettingsInterface Returns all configuration settings.
     *
     */

    function getSettings(): SettingsInterface;

}
