<?php
namespace Phalcon\Encryption\Security\JWT\Validator;

use \Phalcon\Encryption\Security\JWT\Exceptions\ValidatorException;
use \Phalcon\Encryption\Security\JWT\Token\Token;

class Callback
{
    protected string $class;
    protected string $method;
    protected string $error;

    public function __construct(string $class,string $method,string $error){
        $this->$class = $class;
        $this->$method = $method;
        $this->$error = $error;
    }

    public function __get($name) {
        if($this->$name) {
            return $this->$name;
        }
        return null;
    }

    public function __isset($name) {
        return isset($this->$name);
    }
}
?>