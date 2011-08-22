
#universal-error-catcher

## Overview

It wraps errors and exception handling logic. Any errors or exception even parse or fatal ones process the same and passed to you as exception.

## Example

The most common way is to send an email to admin:

    $catcher = new UniversalErrorHandler_Catcher();

    $catcher->registerCallback(function(Exception $e) {
      $to = 'admin@foo-comapny.com';
      $subject = 'An error has appeared.';
      $body = 'The error `'.$e->getMessage().'` in file `'.$e->getFile().'` on line `'.$e->getLine().'`';

      mail($to, $subject, $body);
    });

    $handler->start();

    // after the start method is called everything is under your control.

Register callbacks:

    $handler = new UniversalErrorHandler_Handler();

    $handler->registerCallback(function(Exception $e) {
      // do some stuff
    });

    $handler->registerCallback(function(Exception $e) {
      // do some extra stuff
    });

    $handler->start();

The library is completely covered by phpunit tests.