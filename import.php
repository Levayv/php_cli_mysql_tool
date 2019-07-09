<?php

require_once 'credentials.php';
require_once 'validator.php';
require_once 'sql_parser.php';
require_once 'logger.php';

logger_on();
log4("Entry point");
log4("Argument count is $argc");


$myArgs = validateUserInput($argv);
// todo remove all dumbs
// todo replace error handling logic to Exception



doStuff($myArgs);

function validateDbTableArg($string){
    if (preg_match('/[^A-Za-z0-9$_,]/', $string)) {
        dieSafely("Temp: Invalid characters in DB/Table name : $string (can be one only A-Z , a-z , 0-9 , $ , _ ) \n");
    }
    return true;
}
function validateFileArg($string){
    if (preg_match('/[^A-Za-z0-9._]/', $string)) {
        dieSafely("Temp: Invalid characters in Table name : $string (can be one only A-Z , a-z , 0-9 , . , _ ) \n");
    }
    return true;
}
//function dieSafely($string){
//    echo "ERROR: ".$string."\n";
//    echo "USAGE: php file_name.php import|export ... \n";
//    die();
//}
function doStuff($args){ // TODO export to separate file ?
    echo " start doing staff \n";
    var_dump($args);
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
    global $connection;
    $sql2 = "select * from people;";
    $sql2 = "show create table people;";
    $sql2 = "show tables;";
    $result = mysqli_query($connection , $sql2);
    if ($result == false){
        dieSafely("Mysql query is empty");
    }
    $iter=0;
    while ($row = mysqli_fetch_array($result)){
        echo "$row[0]\n";
        $table_names[$iter] = "$row[0]";
        $iter++;
    }
    $sql_skeleton = "SHOW CREATE TABLE ";
//    $sql_arr = ['asd',"asd"]; //todo remove
    foreach ($table_names as $key => $value) {
        $sql_arr[$key] = $sql_skeleton.$value.";";
        echo "Sql_arr [$key] = $sql_arr[$key] \n";
    }

//    $result = mysqli_query($connection , $sql_arr[0]);
//    if ($result == false){
//        dieD("Mysql query is empty for - $sql_arr[0]");
//    }

//    $sql_arr_result;
    $iter = 0;
    foreach ($sql_arr as $key => $value) {
//        $value = "show tables;";
//        echo "\n!!! $value at $key";
//        echo "\n!!! $sql_arr[0]";
//        echo "\n!!! $sql_arr[1]";
//        echo "\n!!! $sql_arr[2]";
//        echo "\n!!! $sql_arr[3]";
//        unset($result);
//        echo "!!!";
        $result = mysqli_query($connection , $value);
        if ($result == false){
            dieSafely("Mysql query is empty for - $value");
        }
        // for each table get a show create query
        while ($row = mysqli_fetch_array($result)){
//            var_dump($row); //todo CHECK ME ASAP
            $table_show_create[$iter] = "$row[1]";
            $iter++;
        }
    }
    $string_to_file = implode(";\n\n# END #\n\n", $table_show_create);
//    echo "\n\n\n".$string_to_file ."\n size = ".count($table_show_create)  ;
    $file = fopen("work_dir/test.sql",'w');

    if (!fwrite($file,"$string_to_file"))
        dieSafely("file error");

    fclose($file);
    echo "\nJob done";
}
