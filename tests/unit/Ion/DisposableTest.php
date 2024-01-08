<?php

/*
 * See license information at the package root in LICENSE.md
 */


namespace Ion;

/**
 * Description of SemVerTest
 *
 * @author Justus
 */

use PHPUnit\Framework\TestCase;

class DisposableTestClass extends Disposable {

    protected function dispose(bool $disposing) {

        // empty!
    }
}

class DisposableTest extends TestCase {
        
    function testDestroy() {
        
        $obj = new DisposableTestClass();

        $this->assertFalse($obj->isDisposed());

        $obj->destroy();

        $this->assertTrue($obj->isDisposed());
    }   
}
