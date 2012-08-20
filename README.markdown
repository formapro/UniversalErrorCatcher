#universal-error-catcher [![Build Status](https://secure.travis-ci.org/formapro/UniversalErrorCatcher.png?branch=master)](http://travis-ci.org/formapro/UniversalErrorCatcher)

## Overview

It wraps errors and exception handling logic. Any exception or errors even parse and fatal ones are handled in the same way and passed to you as an exception.

## Exceptions
* `FatalErrorException` - fatal errors i.e. `E_ERROR`, `E_PARSE`, `E_CORE_ERROR`, `E_COMPILE_ERROR`
* `ErrorException` - recoverable errors i.e. `E_WARNING`, `E_USER_WARNING`, `E_NOTICE` etc
* `SuppressedErrorException` - recoverable errors which comes from the code under `@`

## Examples

The most common way is to send an email to admin:

```php
<?php
    $catcher = new UniversalErrorCatcher_Catcher();

    $catcher->registerCallback(function(Exception $e) {
      $to = 'admin@foo-comapny.com';
      $subject = 'An error has appeared.';
      $body = 'The error `'.$e->getMessage().'` in file `'.$e->getFile().'` on line `'.$e->getLine().'`';

      mail($to, $subject, $body);
    });

    $catcher->start();

    // after the start method is called everything is under your control.
```

Registering callbacks:

```php
<?php
    $catcher = new UniversalErrorCatcher_Catcher();

    $catcher->registerCallback(function(Exception $e) {
      // do some stuff
    });

    $catcher->registerCallback(function(Exception $e) {
      // do some extra stuff
    });

    $catcher->start();
```

Converting all notices, errors to exceptions:

```php
<?php
    $catcher = new UniversalErrorCatcher_Catcher();
    $catcher->setThrowRecoverableErrors(true);
    $catcher->start();
    
    try
    {
        echo $undefinedVariable;
    }
    catch(Exception $e)
    {
        echo $e->getMessage();
    }
```

Suppressed errors catcher:

```php
<?php
    $catcher = new UniversalErrorCatcher_Catcher();
    $catcher->setThrowSuppressedErrors(false); //false by default

    $catcher->registerCallback(function(Exception $e) {
        if($e instanceof SuppressedErrorException) {
            echo $e->getMessage();
        }
    });

    $catcher->start();

    @trigger_error('supressed warning', E_USER_WARNING);
```

The library is completely covered with phpunit tests.