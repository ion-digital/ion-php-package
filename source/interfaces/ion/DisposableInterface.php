<?php

namespace Ion;


/**
 * Allows a class to gracefully dispose of its resources.
 *
 * @author Justus
 */
interface DisposableInterface {

    function destroy(): void;

}
