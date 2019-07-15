<?php

require_once 'lib/logger.php';
$connection = null;

/**
 * @param $args
 */
function task_export($args)
{
    global $data_too;
    export_structure($args);

    //temp solution for data export
    $data_too = true;
    if ($data_too) {
        export_data($args);
    }
}

function export_data($args)
{
    $table_names = $args['table_names'];
    // todo make function instead of SQL skeletons
    if (!is_array($table_names)) {
        //case table_names is string , cast it to array
        $array_buffer[0] = $table_names;
        $table_names = $array_buffer;
    }
    $i = 0;
//    dd($table_names);
    foreach ($table_names as $value_Table) {
        $data = get_all_rows_data($value_Table);
        foreach ($data as $value_Data) {
            $array_to_write[$i++] = build_sql_insert_into($value_Table, $value_Data);
        }
    }
    $delimiter = "\n";
    if (!isset($array_to_write)) { //todo remove ?
        dieSafely("BUG function export_data() array_to_write is null");
    }
    $string_to_write = implode($delimiter, $array_to_write);
    $string_to_write .= $delimiter;
    $disable_fkcheck = "SET FOREIGN_KEY_CHECKS=0;\n\n";
    $enable_fkcheck = "\nSET FOREIGN_KEY_CHECKS=1;\n";
    file_write_append($args['file_name'], $disable_fkcheck);
    file_write_append($args['file_name'], $string_to_write);
    file_write_append($args['file_name'], $enable_fkcheck);
}

function export_structure($args)
{
    $table_names = $args['table_names'];
    if (!is_array($table_names)) {
        //case table_names is string , cast it to array
        $array_buffer[0] = $table_names;
        $table_names = $array_buffer;
    }
//    // todo overkill --- start --- ???
//    if (!is_array($table_names)){
//        dd($table_names , "BUG 1");
//    }
//    // todo overkill --- end --- ???
    foreach ($table_names as $key => $value) {
        $sql_array[$key] = build_sql_show_create_table($value);
    }
    $iter = 0;
    /** @var array $sql_array each row represents table structure recreation query */
    foreach ($sql_array as $key => $value) {
        $result = query_get_something($value);
        while ($row = mysqli_fetch_array($result)) {
            $table_structure[$iter] = "$row[1]";
            $iter++;
        }
    }
    $delimiter = "; \n\n### END ###\n\n";
    $string_to_write = implode($delimiter, $table_structure);
    $string_to_write .= $delimiter;

    file_truncate($args['file_name']);
    file_write_append($args['file_name'], $string_to_write);
    log4("Export completed successfully");
}

function task_import($args){
    dd("!!! import");
}

function file_truncate($file_name)
{
    $file = fopen("$file_name", 'a');
    fclose($file);
}

function file_write_append($file_name, $data)
{
    $file = fopen("$file_name", 'a');
    if (!fwrite($file, "$data"))
        dieSafely("file error");
    fclose($file);
}

function build_sql_show_create_table($table_name)
{
    $sql_skeleton = "SHOW CREATE TABLE ";
    return $sql_skeleton . $table_name . ";";
}

function build_sql_insert_into($table_name, $data)
{
    $sql_skeleton_1 = "INSERT INTO ";
    $sql_skeleton_2 = " VALUES (";
    $sql_skeleton_3 = ");";
    foreach ($data as &$item) {
        $item = addslashes($item);
        if ($item == "") {
            $item = "NULL";
        } else {
            $item = "'" . $item . "'";
        }
    }
    $data = implode(",", $data);
    return $sql_skeleton_1 . $table_name . $sql_skeleton_2 . $data . $sql_skeleton_3;
}

function get_db_names()
{
    $sql = "SHOW DATABASES;";
    $db_names = query_get_db_or_table_names($sql, null);
    return $db_names;
}

function get_table_names($db)
{
    $sql1 = "USE " . $db . ";";
    $sql2 = "SHOW TABLES;";
    $table_names = query_get_db_or_table_names($sql1, $sql2);
    return $table_names;
}

function get_all_rows_data($table)
{
    $sql_skeleton = "SELECT * FROM ";
    $sql_select = $sql_skeleton . $table . ";";
    $result = query_get_something($sql_select);
    $iter = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $data[$iter] = $row;
        $iter++;
    }
    return $data;
}

/**
 * @param $sql
 * @return mysqli_result|bool
 * For successful SELECT, SHOW, DESCRIBE or EXPLAIN queries, mysqli_query() will return a mysqli_result object.
 * For other successful queries mysqli_query() will return TRUE.
 * Returns FALSE on failure.
 */
function query_get_something($sql)
{
    global $connection;
    mysqli_real_escape_string($connection, $sql);
    $result = mysqli_query($connection, $sql);
    if ($result == false) {
        dieSafely("Mysql1 - " . mysqli_error($connection));
    }
    return $result;
}

/**
 * @param $sql_db string for "USE database_name;" query
 * @param $sql_t string for "SHOW CREATE TABLE table_name;" query
 * @return array
 */
function query_get_db_or_table_names($sql_db, $sql_t)
{
    if (!isset($sql_t)) {
        // case 1 - get database names
        $result = query_get_something($sql_db);
    } else {
        // case 2 - get table names
        $result = query_get_something($sql_db);
        $result = query_get_something($sql_t);
    }
    $iter = 0;
//    $names = null; //todo useless ? research
    while ($row = mysqli_fetch_array($result)) {
        $names[$iter] = "$row[0]";
        $iter++;
    }
    return $names;
}