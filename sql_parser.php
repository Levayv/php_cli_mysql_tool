<?php

require_once 'logger.php';
$connection = null;

function doStuff($args){ // TODO refactor / split
    global $connection;
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

function get_db_names(){
    $sql = "SHOW DATABASES;";
    $db_names = get_db_or_table_names($sql);
    return $db_names;
}
function get_table_names($db){
    var_dump($db);
    $sql = "USE ".$db."; SHOW TABLES;"; //todo bug asap
    echo $sql;
    $table_names = get_db_or_table_names( $sql );
    return $table_names;
}
function get_db_or_table_names($sql1,$sql2){
    global $connection;
    $result1 = mysqli_query($connection , $sql1);
    if ($result1 == false){
        dieSafely("Mysql1 - ".mysqli_error($connection));
    }
    $result2 = mysqli_query($connection , $sql2);
    if ($result2 == false){
        dieSafely("Mysql2 - ".mysqli_error($connection));
    }
    $iter=0;
    $names = null;
    while ($row = mysqli_fetch_array($result1)){
        echo "$row[0]\n";
        $names[$iter] = "$row[0]";
        $iter++;
    }
    return $names;
}