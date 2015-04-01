<?php

/**
* This file is part of Chochan framework
* 
* @author    Gabriel Alonso <gbr.alonso@gmail.com>
* @copyright 2014
* @license   WTFPL - http://www.wtfpl.net/txt/copying/
* 
*/

namespace Chochan\Routing;

/**
 * Routing class test
 * 
*/
class RoutingTest extends \PHPUnit_Framework_TestCase
{
    protected $router;

    protected function setUp()
    {
        parent::setUp();
    
        $this->router = new Router();

        $this->router->setBaseDir("/");

        $this->router->register("greeting/:name/:lastName?", function ($name, $lastName = "") {
            return "Hello $name $lastName!!!";
        })->method("GET");
    }

    public function testRoutingMatch()
    {
        $routemap = $this->router->match("GET", "/greeting/chochan/framework");

        $this->assertEquals("Hello chochan framework!!!", call_user_func_array($routemap[0], array_values($routemap[1])));
    }

    public function testRoutingNotMatch()
    {
        $routemap = $this->router->match("POST", "/greeting/chochan/framework!");

        $this->assertEquals(null, $routemap);
    }
}
