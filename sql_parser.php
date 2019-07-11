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
    $db_names = get_query($sql,null);
    return $db_names;
}
function get_table_names($db){
    $sql1 = "USE ".$db.";";
    $sql2 = "SHOW TABLES;"; //todo bug asap
    echo "sql1 = $sql1\n";
    echo "sql2 = $sql2\n";
    $table_names = get_query( $sql1 , $sql2);
    return $table_names;
}

/**
 * @param $sql_db string for "USE database_name;" query
 * @param $sql_t string for "SHOW CREATE TABLE table_name;" query
 * @return null
 */
function get_query($sql_db, $sql_t){
    //TODO refactor function name
    //TODO optimise
    global $connection;
    if (!isset($sql_t)){
        // case 1 - get database names
        mysqli_real_escape_string($connection , $sql_t);
        $result = mysqli_query($connection , $sql_db);
        if ($result == false){
            dieSafely("Mysql1 - ".mysqli_error($connection));
        }
    }else{
        // case 2 - get table names
        $result = mysqli_query($connection , $sql_db);
        if ($result == false){
            dieSafely("Mysql1 - ".mysqli_error($connection));
        }
        $result  = mysqli_query($connection , $sql_t);
        if ($result == false){
            dieSafely("Mysql1 - ".mysqli_error($connection));
        }
    }
    $iter=0;
    $names = null; //todo useless ? research
    while ($row = mysqli_fetch_array($result)){
        $names[$iter] = "$row[0]";
        $iter++;
    }
    return $names;
}