<?php
namespace Phalcon\Encryption\Security\JWT;

use \Phalcon\Encryption\Security\JWT\Token\Parser;
use \Phalcon\Encryption\Security\JWT\Token\Token;
use \Phalcon\Encryption\Security\JWT\Validator\Dynamic as DynamicValidator;
use \Phalcon\Encryption\Security\JWT\Validator\Callback;
use \Phalcon\Encryption\Security\JWT\Builder;
use \Phalcon\Encryption\Security\JWT\Signer\Hmac;

class Jwt extends \Phalcon\Di\Injectable{
    protected $options;

    /**
     * Generates new JWT parser with optional key overrides
     * Config settings will override options object
     * @param \stdClass|null $options
     */
    public function __construct(\stdClass $options = null){
        $this->options          = $this->defaultOptions($options);
        $this->options->signer  = new Hmac();
        $this->setValidateMethods();
    }

    public function getOptions(){
        return $this->options;
    }

    /**
     * Accepts options object to define key settings
     * Overridden by config object if present in DI
     * @param \stdClass|null $options
     * @return \stdClass
     */
    protected function defaultOptions(\stdClass $options = null): \stdClass
    {
        $obj = (object) [
            "key"              => "S@g3!tS0lut!0ns",
            "issuer"           => "https://myurl.com",
            "audience"         => "https://myurl.com",
            "duration"         => "+1 day",
            "subject"          => "JWT Authorization",
            "validator"        => null,
            "validate_methods" => null
        ];

        if ($options) {
            $obj->key       = $options->pass_key ?: $obj->key;
            $obj->issuer    = $options->issuer ?: $obj->issuer;
            $obj->audience  = $options->audience ?: $obj->audience;
            $obj->duration  = $options->duration ?: $obj->duration;
            $obj->subject   = $options->subject ?: $obj->subject;
            $obj->validator = $options->validator ?: $obj->validator;
        }

        if ($this->config && $this->config->jwt) {
            $obj->key       = $this->config->jwt->key ?: $obj->key;
            $obj->issuer    = $this->config->jwt->issuer ?: $obj->issuer;
            $obj->audience  = $this->config->jwt->audience ?: $obj->audience;
            $obj->duration  = $this->config->jwt->duration ?: $obj->duration;
            $obj->validator = $this->config->jwt->validator ?: $obj->validator;
        }

        return $obj;
    }

    /**
     * Override defined validator from config or options array
     * @param string $Class
     * @param array|null $methods
     * @return void
     */
    public function setValidator(string $Class = null, array $methods = null){
        $this->options->validator = $Class;
        $this->setValidateMethods($methods);
    }

    /**
     * If $methods provided, sets array of validator methods to call.
     * Otherwise, parses validator class for all methods.  
     * @param array|null $methods
     * @return void
     */
    public function setValidateMethods(array $methods = null){
        $methodLists = [];
        if(class_exists($this->options->validator)){
            if(empty($methods)) {
                $methods = get_class_methods($this->options->validator);
            }
            foreach($methods as $method){
                $methodLists[] = str_replace('validate','',$method);
            }
        }
        $this->options->validator_methods = $methodLists;
    }

    /**
     * Generates new JWT token given provided claims and key options
     * Array can be empty ([]) if no additional claims are required
     * @param array $claims
     * @return mixed
     */
    public function generateToken(array $claims){
        $builder = new Builder($this->options->signer);
        $time = $this->_getTimeStamps();

        $builder
            ->setAudience($this->options->audience)  
            ->setContentType('application/json')
            ->setExpirationTime($time->expires)
            ->setIssuedAt($time->issued)
            ->setIssuer($this->options->issuer) 
            ->setNotBefore($time->notBefore)
            ->setSubject($this->options->subject)
            ->setPassphrase($this->options->key);
        
        foreach($claims as $claim => $value){
            $builder->addClaim($claim,$value);
        }

        $tokenObject = $builder->getToken();
        return $tokenObject->getToken();
    }

    public function parseToken(string $token): Token
    {
        $parser = new Parser();
        return $parser->parse($token);
    }

    public function validate(Token $token): bool 
    {
        $time = $this->_getTimeStamps();
        $validator = new DynamicValidator($token, $this->options->validator, 100);

        $validator
            ->validateAudience($this->options->audience)
            ->validateExpiration($time->issued)
            ->validateIssuedAt($time->issued)
            ->validateIssuer($this->options->issuer)
            ->validateNotBefore($time->notBefore)
            ->validateSignature($this->options->signer, $this->options->key);

        foreach($this->options->validator_methods as $validation_callback){
            $validator->validateCallback($validation_callback, $token);
        }

        return $validator->hasErrors($this->logger);
    }

    protected function _getTimeStamps(){
        $now        = new \DateTimeImmutable();
        return (object) [
            'issued'    => $now->getTimestamp(),
            'notBefore' => $now->modify('-1 minute')->getTimestamp(),
            'expires'   => $now->modify($this->options->duration)->getTimestamp()
        ];
    }
}
?>