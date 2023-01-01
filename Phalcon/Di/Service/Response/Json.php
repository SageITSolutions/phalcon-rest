<?php
namespace Phalcon\Di\Service\Response;

class Json implements \Phalcon\Di\ServiceProviderInterface
{

    public function register(\Phalcon\Di\DiInterface $di): void
    {
        $di->setShared(
            'request',
            function () use ($di) {
                $response = new \Phalcon\Http\Response();
                $response->setContentType('application/json', 'utf-8');
                return $response;
            }
        );
    }
}
?>