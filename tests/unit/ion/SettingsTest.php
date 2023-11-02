<?php

/*
 * See license information at the package root in LICENSE.md
 */


namespace ion;

/**
 * Description of PackageTest
 *
 * @author Justus
 */

use \ion\Package;
use \ion\ISemVer;
use \ion\SemVer;
use \ion\Packages\PackageException;
use PHPUnit\Framework\TestCase;
use \ion\Packages\AutoLoader;
use \ion\Settings;

class SettingsTest extends TestCase {

    const INT_VALUE = 123;
    const STRING_VALUE = "string";
    const FLOAT_VALUE = 123.4;
    const ARRAY_VALUE = [ 1, 2, 3];
    const BOOL_VALUE = true;
    
    function testCreate() {
        
        $obj = new Settings([]);
        
        $this->assertNotNull($obj);
    }
    
    
    function testParseJson() {
        
        $json = json_encode(new class extends \stdClass { 
            
            public $int = SettingsTest::INT_VALUE;
            public $string = SettingsTest::STRING_VALUE;
            public $float = SettingsTest::FLOAT_VALUE;
            public $array = SettingsTest::ARRAY_VALUE;
            public $bool = SettingsTest::BOOL_VALUE;
        });
        
        $obj = Settings::parseJson($json);
        
        $this->assertEquals(self::INT_VALUE, $obj->getSetting('int'));
        $this->assertEquals(self::STRING_VALUE, $obj->getSetting('string'));
        $this->assertEquals(self::FLOAT_VALUE, $obj->getSetting('float'));
        $this->assertEquals(self::ARRAY_VALUE, $obj->getSetting('array'));
        $this->assertEquals(self::BOOL_VALUE, $obj->getSetting('bool'));
        
        $this->assertNull($obj->getSetting('non_existent_setting'));
        
        
    }
}
