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
abstract class Disposable implements DisposableInterface
{
    private $disposed = false;
    public final function destroy() : void
    {
        if ($this->isDisposed() === true) {
            return;
        }
        $this->dispose(true);
        $this->disposed = true;
    }
    public final function isDisposed() : bool
    {
        return $this->disposed;
    }
    protected abstract function dispose(bool $disposing);
    public final function __destruct()
    {
        if ($this->isDisposed() === true) {
            return;
        }
        $this->dispose(false);
    }
}