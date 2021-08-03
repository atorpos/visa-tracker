<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    include_once 'dbconnect.php';
    include_once 'visacases.php';

    $database   = new dbconnect();
    $db         =   $database->getConnection();

    $record     =   new visacases($db);

    $stmt   =   $record->read();
