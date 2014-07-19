<?php

/**
 *
 * This file is part of Chochan framework
 *
 * Chochan Framework
 * 
 *         _//|.-~~~~-,
 *       _/66  \       \_@
 *      (")_   /   /   |
 *        '--'|| |-\  /
 *            //_/ /_/
 *
 * @author Gabriel Alonso <gbr.alonso@gmail.com>
 * @copyright 2014
 * @license     WTFPL - http://www.wtfpl.net/txt/copying/
 *
*/

namespace Chochan;

use Chochan\App;

/**
 * Chochan class
 * 
 * This class is a static representer of the framework
 * 
 */
class Chochan
{
    /*
     * Version
     *
     * @var string version
    */
    public static $version = "0.0.1b";

    /**
     * 
     * Constructor dependences
     * 
     * @var Chochan\App
     * 
     */
    private static $app;

    /**
     * Get container
     *
     * @var Chochan\Di\Container
    */
    private static $container;

    /**
     * Initialize components
     * 
     * @param Chochan\Di\Container $container container
     * 
    */
    public static function wakeUp(\Chochan\Di\Container $container)
    {
        $container->set("Chochan\Routing\Router", function () use ($container) {
            return $container->newInstance("Chochan\Routing\Router");
        });

        $container->set("Chochan\Dispatcher\Dispatch", function () use ($container) {
            return $container->newInstance("Chochan\Dispatcher\Dispatch");
        });

        $container->set("Chochan\Http\Request", function () use ($container) {
            return $container->newInstance("Chochan\Http\Request");
        });

        $container->set("Chochan\Http\Response", function () use ($container) {
            return $container->newInstance("Chochan\Http\Response");
        });

        $container->set("Chochan\View\Template", function () use ($container) {
            return $container->newInstance("Chochan\View\Template");
        });

        $container->dependences["Chochan\App"] = array(
            'Chochan\Dispatcher\Dispatch' => $container->get('Chochan\Dispatcher\Dispatch'),
            'Chochan\Routing\Router' => $container->get('Chochan\Routing\Router'),
            'Chochan\Http\Response' => $container->get('Chochan\Http\Response'),
            'Chochan\Http\Request' => $container->get('Chochan\Http\Request')
        );

        self::$app = $container->newInstance("Chochan\App");
        self::$container = $container;
    }

    /**
     * Call Static
     * 
     * __callStatic() is triggered when invoking inaccessible 
     * methods in a static context.
     * 
     * The $name argument is the name of the method being called. 
     * 
     * The $arguments argument is an enumerated array containing 
     * the parameters passed to the $name'ed method. 
     * 
     * @param string $name Method name
     * @param array $params Params
     * 
     * @return mixed
    */
    public static function __callStatic($name, $params)
    {
        switch ($name) {
            case 'before':
                return self::$app->before($params);
                break;
            case 'after':
                return self::$app->after($params);
                break;
            case 'route':
                return self::$app->route($params);
                break;
            case 'getContainer':
                return self::$container;
                break;
            case 'oink':
                return self::$app->oink();
                break;
        }
    }
}
