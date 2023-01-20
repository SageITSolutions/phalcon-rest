<?php
namespace Phalcon\Encryption\Security\JWT\Validator;

use \Phalcon\Encryption\Security\JWT\Exceptions\ValidatorException;
use \Phalcon\Encryption\Security\JWT\Token\Token;

class Dynamic extends \Phalcon\Encryption\Security\JWT\Validator
{
    protected ?string $validator;

    protected $extended_errors;

    /**
     * Calls parent JWT Validator and extends with custom Validator Class
     * @param Token $token to validate
     * @param string|null $validator (Custom Validator Class)
     * @param int|null $timeShift allowable offset (delay)
     */
    public function __construct(Token $token, string $validator = null, int $timeShift = null){
        parent::__construct($token, $timeShift);
        $this->validator = $validator;
        $this->extended_errors = [];
    }
    public function validateCallback($method, Token $token)
    {
        $result = call_user_func($this->validator . '::validate' . $method,$token);
        if($result){
            $this->extended_errors[] = "Validation: " . str_replace("Validation: ", "", $result);
        }
    }

    public function hasErrors(\Phalcon\Logger\Logger $logger = null): bool
    {
        $this->extended_errors = array_merge($this->extended_errors, $this->getErrors());
        
        if(!empty($this->extended_errors)){
            $error_message = "";
            foreach($this->extended_errors as $err){
                if ($error_message != "") {
                    $error_message .= "\n";
                }
                if ($logger) {
                    $logger->error($err);
                }
                $error_message .= $err;
            }
            throw new ValidatorException($error_message);
        }
        return false;
    }
}
?>