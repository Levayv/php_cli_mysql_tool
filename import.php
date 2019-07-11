<?php

require_once 'credentials.php';
require_once 'validator.php';
require_once 'sql_parser.php';
require_once 'logger.php';

logger_on();
log4("Entry point");
log4("Argument count is $argc");

try{
    $myArgs = validateUserInput($argv);
//    var_dump($myArgs);die();
    doStuff($myArgs);

}catch (Exception $e){
    dieSafely("???");
}

// todo remove all dumbs
// todo replace error handling logic to Exception





//function dieSafely($string){
//    echo "ERROR: ".$string."\n";
//    echo "USAGE: php file_name.php import|export ... \n";
//    die();
//}

//function doStuff($args){ // TODO export to separate file ?
//    var_dump($args);
//    $connection = mysqli_connect(
//        DB_HOST,
//        DB_USER,
//        DB_PASS,
//        "testingzone",
//        DB_PORT
//    );
//    if  ($connection  == false){
//        dieSafely("CONNECTION FAILED");
//    } else{
//        echo "CONNECTION ESTABLISHED - ".mysqli_get_host_info($connection)."\n";
//    }
//    $sql2 = "select * from people;";
//    $sql2 = "show create table people;";

//}
