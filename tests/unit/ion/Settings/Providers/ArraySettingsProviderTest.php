<?php

/*
 * See license information at the package root in LICENSE.md
 */


namespace ion\Settings\Providers;

/**
 * Description of PackageTest
 *
 * @author Justus
 */

use \PHPUnit\Framework\TestCase;
use \ion\Package;
use \ion\PackageInterface;

class ArraySettingsProviderTest extends TestCase {

    private const ORIGINAL_KEY = "originalKey";
    private const ORIGINAL_VALUE = "originalValue";    
    private const INSERTED_KEY = "insertedKey";
    private const INSERTED_VALUE = "insertedValue";
    
    const TEST_PACKAGE_VENDOR = "xyz";
    const TEST_PACKAGE_PROJECT = "project";

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

        $provider = new ArraySettingsProvider([ self::ORIGINAL_KEY => self::ORIGINAL_VALUE ]);
        $this->assertNotNull($provider);

        $settings = $provider->load($package, [ self::INSERTED_KEY => self::INSERTED_VALUE ]);
        $this->assertNotNull($settings);
        
        $this->assertEquals(self::ORIGINAL_VALUE, $settings->getAsString(self::ORIGINAL_KEY));
        $this->assertEquals(self::INSERTED_VALUE, $settings->getAsString(self::INSERTED_KEY));
    }
}
