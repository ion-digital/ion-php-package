<?php

/*
 * See license information at the package root in LICENSE.md
 */


namespace Ion\Settings\Providers;

/**
 * Description of PackageTest
 *
 * @author Justus
 */

use \PHPUnit\Framework\TestCase;
use \Ion\Package;
use \Ion\PackageInterface;

class JsonFileSettingsProviderTest extends TestCase {

    private const INSERTED_KEY = "insertedKey";
    private const INSERTED_VALUE = "insertedValue";
    
    const TEST_PACKAGE_VENDOR = "xyz";
    const TEST_PACKAGE_PROJECT = "json-project";

    function testLoad() {      

        $package = Package::create(
            
            self::TEST_PACKAGE_VENDOR, 
            self::TEST_PACKAGE_PROJECT, 
            function(PackageInterface $package): void {

                return;
            },
            realpath(__DIR__ . "../../../../../data/test-entry.php"),
            true
        );

        $provider = new JsonFileSettingsProvider("tests/data/settings.json");
        $this->assertNotNull($provider);

        $settings = $provider->load($package, [ self::INSERTED_KEY => self::INSERTED_VALUE ]);

        $this->assertNotNull($settings);
        $this->assertEquals(self::INSERTED_VALUE, $settings->getAsString(self::INSERTED_KEY));
    }
}
