<?php
namespace Phalcon\Encryption\Security\JWT;

use \Phalcon\Encryption\Security\JWT\Token\Parser;
use \Phalcon\Encryption\Security\JWT\Token\Token;
use \Phalcon\Encryption\Security\JWT\Validator\Dynamic as DynamicValidator;
use \Phalcon\Encryption\Security\JWT\Validator\Callback;
use \Phalcon\Encryption\Security\JWT\Builder;
use \Phalcon\Encryption\Security\JWT\Signer\Hmac;

class JWT extends \Phalcon\Di\Injectable{
    protected $options;

    public function __construct(\stdClass $options = null){
        $this->options          = $this->defaultOptions($options);
        $this->options->signer  = new Hmac();
    }

    protected function defaultOptions(\stdClass $options = null): \stdClass
    {
        $obj = (object) [
            "pass_key" => "S@g3!tS0lut!0ns",
            "issuer" => "https://myurl.com",
            "audience" => "https://myurl.com",
            "duration" => "+1 day",
            "subject" => "JWT Authorization"
        ];

        if ($options) {
            $obj->pass_key = $options->pass_key ?: $obj->pass_key;
            $obj->issuer = $options->issuer ?: $obj->issuer;
            $obj->audience = $options->audience ?: $obj->audience;
            $obj->duration = $options->duration ?: $obj->duration;
            $obj->subject = $options->subject ?: $obj->subject;
        }

        if ($this->config && $this->config->jwt) {
            $obj->pass_key = $this->config->jwt->pass_key ?: $obj->pass_key;
            $obj->issuer = $this->config->jwt->issuer ?: $obj->issuer;
            $obj->audience = $this->config->jwt->audience ?: $obj->audience;
            $obj->duration = $this->config->jwt->duration ?: $obj->duration;
            $obj->subject = $this->config->jwt->subject ?: $obj->subject;
        }

        return $obj;
    }

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
            ->setPassphrase($this->options->pass_key);
        
        foreach($claims as $claim => $value){
            $builder->addClaim($claim,$value);
        }

        $tokenObject = $builder->getToken();
        return $tokenObject->getToken();
    }

    public function parseToken(string $token): Token
    {
        $parser = new Parser();
        $tokenObject = $parser->parse($token);
    }

    public function validate(Token $token, array $validationCallbacks = null): bool 
    {
        $time = $this->_getTimeStamps();
        $validator = new DynamicValidator($token, $validationCallbacks, 100); // allow for a time shift of 100

        $validator
            ->validateAudience($this->options->audience)
            ->validateExpiration($time->issued)
            ->validateIssuedAt($time->issued)
            ->validateIssuer($this->options->issuer)
            ->validateNotBefore($time->notBefore)
            ->validateSignature($this->options->signer, $this->pass_key);
        
        foreach($validationCallbacks as $cb){
            $validator->validateCallback(new Callback(
                $cb['class'],
                $cb['method'],
                $cb['error']
            ));
        }
        
        return true;
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