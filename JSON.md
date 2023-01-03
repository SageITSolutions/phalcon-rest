<div align="center">
  <!-- PROJECT LOGO -->
  <a href="https://github.com/SageITSolutions/phalcon-rest">
    <img src=".readme/logo.png" alt="Logo" width="445" height="120">
  </a>

  <h1 align="center">Phalcon REST -> JSON Requests/Response</h1>

  <p>
    Library for interpretting JSON requests and returning JSON response.
  </p>

**[Main Readme »](README.md)**

</div>

<!-- TABLE OF CONTENTS -->

## Table of Contents

- [Requests](#requests)
- [Response](#response)

## Requests
Custom `Http Request` created to parse requests from JSON.

### **Get**
Searches for named parameter and returns using Phalcon request getHelper
```php
$param = $this->request->get($name, $filters, $default, $notAllowEmpty, $noRecursive);
```

### **Post**
calls get but for posts
```php
$param = $this->request->getPost($name, $filters, $default, $notAllowEmpty, $noRecursive);
```

### **Has Post**
Checks if parameter is present in body
```php
if($this->request->hasPost($name)){
  //Do Stuff
}
```

### **Bearer Token**
Extracts Bearer Token from header and returns as Authorization String.  Couples well with [JWT Encryption](#JWT.md) included in this library.
```php
$tokenString = $this->request->getBearerToken();
```

## Response
Simply adds ContentType `application/json` and `utf-8` to the default Phalcon HTTP Response;
```php
$di->setShared(
    'response',
    function () use ($di) {
        $response = new \Phalcon\Http\Response();
        $response->setContentType('application/json', 'utf-8');
        return $response;
    }
);
```
_actual service content_