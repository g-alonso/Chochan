<?php

/**
* This file is part of Chochan framework
* 
* @author    Gabriel Alonso <gbr.alonso@gmail.com>
* @copyright 2015
* @license   WTFPL - http://www.wtfpl.net/txt/copying/
* 
*/

namespace Chochan\Dispatcher;

/**
 * Dispatch class test
 * 
*/
class DispatchTest extends \PHPUnit_Framework_TestCase
{

    private $dispatcher;

    protected function setUp()
    {
        parent::setUp();

        $this->dispatcher = new \Chochan\Dispatcher\Dispatch;
    }

    /**
    * @expectedException Chochan\Exception\DispatchException
    */
    public function testTemplateException()
    {
        $this->dispatcher->dispatch(null);
    }


    public function testDispatch()
    {
        $this->dispatcher->setPreHook(array(0 => function(){
            echo "1";
        }));

        $this->dispatcher->setPostHook(array(0 => function(){
            echo "3";
        }));

        ob_start();
        
        $this->dispatcher->dispatch(
            array(
                0 => function(Dispatch $dtmp, $number){
                    echo $number;
                }, 
                1 => array("number" => "2")
            )
        );

        $r = ob_get_clean();

        $this->assertEquals("123", $r);
    }
}
