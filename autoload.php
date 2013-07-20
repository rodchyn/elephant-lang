<?php

require_once __DIR__ . '/src/Autoload.php';

if(empty($loader) or !($loader instanceof Composer\Autoload\ClassLoader))
{
    throw new \RuntimeException('Loader at this place must be instance of Composer\Autoload\ClassLoader');
}

$autoLoader = new Rodchyn\ElephantLang\Autoload($loader);
\spl_autoload_register(array($autoLoader, 'loadClass'));
