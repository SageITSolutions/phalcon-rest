<?php
namespace Phalcon\Encryption\Security\JWT\Validator;

use \Phalcon\Encryption\Security\JWT\Exceptions\ValidatorException;
use \Phalcon\Encryption\Security\JWT\Token\Token;

class Dynamic extends \Phalcon\Encryption\Security\JWT\Validator
{
    public function validateCallback(Callback $callback,$error)
    {
        if(!call_user_func(array($callback->class, $callback->method))){
            throw new ValidatorException(
                "Validation: " . $callback->error
            );
        }
    }
}
?>