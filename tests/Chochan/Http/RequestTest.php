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

require_once 'PHPUnit/autoload.php';
require realpath(__DIR__.'/../../../src/Chochan/').'/autoload.php';

/**
 * Request class test
 * 
 * Represents a HTTP request test
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
        $this->assertEquals(true, $this->request->isAjax());
    }

    public function testGetProxyIpAddress()
    {
        $this->assertEquals(true, $this->request->getProxyIpAddress());
    }
}
