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

use PHPUnit\Framework\TestCase;


class TestSettingsSection {

    public $sectionInt = SettingsTest::INT_VALUE;
    public $sectionString = SettingsTest::STRING_VALUE;
    public $sectionFloat = SettingsTest::FLOAT_VALUE;
    public $sectionArray = SettingsTest::ARRAY_VALUE;
    public $sectionBool = SettingsTest::BOOL_VALUE;  
}

class TestSettings {

    public $int = SettingsTest::INT_VALUE;
    public $string = SettingsTest::STRING_VALUE;
    public $float = SettingsTest::FLOAT_VALUE;
    public $array = SettingsTest::ARRAY_VALUE;
    public $bool = SettingsTest::BOOL_VALUE;

    public $section = null;  

    public function __construct() {

        $this->section = new TestSettingsSection(); 
    }
}

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
    
    
    function testStructure() {
        
        $json = json_encode(new TestSettings(), JSON_PRETTY_PRINT);
     
        $obj = new Settings(json_decode($json, true)); // Force an associative array

        $this->assertEquals(0, $obj->getAsInt('non_existent_setting'));
        $this->assertEquals("", $obj->getAsString('non_existent_setting'));
        $this->assertEquals(0.0, $obj->getAsFloat('non_existent_setting'));
        $this->assertEquals([], $obj->getAsArray('non_existent_setting'));
        $this->assertEquals(false, $obj->getAsBool('non_existent_setting'));

        $this->assertEquals(self::INT_VALUE, $obj->getAsInt('int'));
        $this->assertEquals(self::STRING_VALUE, $obj->getAsString('string'));
        $this->assertEquals(self::FLOAT_VALUE, $obj->getAsFloat('float'));
        $this->assertEquals(self::ARRAY_VALUE, $obj->getAsArray('array'));
        $this->assertEquals(self::BOOL_VALUE, $obj->getAsBool('bool'));

        $section = $obj->getSection('section');
        
        $this->assertNotNull($section);

        $this->assertEquals(0, $section->getAsInt('non_existent_setting'));
        $this->assertEquals("", $section->getAsString('non_existent_setting'));
        $this->assertEquals(0.0, $section->getAsFloat('non_existent_setting'));
        $this->assertEquals([], $section->getAsArray('non_existent_setting'));
        $this->assertEquals(false, $section->getAsBool('non_existent_setting'));

        $this->assertEquals(self::INT_VALUE, $section->getAsInt('sectionInt'));
        $this->assertEquals(self::STRING_VALUE, $section->getAsString('sectionString'));
        $this->assertEquals(self::FLOAT_VALUE, $section->getAsFloat('sectionFloat'));
        $this->assertEquals(self::ARRAY_VALUE, $section->getAsArray('sectionArray'));
        $this->assertEquals(self::BOOL_VALUE, $section->getAsBool('sectionBool'));        
    }

    
}
