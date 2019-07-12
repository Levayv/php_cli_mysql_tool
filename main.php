<?php

require_once 'config/credentials.php';
require_once 'bin/validator.php';
require_once 'bin/sql_parser.php';
require_once 'lib/logger.php';

logger_on();
log4("Entry point");
log4("Argument count is $argc");

try {
    $myArgs = validateUserInput($argv);
    export($myArgs);
} catch (Exception $e) { // todo refactor ?
    dieSafely("???");
}