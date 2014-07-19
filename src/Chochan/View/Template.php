<?php
/**
* View.php 
*
* This file is part of Chochan framework
* 
* @author Gabriel Alonso <gbr.alonso@gmail.com>
* @copyright 2014 
* @license     WTFPL - http://www.wtfpl.net/txt/copying/
*
*/

namespace Chochan\View;

use Chochan\Exception\TemplateException;

/**
 * Viw class 
 * 
 * This class provides loading view support
 *  
 */
class Template
{
    /**
    * Load view
    * 
    * @param string $fileName name of file
    * @param string $location where is located
    * @param array $params variables to send to view
    *
    * @throws TemplateException if can not load file template
    * @return string 
    */
    public static function loadView($fileName, $location, $params = array())
    {
        if (file_exists($location.$fileName) == false) {
            throw new TemplateException(sprintf("Can't load file %s", $location.$fileName));
        }

        extract($params);
        
        ob_start();

        include $location.$fileName;
        
        $r = ob_get_clean();
       
        return $r;
    }
}
