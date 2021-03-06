<?php

/**
* Dispatch.php 
*
* This file is part of Chochan framework
* 
* @author    Gabriel Alonso <gbr.alonso@gmail.com>
* @copyright 2014
* @license   WTFPL - http://www.wtfpl.net/txt/copying/g/
*
*/

namespace Chochan\Dispatcher;

use Chochan\Exception\DispatchException;

/**
 * Dispatch Class
 *
 * Executes Something
 * 
 * @todo improve class description, implements pre/post filters by route
 */
class Dispatch
{
    /**
     * Pre execution hooks
     * 
     * @var array
     * 
     */
    private $preHooks = array();
    
    /**
     * Post execution filters
     * 
     * @var array
     * 
    */
    private $postHooks = array();

    /**
     * Execute
     *
     * @param $resolvedRoute
     * @throws DispatchException
     *
     * @return void
     */
    public function dispatch($resolvedRoute)
    {
        if ($resolvedRoute == null) {
            throw new DispatchException("Can not dispatch null value");
        }

        $executionChain = array_merge(
            $this->preHooks,
            array($resolvedRoute),
            $this->postHooks
        );
        
        array_walk($executionChain, array($this, 'executeClosure'));
    }

    /**
     * Set pre execution hook
     * 
     * @param \Closure $closure closure
     * 
     * @return void
     */
    public function setPreHook($closure)
    {
        $this->preHooks[] = $closure;
    }

    /**
     * Set post execution hook
     * 
     * @param \Closure $closure closure
     * 
     * @return void
     */
    public function setPostHook($closure)
    {
        $this->postHooks[] = $closure;
    }

    /**
     * Execute
     *
     * @param array $resolvedRoute Array([0] => Closure Object, [1] => Request params)
     *
     * @return void
    */
    private function executeClosure($resolvedRoute)
    {
        $reflection = new \ReflectionFunction($resolvedRoute[0]);
        $arguments  = $reflection->getParameters();

        $args = array();

        // Get dependencies by reflection
        foreach ($arguments as $arg) {
            if ($arg->getClass() instanceof \ReflectionClass) {
                $object = $arg->getClass()->getName();
                $args[] = new $object;
            }
        }

        if (isset($resolvedRoute[1])) {
            // Insert custom params
            foreach ($resolvedRoute[1] as $paramName => $paramValue) {
                $args[$paramName] = $paramValue;
            }
        }

        call_user_func_array($resolvedRoute[0], array_values($args));
    }
}
