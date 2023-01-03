<div align="center">
  <!-- PROJECT LOGO -->
  <a href="https://github.com/SageITSolutions/phalcon-rest">
    <img src=".readme/logo.png" alt="Logo" width="445" height="120">
  </a>

  <h1 align="center">Phalcon REST -> Middleware</h1>

  <p>
    Middleware services for parsing JSON request and reponses, as well as common error catches and Cross-Origin Resource Sharing (CORS).
  </p>

**[Main Readme »](README.md)**

</div>

<!-- TABLE OF CONTENTS -->

## Table of Contents

- [Usage](#usage)
  - [Implementing a Middleware Service](#implementing-a-middleware-service)
- [JSON Formatting](#json-formatting)
  - [JSON Request](#json-request)
  - [JSON Response](#json-response)
- [Cross-Origin Resource Sharing](#cross-origin-resource-sharing-cors)
- [Exception Handling](#not-found-and-exception-handling)
  - [Not Found](#not-found)
  - [Exception](#exception)

# Usage
Each middleware class corresponds to various other services which should be added as needed. See `Implementing a Service` of the [Main Readme](#README.md)

## Implementing a Middleware Service
This project consists of prebuild middleware services that simply need to be added to Micro app for services.  Like the main services, these can be done manually, or parsed from a config.

**Config Register**

One option is to have your application iterate micro services listed in the config object and register them

```yaml
middleware:
  before:
    notfound: Phalcon\Middleware\NotFound
    exception: Phalcon\Middleware\Exception
    cors: Phalcon\Middleware\CORS
    request: Phalcon\Middleware\JSON\Request
  after:
    response: Phalcon\Middleware\JSON\Response
```

```php
$manager = $app->getDI()->get('eventsManager');
foreach ($middleware as $trigger => $handler) {
  foreach ($handler as $class) {
    $svs = new $class();
    $manager->attach('micro',$svs);
    $app->$trigger($svs);
  }
}
$this->setEventsManager($manager);
```
_Note that middleware **requires** the presence of an eventsManager service.  Just ensure the micro app has `Phalcon\Events\Manager` added as a service_

**Register Manually**

```php
$manager = $app->getDI()->get('eventsManager');
$nf = new Phalcon\Middleware\NotFound();
$jsonreq = new Phalcon\Middleware\JSON\Request();
$jsonres = new Phalcon\Middleware\JSON\Response();

$manager->attach('micro', $nf);
$manager->attach('micro', $jsonreq);
$manager->attach('micro', $jsonres);

$app->before($nf);
$app->before($jsonreq);
$app->after($jsonres);

$app->setEventsManager($manager);
```

# JSON Formatting
The JSON request and Response Middleware ensures that HTTP traffic is properly formatted

## JSON Request
This middleware ensures inbound requests are properly formatte d JSON otherwise it calls a Format Exception

## JSON Response
This middlware parses the Micro App return value and encapsulates it in a standard JSON format

```json
{
  'code': 200,
  'status': 'success',
  'message': '',
  'payload': MIXED
}
```

# Cross-Origin Resource Sharing (CORS)
This middleware adds heads for CORS enablement prior to parsing the route.

# Not found and Exception handling
There are two components to this middleware to allow for separate utilization of exception handling and Not Found results.  Both middleware services in this section rely on [sageit\phalcon-exception-handler](https://github.com/SageITSolutions/phalcon-exception-handler).

## Not Found
The not found middleware simply returns a JSON formatted 404 response with a message including `('POST:\route') not found or error in request`.  POST is replaced with the http method while route is the full URI requested.

## Exception
This middleware takes any thrown exception and encapsulates it in a JSON response where the code and message match the excption.