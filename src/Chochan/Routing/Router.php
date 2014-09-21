<?php

/**
* Router.php 
*
* This file is part of Chochan framework
* 
* @author    Gabriel Alonso <gbr.alonso@gmail.com>
* @copyright 2014
* @license   WTFPL - http://www.wtfpl.net/txt/copying/
* 
*/

namespace Chochan\Routing;

/*
 * Router class
 *
 * Route Manager
 *
*/
class Router
{
    /**
     * 
     * Registered Routes
     * 
     * @var array
     * 
     */
    private $routes = array();


    /**
     * 
     * Base dir
     * 
     * @var string
     * 
     */
    private $baseDir = null;


    /**
     * Constructor
     *
     * @param string $baseDir Base dir
     *
     * @return \Chochan\Routing\Router
     */
    public function __construct($baseDir = null)
    {
        $this->baseDir = $baseDir;
    }

    /**    
     * Set base dir
     * 
     * @param string $baseDir Base dir 
     * 
     * @return void     
     */
    public function setBaseDir($baseDir)
    {
        $this->baseDir = $baseDir;
    }

    /**     
     * Get base dir
     *     
     * @return string
     */
    public function getBaseDir()
    {
        return (is_null($this->baseDir)) ? $_SERVER['SCRIPT_NAME'] : $this->baseDir;
    }

    /**
     * Register a new route
     * 
     * @param string $routePattern Route pattern
     * @param \Closure $closure closure
     * 
     * @return \Chochan\Routing\Route
     */
    public function register($routePattern, \Closure $closure)
    {
        $route = new Route($this->getBaseDir().$routePattern);
        $route->setClosure($closure);

        $this->routes[] = $route;

        return $this->routes[count($this->routes) - 1];
    }

    /**
     * Check if the url, matches with a registered route
     * 
     * @param string $httpMethod Http method
     * @param string $url Url
     * 
     * @return array with Closure and params or null if not match
     * 
     */
    public function match($httpMethod, $url)
    {
        foreach ($this->routes as $name => $route) {
            if ($route->match($httpMethod, $url)) {
                return $route->getMatchedStuff();
            }
        }

        return null;
    }
}
