<?php

/*
 * This file is part of the CG library.
 *
 *    (C) 2011 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 */

spl_autoload_register(function($class)
{
    if (0 === strpos($class, 'Rodchyn\\ElephantLang\\')) {
        $path = __DIR__.'/../src/'.strtr(preg_replace('/^Rodchyn\\\ElephantLang\\\/', '', $class), '\\', '/').'.php';
        if (file_exists($path) && is_readable($path)) {
            require_once $path;

            return true;
        }
    }
});

require __DIR__ . '/../vendor/autoload.php';

