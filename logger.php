<?php

$log = false;
function log4($message){ //TODO use namespaces
    echo "Log: ";
    echo $message;
    echo "\n";
}
/**
 * Die with custom formatted output and Usage info
 */
function dieSafely($error_message){
    // todo research detect open files and db connection for closing before die()
    // ...
    echo "\n";
    echo "Error: ".$error_message;
    echo "\n";
//    echo "\n";
//    echo "USAGE: php file_name.php import|export ... ";
//    echo "\n";
    die();
}
function logger_on(){
    logger_switch(true);
}
function logger_off(){
    logger_switch(false);
}
function logger_switch($bool){
    global $log;
    $log = $bool;
}
