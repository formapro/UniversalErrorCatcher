<?php

$rootDir = realpath(dirname(__FILE__).'/../');

require_once $rootDir.'/src/UniversalErrorCatcher/ErrorException.php';
require_once $rootDir.'/src/UniversalErrorCatcher/FatalErrorException.php';
require_once $rootDir.'/src/UniversalErrorCatcher/SuppressedErrorException.php';
require_once $rootDir.'/src/UniversalErrorCatcher/Catcher.php';
