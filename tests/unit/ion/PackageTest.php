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

use PHPUnit\Framework\TestCase;

class PackageTest extends TestCase {
    
    const TEST_PACKAGE_VENDOR = 'xyz';
    const TEST_PACKAGE_PROJECT = 'package';
    const TEST_PACKAGE = self::TEST_PACKAGE_VENDOR . '/' . self::TEST_PACKAGE_PROJECT;
    
    const TEST_PACKAGE_PROJECT_1 = self::TEST_PACKAGE_PROJECT . '_1';
    const TEST_PACKAGE_PROJECT_2 = self::TEST_PACKAGE_PROJECT . '_2';    
    const TEST_PACKAGE_PROJECT_3 = self::TEST_PACKAGE_PROJECT . '_3'; 
    const TEST_PACKAGE_PROJECT_4 = self::TEST_PACKAGE_PROJECT . '_4'; 
    const TEST_PACKAGE_PROJECT_5 = self::TEST_PACKAGE_PROJECT . '_5'; 
    const TEST_PACKAGE_PROJECT_6 = self::TEST_PACKAGE_PROJECT . '_6'; 
    const TEST_PACKAGE_PROJECT_7 = self::TEST_PACKAGE_PROJECT . '_7'; 
    const TEST_PACKAGE_PROJECT_8 = self::TEST_PACKAGE_PROJECT . '_8'; 
    const TEST_PACKAGE_PROJECT_9 = self::TEST_PACKAGE_PROJECT . '_9';  
    const TEST_PACKAGE_PROJECT_10 = self::TEST_PACKAGE_PROJECT . '_10'; 
    
    const TEST_PACKAGE_1 = self::TEST_PACKAGE_VENDOR . '/' . self::TEST_PACKAGE_PROJECT_1;
    const TEST_PACKAGE_2 = self::TEST_PACKAGE_VENDOR . '/' . self::TEST_PACKAGE_PROJECT_2;
    
    const AUTO_LOADER_PROJECT_DIR = '../../data/';
    const ENTRY_FILENAME = 'root.txt';
    
    const SOURCE_DIRECTORY = './a/';
    const EXTRA_DIRECTORY_1 = './b/';
    const EXTRA_DIRECTORY_2 = './c/';
    const NON_EXISTENT_DIRECTORY = './non_existent/';
    
    const MAJOR_VERSION = 1;
    const MINOR_VERSION = 2;
    const PATCH_VERSION = 3;
    
    private static function createRootDirectory(PackageTest $tests, bool $createFileName = true) {
        
        $f = __DIR__ . DIRECTORY_SEPARATOR . self::AUTO_LOADER_PROJECT_DIR . DIRECTORY_SEPARATOR . ($createFileName ? self::ENTRY_FILENAME : '');

        $f = realpath($f);

        $tests->assertNotNull($f);

        if(!$createFileName) {

            return $f . DIRECTORY_SEPARATOR;
        }

        return($f);
    }
    
    private static function createPackage(
        
            PackageTest $tests,
            string $project, 
            bool $debug = null, 
            bool $cache = null, 
            array $loaders = null, 
            bool $createVersion = true, 
            bool $createFileName = true
        ) {
        
        $version = null;

        if($createVersion === true) {

            $version = new SemVer(self::MAJOR_VERSION, self::MINOR_VERSION, self::PATCH_VERSION);
        }

        return Package::create(
            
            self::TEST_PACKAGE_VENDOR, 
            $project, 
            true, 
            function(PackageInterface $package): void {

                return;
            }, 
            static::createRootDirectory($tests, $createFileName), 
            $version
        );                     

    }
    
    public function testCreate() {
        
        $this->assertEquals(false, Package::hasInstance(self::TEST_PACKAGE_VENDOR, self::TEST_PACKAGE_PROJECT_1));
        $this->assertEquals(false, Package::hasInstance(self::TEST_PACKAGE_VENDOR, self::TEST_PACKAGE_PROJECT_2));

        $this->assertNull(Package::getInstance(self::TEST_PACKAGE_VENDOR, self::TEST_PACKAGE_PROJECT_1));
        $this->assertNull(Package::getInstance(self::TEST_PACKAGE_VENDOR, self::TEST_PACKAGE_PROJECT_2));
        
        $package1 = self::createPackage($this, self::TEST_PACKAGE_PROJECT_1, true, false);
        $package2 = self::createPackage($this, self::TEST_PACKAGE_PROJECT_2, false, false);        

        $this->assertEquals(3, count(Package::getInstances()));
        
        $this->assertEquals(true, Package::hasInstance(self::TEST_PACKAGE_VENDOR, self::TEST_PACKAGE_PROJECT_1));
        $this->assertEquals(true, Package::hasInstance(self::TEST_PACKAGE_VENDOR, self::TEST_PACKAGE_PROJECT_2));
        
        $this->assertNotNull(Package::getInstance(self::TEST_PACKAGE_VENDOR, self::TEST_PACKAGE_PROJECT_1));
        $this->assertNotNull(Package::getInstance(self::TEST_PACKAGE_VENDOR, self::TEST_PACKAGE_PROJECT_2));
        
        $this->assertEquals(self::TEST_PACKAGE_1, Package::getInstance(self::TEST_PACKAGE_VENDOR, self::TEST_PACKAGE_PROJECT_1)->getName());
        $this->assertEquals(self::TEST_PACKAGE_2, Package::getInstance(self::TEST_PACKAGE_VENDOR, self::TEST_PACKAGE_PROJECT_2)->getName());
        
        $this->assertEquals(self::TEST_PACKAGE_1, Package::getInstances()[self::TEST_PACKAGE_VENDOR . '/' . self::TEST_PACKAGE_PROJECT_1]->getName());
        $this->assertEquals(self::TEST_PACKAGE_2, Package::getInstances()[self::TEST_PACKAGE_VENDOR . '/' . self::TEST_PACKAGE_PROJECT_2]->getName());        
        
        //$this->assertEquals(true, $package1->isDebugEnabled());
        //$this->assertEquals(false, $package1->isCacheEnabled());
        //$this->assertEquals(3, count($package1->getAdditionalPaths()));
        //$this->assertEquals(1, count($package1->getSearchPaths()));
        
        //$this->assertEquals(false, $package2->isDebugEnabled());
        //$this->assertEquals(false, $package2->isCacheEnabled());
        //$this->assertEquals(3, count($package2->getAdditionalPaths()));
        //$this->assertEquals(3, count($package2->getSearchPaths()));        
        
        $package1->destroy();
        $package2->destroy();
        
    }
    
    public function testVersionAndName() {
        
        $package = self::createPackage($this, self::TEST_PACKAGE_PROJECT, true, false, [], true, true);
                        
        $this->assertEquals(1, $package->getVersion()->getMajor());
        $this->assertEquals(2, $package->getVersion()->getMinor());
        $this->assertEquals(3, $package->getVersion()->getPatch());
        
        $this->assertEquals(self::TEST_PACKAGE_VENDOR, $package->getVendor());
        $this->assertEquals(self::TEST_PACKAGE_PROJECT, $package->getProject());
        $this->assertEquals(self::TEST_PACKAGE, $package->getName());        
        
        $package->destroy();        
    }
    
    public function testPathsForPackageWithFilename() {
        
        // with filename
        
        $packageWithFilename = self::createPackage($this, self::TEST_PACKAGE_PROJECT, true, false, [], true, true);

        $this->assertEquals(static::createRootDirectory($this, false), $packageWithFilename->getProjectRootDirectory());        
        $this->assertEquals(static::createRootDirectory($this, true), $packageWithFilename->getProjectRootFile());
        
        $packageWithFilename->destroy();
    }
    
    // public function testPathsForPackageWithoutFilename() {    
        
    //     // without filename
        
    //     $packageWithoutFilename = self::createPackage($this, self::TEST_PACKAGE_PROJECT, true, false, [], true, false);
                
    //     $this->assertEquals(static::createRootDirectory($this, false), $packageWithoutFilename->getProjectRootDirectory());        
    //     $this->assertNull($packageWithoutFilename->getProjectRootFile());
        
    //     $packageWithoutFilename->destroy();

    // }

    public function testLoadVersion() {
        
        $package =  self::createPackage($this, self::TEST_PACKAGE_PROJECT_9, true, false, null, false);                          
                                
        $this->assertEquals(true, file_exists($package->getProjectRootDirectory() . 'root.txt'));
        
        $this->assertEquals(true, file_exists($package->getProjectRootDirectory() . 'version.json'));
        
        $this->assertEquals(9, $package->getVersion()->getMajor());
        $this->assertEquals(9, $package->getVersion()->getMinor());
        $this->assertEquals(9, $package->getVersion()->getPatch());
        
        $this->assertEquals('tests', $package->getVersion()->getRelease());
        
        $this->assertEquals(3, count($package->getVersion()->getBuildData()));
        
        $package->destroy();
    }
   
}
