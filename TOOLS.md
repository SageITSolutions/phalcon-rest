<div align="center">
  <!-- PROJECT LOGO -->
  <a href="https://github.com/SageITSolutions/phalcon-rest">
    <img src=".readme/logo.png" alt="Logo" width="445" height="120">
  </a>

  <h1 align="center">Phalcon REST -> Tools</h1>

  <p>
    Tools Library for decomposing routes, checking csrf, and additional utility methods.
  </p>

**[Main Readme »](README.md)**

</div>

<!-- TABLE OF CONTENTS -->

## Table of Contents

- [Route Parsing](#controller)
  - [Controller](#controller)
  - [Action](#action)
  - [Query Parameters](#params)
- [CSRF](#csrf)
- [Password Encryption](#password-encryption)
- [Array | Object Parsing](#array--object-parsing)

## Controller
parses a route by `/` and returns the section defining the `Controller` class.

```php
$controller = $this->tools->Controller($route);
```

## Action
parses a route by `/` and returns the section defining the `Action` method.

```php
$action = $this->tools->Action($route);
```

## Params
parses a route by `/` and returns the section defining the `Params` query string.

```php
$queryParms = $this->tools->Params($route);
```

## CSRF
Calls Phalcon Security class to process CSRF. Calls flash service if present and redirect is false.  If Redirect is defined, redirects on failure.

```php
$this->tools->csrf($redirect);
$this->tools->csrf(false);
```

## Password Encryption
Allows encryption, password generation, and secure Hash validation to strings.

```php
//encrypt a raw string password
$encryptedPass = $this->tools->encryptPassword($password);
//gen Password, defaults to 8 characters with "strict" defined
$newPassword = $this->tools->generatePassword(); 
//Override to 12 characters
$newPassword_12char = $this->tools->generatePassword(12);
//Validate provided password
if(!$this->tools->verifyPassword($rawPassword, $encrypted)){
  //Error event
}
```

## Array | Object Parsing
Boxed functionality for parsing arrays.

```php
if($this->tools->arrayHasAllKeys($keys, $targetArray)){
  //ALL keys provided are present within the target Array
}
if($this->tools->objectHasAllProperties($properties, $object)){
  //ALL properties provided are present within the target Object
}
//Checks if $add object exists in array before adding to end
$this->tools->appendArray($array, $add);
//Provides a defalt value if array is empty
//** this functionality is replaced with php 7+ ?: ternary operators
$this->tools->default($target, $default);
```
