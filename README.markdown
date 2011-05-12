
#universal-error-handler

## Overview

It wraps logic that can catch any errors or exception even parse or fatal ones.

## Example

You can send email with error message:

    $handler = new UniversalErrorHandler_Handler();

    $handler->registerCallback(function(Exception $e) {
      $to = 'admin@foo-comapny.com';
      $subject = 'An error has appeared.';
      $body = 'The error `'.$e->getMessage().'` in file `'.$e->getFile().'` on line `'.$e->getLine().'`';

      mail($to, $subject, $body);
    });

    $handler->start();

    // after the start method is called all exceptions and php errors will be caught.
    // php errors will be converted to ErrorException

Or define several callbacks function:

    $handler = new UniversalErrorHandler_Handler();

    $handler->registerCallback(function(Exception $e) {
      // do some stuff
    });

    $handler->registerCallback(function(Exception $e) {
      // do some other stuff
    });

    $handler->start();

Also it is posible to brake the callback chain by returning true from  a callback.

    $handler = new UniversalErrorHandler_Handler();

    $handler->registerCallback(function(Exception $e) {
      // do some stuff

      return true;
    });

    $handler->registerCallback(function(Exception $e) {
      // this will never called
    });

    $handler->start();
