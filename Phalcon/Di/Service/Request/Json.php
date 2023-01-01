<?php
namespace Phalcon\Di\Service\Request;

class Json implements \Phalcon\Di\ServiceProviderInterface
{

    public function register(\Phalcon\Di\DiInterface $di): void
    {
        $di->setShared(
            'request',
            function () use ($di) {
                $request = new \Phalcon\Http\Request\Json();
                return $request;
            }
        );
    }
}
?>