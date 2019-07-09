<?php

require 'private.php';

//$argv = $_SERVER[$argv]; // todo useless ? research argv & SuperGlobals
// todo remove all dumbs
// todo replace error handling logic to Exception

echo "Entry point\n";
echo "Argument count = $argc \n";
$valid_args = ["import" , "export"];
foreach ($valid_args as &$item){
    $item = "-" . $item;
}
//dieD("YOU SHALL NOT PASS");
/** check if arguments count is valid , argv[0] is this php file name */
if ($argc < 2 || $argc > 5){
    dieD("Temp: Invalid argument count: $argc \n");
}
// check if argument values are valid
// check 1st argument - EXPORT or IMPORT
if (!in_array($argv[1], $valid_args)) {
    $string = implode(" , ", $valid_args);
    dieD("Temp: Invalid first argument: $argv[1] (can be one of $string ) \n");
    //todo parse $valid_args array to single string
} else { //todo remove else clause
    echo "good 1st arg \n";
}
$myArgs['task'] = $argv[1];
// check 2nd argument - dbname
validateDbTableArg($argv[2]);
$myArgs['db_name'] = $argv[2];

// detect missing arguments and set default values
if ($argc-1 == 2) {
//    echo "case 2 args \n";
    dieD ("Missing functionality - 2 argument not supported YET ");
}
if ($argc-1 == 3) {
//    echo "case 3 args \n";
    if (validateFileArg($argv[3])){
        $myArgs['filename'] = $argv[3];
    }
    dieD ("Missing functionality - 3 argument not supported YET ");
}
if ($argc-1 == 4) {
//    echo "case 4 args \n";
    if (validateDbTableArg($argv[3])){
        $myArgs['table_names'] = $argv[3];
    }
    if (validateFileArg($argv[4])){
        $myArgs['file_name'] = $argv[4];
    }
}
doStuff($myArgs);

function validateDbTableArg($string){
    if (preg_match('/[^A-Za-z0-9$_,]/', $string)) {
        dieD("Temp: Invalid characters in DB/Table name : $string (can be one only A-Z , a-z , 0-9 , $ , _ ) \n");
    }
    return true;
}
function validateFileArg($string){
    if (preg_match('/[^A-Za-z0-9._]/', $string)) {
        dieD("Temp: Invalid characters in Table name : $string (can be one only A-Z , a-z , 0-9 , . , _ ) \n");
    }
    return true;
}
function dieD($string){
    die("\n Error: ".$string."\n");
}
function doStuff($args){ // TODO export to separate file ?
    echo " start doing staff \n";
    $connection = mysqli_connect(
        DB_HOST,
        DB_USER,
        DB_PASS,
        "cm_quansh",
        DB_PORT
    );
    if  ($connection  == false){
        dieD("CONNECTION FAILED");
    } else{
        echo "CONNECTION ESTABLISHED - ".mysqli_get_host_info($connection)."\n";
    }
    $sql2 = "select * from people;";
    $sql2 = "show create table people;";
    $sql2 = "show tables;";
    $result = mysqli_query($connection , $sql2);
    if ($result == false){
        dieD("Mysql query is empty");
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
            dieD("Mysql query is empty for - $value");
        }
        // for each table get a show create query
        while ($row = mysqli_fetch_array($result)){
            var_dump($row);
//            echo "$row[1]\n";
            $table_show_create[$iter] = "$row[1]";
            $iter++;
        }
    }
    $string_to_file = implode(";\n\n# END #\n\n", $table_show_create);
    echo "\n\n\n".$string_to_file ."\n size = ".count($table_show_create)  ;
    $file = fopen("work_dir/test.sql",'w');
    if (!fwrite($file,"$string_to_file"))
        dieD("file error");
    fclose($file);
}
