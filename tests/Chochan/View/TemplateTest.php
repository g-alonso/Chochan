<?php

/**
* This file is part of Chochan framework
* 
* @author    Gabriel Alonso <gbr.alonso@gmail.com>
* @copyright 2015
* @license   WTFPL - http://www.wtfpl.net/txt/copying/
* 
*/

namespace Chochan\View;

/**
 * Template class test
 * 
*/
class TemplateTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    /**
    * @expectedException Chochan\Exception\TemplateException
    */
    public function testTemplateException()
    {
        Template::loadView("a", "b");
    }

    public function testTemplate()
    {
        $temp = tmpfile();
        fwrite($temp, "oink!");
        fseek($temp, 0);
        
        $file = stream_get_meta_data($temp);

        $templateReturn = Template::loadView($file["uri"], "");

        $this->assertEquals($templateReturn, "oink!");

        fclose($temp);
    }
}
