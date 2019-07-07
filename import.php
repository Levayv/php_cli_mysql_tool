<?php

//$argv = $_SERVER[$argv]; // todo useless ? research argv & SuperGlobals
//var_dump($argv); //todo remove all var_dumbs
$myArgs;
echo "Entry point\n";
echo "Argument count = $argc \n";
$valid_args = ["import" , "export"];
//var_dump($valid_args );
foreach ($valid_args as &$item){
    $item = "-" . $item;
}
//var_dump($valid_args);
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
    die("\n\n Error: ".$string."\n");
}
function doStuff($args){ // TODO export to separate file ?
    echo " start doing staff \n";
    mysqli_connect(
        "localhost",
        "",
        "",
        "",
        ""
    );
}
/*
 $count = 0;
foreach ($valid_args as $item){
    if ($item == $argv[1]){
        $count++;
    }
}
if ($count )

    */
