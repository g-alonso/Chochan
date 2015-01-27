<?php

/**
* This file is part of Chochan framework
* 
* @author    Gabriel Alonso <gbr.alonso@gmail.com>
* @copyright 2014
* @license   WTFPL - http://www.wtfpl.net/txt/copying/
* 
*/

namespace Chochan\Http;

/**
 * Request class test
 * 
*/
class RequestTest extends \PHPUnit_Framework_TestCase
{
    protected $request;

    protected function setUp()
    {
        parent::setUp();
    
        $this->request = new Request();
    }

    public function testIsAjax()
    {
        $this->assertEquals(false, $this->request->isAjax());
    }
}
