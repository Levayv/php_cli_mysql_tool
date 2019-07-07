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
//die("YOU SHALL NOT PASS");
/** check if arguments count is valid , argv[0] is this php file name */
if ($argc < 2 || $argc > 5){
  die("Temp: Invalid argument count: $argc \n");
}
// check if argument values are valid
// check 1st argument - EXPORT or IMPORT
echo "asdasd ".$argv[1]." asdasd\n";
if (!in_array($argv[1], $valid_args)) {
    $string = implode(" , ", $valid_args);
    die("Temp: Invalid first argument: $argv[1] (can be one of $string ) \n");
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
    echo "case 2 args";
}
if ($argc-1 == 3) {
    echo "case 3 args";
    if (validateFileArg($argv[3])){
        $myArgs['filename'] = $argv[3];
    }
}
if ($argc-1 == 4) {
    echo "case 4 args";
    if (validateFileArg($argv[3])){
        $myArgs['filename'] = $argv[3];
    }
}
function validateDbTableArg($string){
    if (preg_match('/[^A-Za-z0-9$_]/', $string)) {
        die("Temp: Invalid characters in DB/Table name : $string (can be one only A-Z , a-z , 0-9 , $ , _ ) \n");
    }
    return true;
}
function validateFileArg($string){
    if (preg_match('/[^A-Za-z0-9._]/', $string)) {
        die("Temp: Invalid characters in Table name : $string (can be one only A-Z , a-z , 0-9 , . , _ ) \n");
    }
    return true;
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
