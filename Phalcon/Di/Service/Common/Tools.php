<?php
namespace Phalcon\Di\Service\Common;

class Tools implements \Phalcon\Di\ServiceProviderInterface
{

    public function register(\Phalcon\Di\DiInterface $di): void
    {
        $di->setShared(
            'tools',
            function () {
                return new \Phalcon\Common\Tools();
            }
        );
    }
}
?>