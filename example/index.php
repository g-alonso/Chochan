<?php

/**
* index.php 
*
* Setting routes
* 
* @author    Gabriel Alonso <gbr.alonso@gmail.com>
* @copyright 2014
* @license   WTFPL - http://www.wtfpl.net/txt/copying/
*/

require 'bootstrap.php';

// setting before hook
Chochan\Chochan::before(function (Chochan\View\Template $template) {
    echo round(microtime(true) * 1000)."<br /><br />";
});

// setting after hook
Chochan\Chochan::after(function (Chochan\View\Template $template) {
    echo "<br /><br />".round(microtime(true) * 1000);
});

Chochan\Chochan::route('/', function (Chochan\View\Template $view, Chochan\Http\Response $response) {
    $response->write($view::loadView("test.html", __DIR__."/"))->send();
});

Chochan\Chochan::route('/helloworld', function (Chochan\Http\Response $response) {
    $response->write("Hello World!")->send();
});

//Wake Up chochan!
Chochan\Chochan::oink();
