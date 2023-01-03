<?php
namespace Phalcon\Middleware\JSON;

use Phalcon\Events\Event;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

/**
 * JSON Request Middleware
 * Ensures appropriated JSON formatting before continuing
 */
class Request implements MiddlewareInterface
{
    /**
     * @param Event $event
     * @param Micro $app
     *
     * @returns bool
     */
    public function beforeExecuteRoute(Event $event, Micro $app) {
        $body = $app->request->getRawBody();
        if(!$body) return true;
        
        $_POST = json_decode($body);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \Phalcon\Exception\FormatException($body,'JSON');
            return false;
        }
        return true;
    }

    /**
     * @param Micro $app
     *
     * @returns bool
     */
    public function call(Micro $app){
        return true;
    }
}