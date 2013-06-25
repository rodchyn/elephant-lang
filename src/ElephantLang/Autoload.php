<?php

namespace ElephantLang;

use Composer\Autoload\ClassLoader;

class Autoload extends ClassLoader
{
    public static $composerAutoloader;

    public function __construct($loader)
    {
        static::$composerAutoloader = $loader;
    }

    /**
     * Loads the given class or interface.
     *
     * @param  string    $class The name of the class
     * @return bool|null True if loaded, null otherwise
     */
    public function loadClass($class)
    {
        if ($file = $this->findFile($class)) {
            include $file;

            return true;
        }
    }

    public function findFile($class)
    {
        // work around for PHP 5.3.0 - 5.3.2 https://bugs.php.net/50731
        if ('\\' == $class[0]) {
            $class = substr($class, 1);
        }

        if (false !== $pos = strrpos($class, '\\')) {
            // namespaced class name
            $classPath = strtr(substr($class, 0, $pos), '\\', DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            $className = substr($class, $pos + 1);
        } else {
            // PEAR-like class name
            $classPath = null;
            $className = $class;
        }

        $classPath .= strtr($className, '_', DIRECTORY_SEPARATOR) . '.elph';

        $prefixes = static::$composerAutoloader->getPrefixes();

        foreach ($prefixes as $prefix => $dirs) {
            if (0 === strpos($class, $prefix)) {
                foreach ($dirs as $dir) {
                    if (file_exists($dir . DIRECTORY_SEPARATOR . $classPath)) {
                        return $this->buildElephantFile($dir . DIRECTORY_SEPARATOR . $classPath);
                    }
                }
            }
        }

        foreach ($this->getFallbackDirs() as $dir) {
            if (file_exists($dir . DIRECTORY_SEPARATOR . $classPath)) {
                return $this->buildElephantFile($dir . DIRECTORY_SEPARATOR . $classPath);
            }
        }

        if ($this->getUseIncludePath() && $file = stream_resolve_include_path($classPath)) {
            return $this->buildElephantFile($file);
        }

        return $classMap[$class] = false;
    }

    public function buildElephantFile($file)
    {
        $file = new \SplFileInfo($file);
        $resultFile = $file->getPath() . DIRECTORY_SEPARATOR . $file->getBasename('.elph') . '.php';
        $rewriter = new Rewriter($resultFile);
        $rewriter->save();
        return $resultFile;
    }

}
