<?php

/**
 * Validate User input
 * @param $userInput array of arguments inputted by user
 * @return array (associative) of user input
 */
function validateUserInput($userInput):array{


    return null;
}
function validateDatabaseName($database_name):bool{
    return false;
}
function validateTableName($table_name):bool{
    return false;
}
function validateTableNames($table_names):bool{
    return false;
}
function dieSafely($Error_message){
    // todo research detect open files and db connection for closing before die()
}