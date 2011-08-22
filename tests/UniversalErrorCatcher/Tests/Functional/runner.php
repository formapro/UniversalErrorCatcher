<?php

require_once __DIR__.'/../../../../src/UniversalErrorCatcher/Catcher.php';

$catcher = new UniversalErrorCatcher_Catcher();

$catcher->registerCallback(function(){
    echo "The error was catched\n\n\n";
});

$catcher->start();

include $_SERVER['argv'][1];