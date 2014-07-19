<?php

/**
* Route.php 
*
* This file is part of Chochan framework
* 
* @author    Gabriel Alonso <gbr.alonso@gmail.com>
* @copyright 2014
* @license   WTFPL - http://www.wtfpl.net/txt/copying/
*
*/

namespace Chochan\Routing;

/**
 * Route class
 * 
 * Represents a route
 * 
*/
class Route
{

    /**
     * 
     * Closure to execute
     * 
     * @var mixed
     * 
     */
    private $closure;

    /**
     * 
     * Pattern to match
     * 
     * @var array
     * 
     */
    private $pattern;

    /**
     * 
     * HTTP method
     * 
     * @var array
     * 
     */
    private $closureMethod;

    /**
     * 
     * Matched Stuff
     * 
     * @var ???
     * 
     */
    private $matchedSuff;


    /**
     * 
     * Default HTTP methods
     * 
     * @var array
     * 
     */
    protected $defaultMethods = array('GET','POST','DELETE','PUT');


    /**
     * Constructor
     * 
     * @param string $routePattern routePattern
     *  
     */
    public function __construct($routePattern)
    {
        $this->pattern = $routePattern;
    }


    /**
     * Set closure of route
     * 
     * @param Closure closure
     * 
     * @return void
     * 
    */
    public function setClosure($closure)
    {
        $this->closure = $closure;

        foreach ($this->defaultMethods as $method) {
            $this->closureMethod[$method] = $closure;
        }
    }

    /**
     * Sets HTTP methods allowed
     * 
     * @param string $methods Http methods GET|POST|DELETE
     * 
     * @return $this
     */
    public function method($methods)
    {
        unset($this->closureMethod);

        $methodmap = array_map('strtoupper', (array) explode("|", $methods));

        foreach ($methodmap as $v) {
            if (in_array($v, $this->defaultMethods)) {
                $this->closureMethod[$v] = $this->closure;
            }
        }

        return $this;
    }

    /**
     * Check if the method and request url matchs
     * 
     * @param string $httpMethod Http methods
     * @param string $requestUrl Request url
     * 
     * @return $this
     */
    public function match($httpMethod, $requestUrl)
    {
        $match = false;

        if (isset($this->closureMethod[$httpMethod])) {
            \preg_match($this->compile(), $requestUrl, $matches);

            if (!empty($matches)) {

                $this->matchedSuff = array(
                    $this->closureMethod[$httpMethod],
                    $this->cleanMatches($matches)
                );

                $match = true;
            }
        }

        return $match;
    }

    /**
     * Get resolved     
     * 
     * @return array
     */
    public function getMatchedStuff()
    {
        return $this->matchedSuff;
    }

    /**
     * Return if url has a variable
     * 
     * @return bool
     */
    private function isStatic()
    {
        return \strpos($this->pattern, ":") === false;
    }

    /**
     * Compile Url
     * 
     * @return string Compiled url
     */
    private function compile()
    {
        if ($this->isStatic()) {
            $this->compiled = '~^'.$this->pattern.'$~';
            return $this->compiled;
        }

        $compiled = $this->pattern;
        foreach ($this->getSegments($compiled) as $segment) {
            $compiled = \str_replace($segment['token'], $segment['regex'], $compiled);
        }

        $this->compiled = "~^{$compiled}$~";
        return $this->compiled;
    }

    /**
     * Get segment
     * 
     * @param string $pattern Pattern
     * 
     * @return array Segments
     */
    private function getSegments($pattern)
    {
        $segments = array();
        $parts = \explode("/", ltrim($pattern, "/"));

        foreach ($parts as $segment) {
            if (\strpos($segment, ":") !== false) {
                $segments[] = $this->parseSegment($segment);
            }
        }

        return $segments;
    }

    /**
     * Parse segment
     * 
     * @param string $segment Segment
     * 
     * @return array 
     */
    private function parseSegment($segment)
    {
        $optional = false;

        list($regex, $name) = \explode(":", $segment);

        if (\substr($name, -1) === "?") {
            $name = \substr($name, 0, -1);
            $optional = true;
        }

        if ($regex === "") {
            $regex = "[^\/]+";
        }

        $regex = "/(?P<{$name}>{$regex})";

        if ($optional) {
            $regex = "(?:{$regex})?";
        }

        return array(
            'segment' => $segment,
            'token' => "/".$segment,
            'name' => $name,
            'regex' => $regex,
            'optional' => $optional
        );
    }
    
    /**
     * Clean matches
     * 
     * @param array $matches Matches
     * 
     * @return array 
     * 
     */
    protected function cleanMatches(array $matches)
    {
        $named = array();
        foreach ($matches as $key => $value) {
            if (!is_int($key)) {
                $named[$key] = $value;
            }
        }

        return $named;
    }
}
