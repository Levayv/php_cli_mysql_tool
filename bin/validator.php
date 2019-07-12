<?php

require_once 'lib/logger.php';
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
    //------------------------------------------------------------------
    //-- MANDATORY --
    //------------------------------------------------------------------
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
    }

    $validArgs['task'] = $userInput[1];
    log4("Validation: 1st arg is OK [task]");

    // check 2nd argument - database name
    validateDatabaseName($userInput[2]);
    $validArgs['db_name'] = $userInput[2];
    log4("Validation: 2nd arg is OK [DB name]");

    //------------------------------------------------------------------
    //-- OPTIONAL
    //------------------------------------------------------------------
    // todo delete me

    if ($argc == 3) {
        log4("Optional argument missing 2");
        dieSafely ("Missing functionality - 2 argument not supported YET ");
    }
    if ($argc == 4) {
        log4("Optional argument missing 1");
        if (validateFileName($userInput[3])){
            $validArgs['filename'] = $userInput[3];
        }
        dieSafely ("Missing functionality - 3 argument not supported YET ");
    }
    if ($argc == 5) {
        log4("All Optional argument are specified");
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
        "", //TODO change temp value
        DB_PORT
    );
    // TODO change DB_USER,DB_HOST,DB_PASS,DB_PORT to assoc-arr
    if  ($connection  == false){
        dieSafely("Connection failed to Mysql Server ".mysqli_connect_error());
    } else{
        log4("Connection established to Mysql Server - ".mysqli_get_host_info($connection));
    }
}

/** Check characters in name and check if database exist in MySql Server
 * @param $database_name string
 * @return bool
 */
function validateDatabaseName($database_name):bool{
    // RegEx check
    if (preg_match('/[^A-Za-z0-9$_,]/', $database_name)) {
        dieSafely("Invalid characters in Database name : $database_name ".
            "(can be one only A-Z , a-z , 0-9 , $ , _ )");
    }
    // Mysql check
    $existing_database_names = get_db_names();
    if (!in_array($database_name , $existing_database_names)){
        dieSafely("Database with name ($database_name) doesn't exist. ");
    }
    return true;
}

/**
 * @param $table_name string name to check
 * @return bool
 */
function validateSingleTableName($table_name):bool{
    if (preg_match('/[^A-Za-z0-9_]/', $table_name)) {
        dieSafely("Invalid characters in table name : $table_name\n".
            "       (must contain only Latin letters, numbers or underscore ) \n");
    }
    return false;
}

/**
 * @param $table_names_string string table1,table2,table3 to check
 * @return bool
 */
function validateTableNames($table_names_string):bool{ //todo polish me
//    $table_names_string = "table1,table2,table3";
//    $table_names_string = "table*1";
    if (strpos($table_names_string , ",")) {
//        echo " n comma \n";
        $table_names_arr = explode(",",$table_names_string);
        foreach ($table_names_arr as $value){
            validateSingleTableName($value);
        }
    }else{
//        echo " 0 comma \n";
        validateSingleTableName($table_names_string);
    }
    return true;
}
function validateFileName($file_name){
    if (preg_match('/[^A-Za-z0-9._]/', $file_name)) {
        dieSafely("Invalid characters in file name : $file_name\n".
            "       (must contain only Latin letters, numbers, dot or underscore ) \n");
    }
    // Check if file / folder exist with specified name
    if (file_exists($file_name)){
        if (is_dir($file_name)){
            dieSafely("Folder with the name $file_name already exist, can't create new file");
        }else{
            log4("File ($file_name) for export exists "); //todo overwrite ?
        }
    }else{
        log4("File ($file_name) doesn't exist, it will be created");
    }
    return true;
}
