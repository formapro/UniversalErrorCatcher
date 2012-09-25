#Universal error catcher [![Build Status](https://secure.travis-ci.org/formapro/UniversalErrorCatcher.png?branch=master)](http://travis-ci.org/formapro/UniversalErrorCatcher)

## Overview

It wraps errors and exception handling logic. 
Any exceptions or errors (even parse and fatal ones) are handled in the same way. 

The lib also provides two custom exceptions: 

* `FatalErrorException` - fatal errors i.e. `E_ERROR`, `E_PARSE`, `E_CORE_ERROR`, `E_COMPILE_ERROR`
* `SuppressedErrorException` - recoverable errors which comes from the code under `@`

It is completely covered with phpunit tests.

## Instalation

[Composer](http://getcomposer.org/) is a prefered way to install it.

```bash
php composer.phar require fp/universal-error-catcher
```

When you are asked for a version constraint, type * and hit enter.

## Quick tour
 
The example shows the simplest way of sanding an email on each error. 

```php
<?php
    $catcher = new UniversalErrorCatcher_Catcher();

    $catcher->registerCallback(function(Exception $e) {
      $to = 'admin@example.com';
      $subject = 'Error: '.$e->getMessage();
      $body = (string) $e;

      mail($to, $subject, $body);
    });

    $catcher->start();

    // after the start method is called everything is under your control.
```

### Fatal errors.

Let's imagine we try to call a method which does not exist. In this situation php will raise a fatal error. 

```php
<?php
    $catcher = new UniversalErrorCatcher_Catcher();

    $catcher->registerCallback(function(Exception $e) {
      $e instanceof FatalErrorException //true
    });

    $catcher->start();

    $anObject->notExistMethod();

```

Or the other situation when we run out of memory. In this case the catcher will gladly free some resorved memory for us. 

```php
<?php
    $catcher = new UniversalErrorCatcher_Catcher();

    $catcher->registerCallback(function(Exception $e) {
      $e instanceof FatalErrorException //true
    });

    $catcher->start();

    ini_set('memory_limit', '1K');

    str_repeat('foobar', PHP_INT_MAX);
```

### Recoverable errors:

By default php errors (warnings and so on) wouldn't be thrown but passed to callback in background.

```php
<?php
    $catcher = new UniversalErrorCatcher_Catcher();

    $catcher->registerCallback(function(Exception $e) {
        $e instanceof ErrorException //true
    });

    $catcher->start();
    
    echo $undefinedVariable;
    
    echo 'the script continue to work. This message will be outputed';
```

You can change this by converting all errors to exception. just set `setThrowRecoverableErrors` to true.

```php
<?php
    $catcher = new UniversalErrorCatcher_Catcher();
    $catcher->setThrowRecoverableErrors(true); // false by default

    $catcher->registerCallback(function(Exception $e) {
        $e instanceof ErrorException //true
    });

    $catcher->start();
    
    echo $undefinedVariable;
    
    echo 'the exception is throw. It will never be outputed';
```

The errors behaind `@` (i.e suppressed) are also caught. 
Change `setThrowSuppressedErrors` to true if you want throw them.
 
```php
<?php
    $catcher = new UniversalErrorCatcher_Catcher();
 
    $catcher->registerCallback(function(Exception $e) {
        $e instanceof SuppressedErrorException //true
    });
 
    $catcher->start();

    @trigger_error('supressed warning', E_USER_WARNING);
     
    echo 'the script continue to work. This message will be outputed';
```
 
### Exceptions:
 
Any not caught exceptions will be passed to you: 
 
```php
<?php
    $catcher = new UniversalErrorCatcher_Catcher();
 
    $catcher->registerCallback(function(Exception $e) {
        $e instanceof LogicException //true
    });
 
    $catcher->start();
 
    throw new LogicException('something strange happened. I am scared.');
     
    echo 'the exception is throw. It will never be outputed';
```