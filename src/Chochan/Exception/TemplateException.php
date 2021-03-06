<?php

/**
* TemplateException.php 
*
* This file is part of Chochan framework
* 
* @author    Gabriel Alonso <gbr.alonso@gmail.com>
* @copyright 2014
* @license   WTFPL - http://www.wtfpl.net/txt/copying/
*
*/

namespace Chochan\Exception;

/**
 * TemplateException Class
 * 
 * @todo put description
 */
class TemplateException extends \Exception
{
    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
