<?php
    $servername = "localhost";
    $username = "root";
    $password = "va1f4iz0";
    $dbname   = "sys";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    echo "Connected successfully";

    $file = file_get_contents('json/eb51.json');
    $decode_json = json_decode($file);

    foreach ($decode_json as $v) {
        $sql = sprintf("INSERT INTO eb5table (case_id, case_status, update_date) VALUES('%s', '%s', '%s')",$v->file_no, htmlspecialchars($v->case_status, ENT_QUOTES), strtotime($v->case_date));

        var_dump($sql);
        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    $conn->close();