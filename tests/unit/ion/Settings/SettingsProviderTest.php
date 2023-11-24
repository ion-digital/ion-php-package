<?php

/*
 * See license information at the package root in LICENSE.md
 */


namespace ion\Settings;

/**
 * Description of PackageTest
 *
 * @author Justus
 */

use \PHPUnit\Framework\TestCase;
use \ion\Package;
use \ion\PackageInterface;
use \ion\Settings\Providers\MemorySettingsProvider;

class SettingsProviderTest extends TestCase {

    private const KEY_1 = "key_1";
    private const VALUE_1 = "value_1";

    private const KEY_2 = "key_2";
    private const VALUE_2 = "value_2";    
    
    const TEST_PACKAGE_VENDOR = "xyz";
    const TEST_PACKAGE_PROJECT = "project";

    function testLoad() {      

        $package = Package::create(
            
            self::TEST_PACKAGE_VENDOR, 
            self::TEST_PACKAGE_PROJECT, 
            true, 
            function(PackageInterface $package): void {

                return;
            },
            realpath(__DIR__ . "../../../../data/test-entry.php"),
            null,
            null,
            null,
            new MemorySettingsProvider([ self::KEY_1 => self::VALUE_1 ]),
            new MemorySettingsProvider([ self::KEY_2 => self::VALUE_2 ]),
        );

        $this->assertEquals(self::VALUE_1, $package->getSettings()->getAsString(self::KEY_1));
        $this->assertEquals(self::VALUE_2, $package->getSettings()->getAsString(self::KEY_2));
    }
}
