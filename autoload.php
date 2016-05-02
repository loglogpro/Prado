<?php
/**
 * set class auto load path
 * @author    Mrqi<https://github.com/mrqi>
 */

namespace Prado;

class AutoLoader
{
    public static function init($className)
    {
        $className = preg_replace('/^' . __NAMESPACE__ . '\\/', '', $className);
        $classFile = str_replace('\\', DIRECTORY_SEPARATOR, $className);
        require PRADO_PATH . DIRECTORY_SEPARATOR . $classFile;
    }
}

spl_autoload_register('AutoLoader::init');