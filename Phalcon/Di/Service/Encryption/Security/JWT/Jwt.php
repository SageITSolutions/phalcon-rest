<?php
namespace Phalcon\Di\Service\Encryption\Security\JWT;

class Jwt implements \Phalcon\Di\ServiceProviderInterface
{

    public function register(\Phalcon\Di\DiInterface $di): void
    {
        $di->setShared(
            'jwt',
            function () {
                return new \Phalcon\Encryption\Security\JWT\Jwt();
            }
        );
    }
}
?>