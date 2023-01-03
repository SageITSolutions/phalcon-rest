<?php
namespace Phalcon\Middleware;

use Phalcon\Events\Event;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

/**
 * Not Found Middleware
 */
class NotFound implements MiddlewareInterface
{
    /**
     * @param Event $event
     * @param Micro $application
     *
     * @returns bool
     */
    public function beforeNotFound(Event $event, Micro $app)
    {
        $app
            ->response
            ->setStatusCode(404, 'Not Found')
            ->sendHeaders()
            ->setJsonContent([
            'code' => 404,
            'status' => 'error',
            'message' => "(" . $app->request->getMethod() . ": " . $app->request->getURI() . ") not found or error in request"             
            ])
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