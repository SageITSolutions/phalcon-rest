<?php
namespace Phalcon\Middleware;

use Phalcon\Events\Event;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

/**
 * Middleware to allow Cross-Origin Resource Sharing
 */
class CORS implements MiddlewareInterface
{
    /**
     * @param Event $event
     * @param Micro $app
     *
     * @returns bool
     */
    public function beforeHandleRoute(
        Event $event, 
        Micro $app
    ) {
        if ($app->request->getHeader('ORIGIN')) {
            $origin = $app
                ->request
                ->getHeader('ORIGIN')
            ;
        } else {
            $origin = '*';
        }

        $app
            ->response
            ->setHeader(
                'Access-Control-Allow-Origin', 
                $origin
            )
            ->setHeader(
                'Access-Control-Allow-Methods',
                'GET,PUT,POST,DELETE,OPTIONS'
            )
            ->setHeader(
                'Access-Control-Allow-Headers',
                'Origin, X-Requested-With, Content-Range, ' .
                'Content-Disposition, Content-Type, Authorization'
            )
            ->setHeader(
                'Access-Control-Allow-Credentials', 
                'true'
            )
        ;
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