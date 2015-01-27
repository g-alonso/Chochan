<?php

/**
 * This file is part of Chochan framework
 *
 * @author    Gabriel Alonso <gbr.alonso@gmail.com>
 * @copyright 2015
 * @license   WTFPL - http://www.wtfpl.net/txt/copying/
 *
 */

namespace Chochan\Di;

/**
 * Container class test
 *
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    protected $di;

    protected function setUp()
    {
        parent::setUp();

        $this->di = new Container();
    }

    /**
     * Test has service
     */
    public function testHasService()
    {
        $this->di->set("dep", function(){
            return "Hello!";
        });

        $this->assertEquals($this->di->hasService("dep"), true);
    }

    /**
     * Test new instance
     */
    public function testNewInstance()
    {
        $this->di->set("stdClass", new \stdClass());

        $class = $this->di->newInstance("stdClass");

        $this->assertEquals("stdClass", get_class($class));
    }

    /**
     * Test new instance closure
     */
    public function testNewInstanceClosure()
    {

        $container = $this->di;

        $this->di->set("\Chochan\Http\Request", function () use($container) {
            return $container->newInstance("\Chochan\Http\Request");
        });

        $this->di->set("config", function () use($container) {
            return array("a" => "b");
        });

        $container->dependencies["\Chochan\Http\Request"] = array(
            'config' => function() use($container) {
                return $container->get('config');
            }
        );

        $class = $this->di->newInstance("\Chochan\Http\Request");

        $this->assertEquals("Chochan\Http\Request", get_class($class));
    }
}