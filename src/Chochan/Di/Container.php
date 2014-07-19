<?php

/**
* Container.php 
*
* This file is part of Chochan framework
* 
* @author    Gabriel Alonso <gbr.alonso@gmail.com>
* @copyright 2014
* @license   WTFPL - http://www.wtfpl.net/txt/copying/
*
*/

namespace Chochan\Di;

/**
 * 
 * Container class
 *
 * Dependency injection container 
 * 
 * @todo setter dependences
*/
class Container
{
    /**
	 * 
	 * Array of setted services
	 * 
	 * @var array
	 *
    */
    private $services = array();

    /**
     * Retains the actual service object instances.
     * 
     * @var array
     * 
     */
    protected $instances = array();

    /**
     * 
     * Constructor dependences
     * 
     * @var array
     * 
     */
    public $dependences = array();

    /**
     *  Get a service
     * 
     * @param string Service name
     *
     * @return object Service obj
     */
    public function get($service)
    {
        if (! $this->hasService($service)) {
            throw new \Exception("Service $service not found");
        }

        // already exists?
        if (! isset($this->instances[$service])) {
            
            $object = $this->services[$service];

            if ($object instanceof \Closure) {
                $object = $this->services[$service]->__invoke();
            }
            // save instance
            $this->instances[$service] = $object;
        }
        
        return $this->instances[$service];
    }

    /**
	 * Sets a service.
	 *
	 * @param string $service service key.
	 * @param object $val service obj
	 * 
	 * @return Chochan\Di\Container
    */
    public function set($service, $val)
    {
        if (!isset($this->services[$service])) {
            $this->services[$service] = $val;
        }

        return $this;
    }

    /**
	 * Creates and returns a new instance of a class using reflection.
	 * 
	 * @param string $className class name
	 * 
	 * @return object
    */
    public function newInstance($className)
    {
        $class = new \ReflectionClass($className);

        if (isset($this->dependences[$className])) {

            // check Closure's
            foreach ($this->dependences[$className] as $param => $value) {
                if ($value instanceof \Closure) {
                    $this->dependences[$className][$param] = $value();
                }
            }

            $instance = $class->newInstanceArgs(array_values($this->dependences[$className]));
        } else {
            $instance = $class->newInstance();
        }

        return $instance;
    }

    /**
	 * Retrieves if a particular service exists 
	 * 
	 * @param string $service Service name
	 * 
	 * @return bool
    */
    public function hasService($service)
    {
        return isset($this->services[$service]);
    }
}
