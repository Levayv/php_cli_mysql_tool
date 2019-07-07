<?php

echo "Test start\n";

$asd = ["import" , "export"];

foreach ($asd as &$item){
    $item = "-".$item;
}
unset($item);

var_dump($asd);

echo "Test finish\n";