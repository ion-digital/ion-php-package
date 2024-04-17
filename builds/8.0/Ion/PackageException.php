<?php
/*
 * See license information at the package root in LICENSE.md
 */
namespace Ion;

/**
 * Description of PackageException
 *
 * @author Justus
 */
use Exception;
use Throwable;
class PackageException extends Exception implements PackageExceptionInterface
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}