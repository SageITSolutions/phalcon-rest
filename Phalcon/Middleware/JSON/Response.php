<?php
namespace Phalcon\Middleware\JSON;

use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

/**
 * JSON Response Middleware
 */
class Response implements MiddlewareInterface
{
    /**
     * @param Micro $app
     * @returns bool
     */
   public function call(Micro $app) {
       $payload = [
           'code'    => 200,
           'status'  => 'success',
           'message' => '',
           'payload' => $app->getReturnedValue(),
       ];

       $app
           ->response
           ->setJsonContent($payload)
           ->send()
       ;
       return true;
   }
}