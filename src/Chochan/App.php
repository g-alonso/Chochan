<?php

/**
* This file is part of Chochan framework
* 
* @author Gabriel Alonso <gbr.alonso@gmail.com>
* @copyright 2014
* @license     WTFPL - http://www.wtfpl.net/txt/copying/
*
*/

namespace Chochan;

use Chochan\Dispatcher\Dispatch;
use Chochan\Routing\Router;
use Chochan\Http\Response;
use Chochan\Http\Request;
use Chochan\Exception\DispatchException;

/**
 * App class
 *
 * Application of Chochan Components 
 */
class App
{
    /**
     * 
     * Dispatch
     *
     * @var \Chochan\Dispatcher\Dispatch
     * 
     */
    private $dispatch;

    /**
     * 
     * Router
     * 
     * @var \Chochan\Routing\Router
     * 
     */
    private $router;

    /**
     * 
     * Response
     * 
     * @var \Chochan\Http\Response
     * 
     */
    private $response;

    /**
     * 
     * Request
     * 
     * @var \Chochan\Http\Request
     * 
     */
    private $request;

    /**
     * Consturctor
     * 
     * @param \Chochan\Di\Container $container Expects di container
     * 
     */
    public function __construct(Dispatch $dispatch, Router $router, Response $response, Request $request)
    {
        //delegate exception and error to app
        set_error_handler(array($this, 'handleError'));
        set_exception_handler(array($this, 'handleException'));

        $this->dispatch = $dispatch;
        $this->router = $router;
        $this->response = $response;
        $this->request = $request;
    }

    /**
     * Custom error handler. Converts errors into exceptions.
     *
     * @param int $errno Error number
     * @param int $errstr Error string
     * @param int $errfile Error file name
     * @param int $errline Error file line number
     * @throws \ErrorException
     */
    public function handleError($errno, $errstr, $errfile, $errline)
    {
        if ($errno & error_reporting()) {
            throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
        }
    }

    /**
     * Exception handler
     *
     * @param \Exception $e Thrown exception
     */
    public function handleException(\Exception $e)
    {
        if (is_a($e, 'Chochan\Exception\DispatchException')) {
            $this->notFound();
        } else {
            $this->error($e);
        }
    }


    /**
     * Sends an HTTP 500 response.
     *
     * @param \Exception Thrown exception
     */
    public function error(\Exception $e)
    {
        $msg = sprintf(
            '<h1>500 Internal Server Error</h1>'.
            '<h3>%s (%s)</h3>'.
            '<pre>%s</pre>',
            $e->getMessage(),
            $e->getCode(),
            $e->getTraceAsString()
        );

        try {

            $this->response->status(500)->write($msg)->send();
        } catch (\Exception $ex) {
            exit($msg);
        }
    }

    /**
     * Sends an HTTP 404 response when a URL is not found.
     */
    public function notFound()
    {
        $this->response->status(404)
        ->write(
            '<h1>404 Not Found</h1>'.
            '<h3>The page you have requested could not be found.</h3>'.
            str_repeat(' ', 512)
        )->send();
    }

    /**
     * Register new route
     * 
     * @param array $params [route, callback]
     * 
     * @return \Chochan\Routing\Router
    */
    public function route($params)
    {
        return $this->router
                        ->register($params[0], $params[1]);
    }

    /**
     * Set before route execution 
     * 
     * @param Closure $closure closure
     * 
     * @return void
     */
    public function before($closure)
    {
        $this->dispatch
                ->setPreHook($closure);
    }

    /**
     * Set after route execution
     * 
     * @param Closure $closure closure
     * 
     * @return void
     */
    public function after($closure)
    {
        $this->dispatch
                ->setPostHook($closure);
    }

    /**
     * Wake up Chochan!
     * 
     * @return void
    */
    public function oink()
    {
        $routemap = $this->router
                            ->match(
                                $this->request->getMethod(),
                                $this->request->getUri()
                            );
        $this->dispatch
                ->dispatch($routemap);
    }
}
