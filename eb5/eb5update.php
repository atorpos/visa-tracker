<?php
    $file = file_get_contents('{file_location}');
    $decode_json = json_decode($file);
    $servername = "{server_address}";
    $username = "{user}";
    $password = "{password}";
    $dbname   = "{dbname}";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    echo "Connected successfully";



    foreach ($decode_json as $_v){
        $sql = sprintf("SELECT * FROM eb5table where case_id = '%s' limit 1", $_v->file_no);

        var_dump($sql);
        $result = $conn->query($sql);

        foreach ($result->fetch_all(MYSQLI_ASSOC) as $_w) {
            $update_query = "UPDATE eb5table set `approve_date` =". strtotime($_v->case_date)." where id =". $_w['id'];
            $conn->query($update_query);
            $update_status_query = sprintf("UPDATE eb5table set `case_status`='%s' where id='%s'", $_v->case_status, $_w['id']);
            $conn->query($update_status_query);
            $insert_sql = sprintf("INSERT INTO eb5update (case_id, case_status, create_time, eb5table_id) VALUES('%s', '%s', '%s', '%s')", $_v->file_no, htmlspecialchars($_v->case_status, ENT_QUOTES), strtotime($_v->case_date), $_w['id']);
            $conn->query($insert_sql);
        }

    }


//    $result->fetch_all(MYSQLI_ASSOC);
//    var_dump($result); exit();
//    foreach ($result->fetch_all(MYSQLI_ASSOC) as $_v) {
//        $update_query = "UPDATE eb5table set `approve_date` =". $_v['update_date']." where id =". $_v['id'];
//        $conn->query($update_query);
//        var_dump($conn);
//    }

