<?php

require_once dirname(__FILE__).'/../../../../autoload.php';

function __test_callback()
{
    echo "The error was caught\n\n\n";
}

$catcher = new UniversalErrorCatcher_Catcher();
$catcher->setThrowRecoverableErrors(true);
$catcher->setThrowSuppressedErrors(false);
$catcher->registerCallback('__test_callback');
$catcher->start();

try {
    include $_SERVER['argv'][1];
} catch (SuppressedErrorException $e) {
    echo "SuppressedErrorException was thrown";
} catch (ErrorException $e) {
    echo "ErrorException was thrown";
}
