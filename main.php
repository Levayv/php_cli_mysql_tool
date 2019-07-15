<?php

require_once 'config/credentials.php';
require_once 'bin/validator.php';
require_once 'bin/sql_parser.php';
require_once 'bin/lib/logger.php';

logger_on();
log4("Entry point");
log4("Argument count is $argc");

try {
    $myArgs = validateUserInput($argv);
    $cmd = "task_".$myArgs['task'];
    if (function_exists($cmd)){
        $cmd($myArgs);
//        task_export($myArgs);
//        task_import($myArgs);
    }else{
        dieSafely("No such task, must be import or export"); // todo change to
    }
} catch (Exception $e) { // todo refactor ?
    dieSafely("???");
}