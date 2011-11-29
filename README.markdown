
#universal-error-catcher [![Build Status](https://secure.travis-ci.org/formapro/UniversalErrorCatcher.png?branch=master)](http://travis-ci.org/formapro/UniversalErrorCatcher)

## Overview

It wraps errors and exception handling logic. Any exception or errors even parse and fatal ones are handled in the same way and passed to you as exception.

## Example

The most common way is to send an email to admin:

    $catcher = new UniversalErrorCatcher_Catcher();

    $catcher->registerCallback(function(Exception $e) {
      $to = 'admin@foo-comapny.com';
      $subject = 'An error has appeared.';
      $body = 'The error `'.$e->getMessage().'` in file `'.$e->getFile().'` on line `'.$e->getLine().'`';

      mail($to, $subject, $body);
    });

    $catcher->start();

    // after the start method is called everything is under your control.

Register callbacks:

    $catcher = new UniversalErrorCatcher_Catcher();

    $catcher->registerCallback(function(Exception $e) {
      // do some stuff
    });

    $catcher->registerCallback(function(Exception $e) {
      // do some extra stuff
    });

    $catcher->start();

The library is completely covered by phpunit tests.