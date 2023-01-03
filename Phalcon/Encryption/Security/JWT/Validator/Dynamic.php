<?php
namespace Phalcon\Encryption\Security\JWT\Validator;

use \Phalcon\Encryption\Security\JWT\Exceptions\ValidatorException;
use \Phalcon\Encryption\Security\JWT\Token\Token;

class Dynamic extends \Phalcon\Encryption\Security\JWT\Validator
{
    protected ?string $validator;

    /**
     * Calls parent JWT Validator and extends with custom Validator Class
     * @param Token $token to validate
     * @param string|null $validator (Custom Validator Class)
     * @param int|null $timeShift allowable offset (delay)
     */
    public function __construct(Token $token, string $validator = null, int $timeShift = null){
        parent::__construct($token, $timeShift);
        $this->validator = $validator;
    }
    public function validateCallback($method, Token $token)
    {
        $result = call_user_func($this->validator . '::validate' . $method,$token);
        if($result){
            throw new ValidatorException(
                "Validation: " . $result
            );
        }
    }
}
?>