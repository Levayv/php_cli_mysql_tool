<?php

require_once 'logger.php';
/**
 * Validate User input
 * @param $userInput array of arguments inputted by user
 * @return array (associative) of user input
 */
function validateUserInput($userInput):array{
//    if (!isset($userInput))
//        dieSafely("\$argv SuperGlobal not available, ".
//            "use \"-d register_argc_argv=1\" flag for php interpreter ");
    initDB();
    $valid_args = ["import" , "export"];
    // check if arguments count is valid , argv[0] is this php file name
    $argc = count($userInput);
    if ($argc < 2 || $argc > 5){
        dieSafely("Temp: Invalid argument count: $argc \n");
    }
    // check 1st argument is valid or not
    if (!in_array($userInput[1], $valid_args)) {
        $string = implode(" , ", $valid_args);
        dieSafely("Temp: Invalid first argument: $userInput[1] (can be one of $string ) \n");
        //todo parse $valid_args array to single string
    } else { //todo remove else clause
        echo "good 1st arg \n";
    }

    $validArgs['task'] = $userInput[1];
    // check 2nd argument - dbname
    validateDatabaseName($userInput[2]);
    $validArgs['db_name'] = $userInput[2];

    //----------------------------------------------------------------------------------
    // detect missing arguments and set default values
    if ($argc-1 == 2) {
//    echo "case 2 args \n";
        dieSafely ("Missing functionality - 2 argument not supported YET ");
    }
    if ($argc-1 == 3) {
//    echo "case 3 args \n";
        if (validateFileName($userInput[3])){
            $validArgs['filename'] = $userInput[3];
        }
        dieSafely ("Missing functionality - 3 argument not supported YET ");
    }
    if ($argc-1 == 4) {
//    echo "case 4 args \n";
        if (validateTableNames($userInput[3])){
            $validArgs['table_names'] = $userInput[3];
        }
        if (validateFileName($userInput[4])){
            $validArgs['file_name'] = $userInput[4];
        }
    }

    return $validArgs;
}
function validateMandatoryArguments(){

}
function validateOptionalArgument(){

}
// todo will have arguments for custom mysql connection
function initDB(){
    /** Represents the connection to a MySQL Server. */
    global $connection;
    $connection = mysqli_connect(
        DB_HOST,
        DB_USER,
        DB_PASS,
        "testingzone", //TODO change temp value
        DB_PORT
    );
    // TODO change DB_USER,DB_HOST,DB_PASS,DB_PORT to assoc-arr
    if  ($connection  == false){
        dieSafely("Connection failed to Mysql Server ".mysqli_connect_error());
    } else{
        log4("Connection established to Mysql Server - ".mysqli_get_host_info($connection));
    }
}
/** Check characters in name and check if database exist in MySql Server */
function validateDatabaseName($database_name):bool{
    // RegEx check
    if (preg_match('/[^A-Za-z0-9$_,]/', $database_name)) {
        dieSafely("Invalid characters in Database name : $database_name ".
            "(can be one only A-Z , a-z , 0-9 , $ , _ )");
    }
    // Mysql check
    $a = get_table_names("testingzone");
    var_dump($a);
    return true;
}
function validateTableName($table_name):bool{
    return false;
}
function validateTableNames($table_names):bool{
    return false;
}
function validateFileName($file_name){
    if (preg_match('/[^A-Za-z0-9._]/', $file_name)) {
        dieSafely("Temp: Invalid characters in Table name : $file_name (can be one only A-Z , a-z , 0-9 , . , _ ) \n");
    }
    return true;
}
