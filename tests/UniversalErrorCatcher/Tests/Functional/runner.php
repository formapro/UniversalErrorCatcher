<?php

require_once dirname(__FILE__).'/../../../../autoload.php';

function __test_callback()
{
    echo "The error was caught\n\n\n";
}

$catcher = new UniversalErrorCatcher_Catcher();
$catcher->registerCallback('__test_callback');
$catcher->start();

include $_SERVER['argv'][1];