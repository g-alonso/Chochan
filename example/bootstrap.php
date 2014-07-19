<?php

/**
* Bootstrap
* 
* @author     Gabriel Alonso <gbr.alonso@gmail.com>
* @copyright  2014
* @license    WTFPL - http://www.wtfpl.net/txt/copying/
*/

require '../src/Chochan/autoload.php';

$container = new \Chochan\Di\Container;

Chochan\Chochan::wakeUp($container);
