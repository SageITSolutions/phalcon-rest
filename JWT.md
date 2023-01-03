<div align="center">
  <!-- PROJECT LOGO -->
  <a href="https://github.com/SageITSolutions/phalcon-rest">
    <img src=".readme/logo.png" alt="Logo" width="445" height="120">
  </a>

  <h1 align="center">Phalcon REST -> JWT Encryption</h1>

  <p>
    Tool for processing JWT with dynamic settings, extensible claims, and custom validation options.
  </p>

**[Main Readme »](README.md)**

</div>

<!-- TABLE OF CONTENTS -->

## Table of Contents

- [Usage](#usage)
  - [Using the service](#using-the-service)
  - [Setting Options](#setting-the-options)
    - [Config](#config-method)
    - [Custom Object](#custom-object-method)
- [Creating Tokens](#creating-tokens)
  - [Generating a new token](#new-token)
  - [Reading a token string](#reading-a-token)
- [Validation](#validation)
  - [Calling Validation](#calling-validation)
  - [Custom Validation](#custom-validation)
    - [Creating a class](#creating-a-validation-class)
    - [Class methods](#creating-a-validation-method)
    - [Working Exmaple](#full-working-example-class)
    - [Limiting Validation](#limiting-validation-methods)

# Usage
Usage of the JWT Encryption Library is quite extensible but requires the presense of the service and accessible fully qualified Namespaced validation class if desired.

## Using the service
Make sure the service is included. See `Implementing a Service` of the [Main Readme](#README.md) for config based usage.

**Implemented Manually**
```php
$di->register(new \Phalcon\Di\Service\Encryption\Security\JWT\Jwt());
```

## Setting the Options
Options can be configured by overriding the Jwt service and providing an options array, or using the config object to define JWT encryption. _**The config option is the recommended method**_.

### Config Method
```yaml
jwt:
  #16 char minimum encryption with special characters
  key: My_16_D!g3t_Key$tring 
  issuer: https://mydomain.com
  audience: https://mydomain.com
  duration: +1 day
  subject: My Fancy Subject Line
  validator: App\Library\Security\cValidator #optional
```

### Custom Object Method
This method is only harder in that a custom service has to be created or a specific instance of the JWT class instantiated. This may be the desired method when more than one JWT service is required.
```php
$jwt = new \Phalcon\Encryption\Security\JWT\JWT(
  (object)[
    "key"       => "My_16_D!g3t_Key\$tring" 
    "issuer"    => "https://mydomain.com"
    "audience"  => "https://mydomain.com"
    "duration"  => "+1 day"
    "subject"   => "My Fancy Subject Line"
    "validator" => "App\Library\Security\cValidator" //Optional
  ]
);
```
#### **Custom Service** ####
Encapsulate the JWT object in your own service:
```php
class MyCustomJwt_Service implements \Phalcon\Di\ServiceProviderInterface
{

    public function register(\Phalcon\Di\DiInterface $di): void
    {
        $di->setShared(
            'jwt',
            function () {
                //return new JWT using Custom Object above 
            }
        );
    }
}
```

# Creating Tokens
Tokens can be easily created and parsed through the use of iterators under the covers.

## New Token
A generic Token can be created with no data.  Adding data in the form of claims is easily completed with a key, value array.

```php
\\Gemeric Token
$token = $this->jwt->generateToken();
\\Token including data
$token = $this->jwt->generateToken([
  "name"  => "Bob",
  "age" => 23,
  "gender"  => "male"
]);
```

## Reading a Token
An encapsulated token can be easily parsed with claims available using default `getClaims` functionality.

```php
$tokenObj = $this->jwt->parseToken($stringtoken);
$name = $tokenObj->getClaims()->get('name');
$age = $tokenObj->getClaims()->get('age');
$gender = $tokenObj->getClaims()->get('gender');
/**Results:
 *    Name: 'Bob'
 *    Age: 23
 *    Gender: 'male'
 */
```

# Validation
Out of the box validation checks the minimum attributes to calidate a provided parsed token.

**Default Validation**
- Audience
- Expiration Time
- Issued Time
- Issue entity
- Time not before specific time
- Signature

Additional Validation can be created through the use of a custom validation class provided in the configuration of the JWT service.

## Calling Validation
Triggering Validation is as simple as calling the `validate ` method on the JWT service.  This triggers all default validation and then processes custom validation _if defined_.

```php
$this->jwt->validate($parsedToken);
```
_provided Token must be a `JWT Token` that has already been parsed_

## Custom Validation
Validation is extensible in that you can create a custom validator class with your own methods.  As long as this is passed in the [custom JWT options](#config-method) or defined in the [Jwt Configuration](#custom-object-method) it will be called as part of `jwt->validate`.

### Creating a validation class
An empty class, that is autoloaded and accessible throughout the app is required for custom validation.  Adding the `JWT Token` namespace to the class will help with method declarations.

```php
namespace App\Library\Security;
use \Phalcon\Encryption\Security\JWT\Token\Token;

class cValidator {
    // Static methods to go here
}
```
_Give it any name you like, with or without a namespace, as long as the app can find it by name_.

### Creating a validation method
All methods require the following:
- Be `static`
- Be prefixed with `validate`
- Have a single parameter for a `Phalcon JWT Token`.
- Return a `string` on failure (validation fails)
- Return a `null` on success
```php
public static function validateAge(Token $token): ?string
{
  if($token->getClaims()->get('age') < 21) {
    return $token->getClaims()->get('name')." is under age";
  }
  return null;
}
```

### Full Working example class
**Config**
```yaml
jwt:
  #16 char minimum encryption with special characters
  key: My_16_D!g3t_Key$tring 
  issuer: https://mydomain.com
  audience: https://mydomain.com
  duration: +1 day
  subject: My Fancy Subject Line
  validator: App\Library\Security\cValidator #optional
```
**Class**
```php
<?php
namespace App\Library\Security;

use \Phalcon\Encryption\Security\JWT\Token\Token;

class cValidator {
  public static function validateName(Token $token): ?string
  {
    if($token->getClaims()->get('name') != 'bob') {
      return "Name must be 'Bob'";
    }
    return null;
  }
  public static function validateAge(Token $token): ?string
  {
    if($token->getClaims()->get('age') < 21) {
      return $token->getClaims()->get('name')." is under age";
    }
    return null;
  }
}
?>
```
**Validate**
```php
$this->jwt->validate($parsedToken);
```


### Limiting Validation methods
By default, all methods present in the custom validation class will be processed.  Using the provided class example, this would run both `validateName` and `validateAge`.

In some scenarios, you may want to only run certain validation methods.  This is accomplished by calling the `setValidateMethods` method on the `Jwt Service`.

```php
$this->jwt->setValidateMethods(['Age']);
```
_This will set the validation methods for all future calls to only call `validateAge`_

If you want to remove all custom validation, you have to override the custom validator
```php
$this->jwt->setValidator();
```

**Resetting Methods**

After changing validation methods, this can be reverted to all by calling with `setValidateMethods` no parameters.
```php
$this->jwt->setValidateMethods();
```

After changing the validation class, you have to respecify the class in  `setValidator`.
```php
$this->jwt->setValidator('App\Library\Security\cValidator');
```