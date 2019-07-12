<?php

require_once 'lib/logger.php';
$connection = null;

/**
 * @param $args
 */
function export($args){
    $table_names = $args['table_names'];
    $sql_skeleton = "SHOW CREATE TABLE ";
    if (!is_array($table_names)){
        //case 1 single table names
        $array_buffer[0] = $table_names;
        $table_names = $array_buffer;
    }
//    // todo overkill --- start --- ???
//    if (!is_array($table_names)){
//        dd($table_names , "BUG 1");
//    }
//    // todo overkill --- end --- ???
    foreach ($table_names as $key => $value) {
        $sql_array[$key] = $sql_skeleton.$value.";";
    }
    $iter = 0;
    /** @var array $sql_array each row represents table structure recreation query*/
    foreach ($sql_array as $key => $value) {
        $result = query_get_something($value);
        while ($row = mysqli_fetch_array($result)){
            $table_structure[$iter] = "$row[1]";
            $iter++;
        }
    }
    $delimiter = "; \n\n### END ###\n\n";
    /** @var array $table_structure */
    $string_to_write = implode($delimiter, $table_structure);
    // todo file path validation
    $file_name = $args['file_name'];
    $file = fopen("$file_name",'w');
    if (!fwrite($file,"$string_to_write"))
        dieSafely("file error");
    fclose($file);

    log4("Export completed successfully");
}

/**
 * @param $sql
 * @return mysqli_result|bool
 * For successful SELECT, SHOW, DESCRIBE or EXPLAIN queries, mysqli_query() will return a mysqli_result object.
 * For other successful queries mysqli_query() will return TRUE.
 * Returns FALSE on failure.
 */
function query_get_something($sql){
    global $connection;
    mysqli_real_escape_string($connection , $sql);
    $result = mysqli_query($connection , $sql);
    if ($result == false){
        dieSafely("Mysql1 - ".mysqli_error($connection));
    }
    return $result;
}

function get_db_names(){
    $sql = "SHOW DATABASES;";
    $db_names = query_get_db_or_table_names($sql,null);
    return $db_names;
}
function get_table_names($db){
    $sql1 = "USE ".$db.";";
    $sql2 = "SHOW TABLES;"; //todo bug asap
    $table_names = query_get_db_or_table_names( $sql1 , $sql2);
    return $table_names;
}

/**
 * @param $sql_db string for "USE database_name;" query
 * @param $sql_t string for "SHOW CREATE TABLE table_name;" query
 * @return array
 */
function query_get_db_or_table_names($sql_db, $sql_t){
    if (!isset($sql_t)){
        // case 1 - get database names
        $result = query_get_something($sql_db);
    }else{
        // case 2 - get table names
        $result = query_get_something($sql_db);
        $result  = query_get_something($sql_t);
    }
    $iter=0;
    $names = null; //todo useless ? research
    while ($row = mysqli_fetch_array($result)){
        $names[$iter] = "$row[0]";
        $iter++;
    }
    return $names;
}