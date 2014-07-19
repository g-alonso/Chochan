<?php

/**
* Loader.php 
*
* This file is part of Chochan framework
* 
* @author    Gabriel Alonso <gbr.alonso@gmail.com>
* @copyright 2014
* @license   WTFPL - http://www.wtfpl.net/txt/copying/
*
*/

namespace Chochan\Autoload;

/**
 * Loader class with PSR-0 compliant
 * 
 * This class provides class autoloading
 *  
 * @todo PSR-4
*/
class Loader
{
    /**
     * 
     * Paths to search
     * 
     * @var array
     * 
     */
    static protected $paths = array();
    
    /**
     * Run loader
     * 
     * @param array $paths autoload directories
     * 
     * @return boolean
    */
    public static function run(array $paths = array())
    {
        static::$paths = array_merge(static::$paths, $paths);
        return spl_autoload_register('Chochan\Autoload\Loader::load');
    }
    
    /**
     * Load class in registred directories
     *
     * @param string $class class name
     * 
     * @return void
     * @throws Exception if can't load the class
     */
    public static function load($class)
    {
        $class_file = str_replace(array('\\', '_'), '/', $class).'.php';
        
        foreach (static::$paths as $dir) {
            $file = $dir.'/'.$class_file;

            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }
}
