<?php

/*
 * See license information at the package root in LICENSE.md
 */

namespace Ion;

/**
 * Allows a class to gracefully dispose of its resources.
 *
 * @author Justus
 */

abstract class Disposable implements DisposableInterface {

    private $disposed = false;

    public final function destroy(): void {

        if($this->disposed === true)
            return;

        $this->dispose(true);
        $this->disposed = true;
    }

    protected abstract function dispose(bool $disposing);

    public final function __destruct() {

        $this->destroy(false);        
    }
}