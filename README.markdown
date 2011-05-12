
#universal-error-handler

## Overview

It wraps logic that can catch any errors or exception even parse or fatal ones.

## Example

    $handler = new UniversalErrorHandler_Handler(function(Exception $e) {
      $to = 'admin@foo-comapny.com';
      $subject = 'An error has appeared.';
      $body = 'The error `'.$e->getMessage().'` in file `'.$e->getFile().'` on line `'.$e->getLine().'`';

      mail($to, $subject, $body);
    });

    $handler->start();

    // after the start method is called all exceptions and php errors will be caught.
    // php errors will be converted to ErrorException