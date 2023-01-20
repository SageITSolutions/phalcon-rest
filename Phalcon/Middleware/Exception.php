<?php
namespace Phalcon\Middleware;

use Phalcon\Events\Event;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

/**
 * Exception Middleware
 */
class Exception implements MiddlewareInterface
{
    /**
     * Processes all exceptions as JSON response
     * @param Event $event
     * @param Micro $app
     * @param $exception
     * @return bool
     */
    public function beforeException(Event $event, Micro $app, $exception)
    {
        $handler = new \Phalcon\Exception\Handler(false);
        \Phalcon\Exception\Handler::convertException($exception);

        $app
            ->response
            ->setStatusCode($exception->getCode())
            ->setJsonContent($handler->getJSON($exception))
            ->send();

        return false;
    }

    /**
     * @param Micro $app
     *
     * @returns bool
     */
    public function call(Micro $app)
    {
        return true;
    }
}