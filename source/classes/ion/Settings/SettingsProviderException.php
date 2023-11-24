<?php

/*
 * See license information at the package root in LICENSE.md
 */

namespace ion\Settings;

/**
 * Description of PackageException
 *
 * @author Justus
 */

use \Exception;
use \Throwable;
use \ion\Settings\SettingsException;

class SettingsProviderException extends SettingsException implements SettingsProviderExceptionInterface {
    
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null) {
        
        parent::__construct($message, $code, $previous);
    }
    
}
