<?php

    $servername = "testingdb.cnurlrknw76w.us-west-2.rds.amazonaws.com";
    $username = "admin";
    $password = "xepxub-danwat-2qAxfi";
    $dbname   = "visa_container";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
        var_dump($conn->connect_error);
    }

    $sql = sprintf("SELECT case_id FROM eb5table where approve_date is null order by case_id asc");
    $result = $conn->query($sql);
    $conn->close();
    $json_var = [];
    foreach ($result->fetch_all(MYSQLI_ASSOC) as $_w) {
        var_dump($_w);
        $_v['case_id'] = $_w['case_id'];
        array_push($json_var, $_v);
    }

    $myfile = fopen("/var/www/vhost/dev.altawoz.com/public_html/eb5/json/cases.json", "w");
    fwrite($myfile, json_encode($json_var));
    fclose($myfile);
    $output = shell_exec(sprintf('/bin/python3 /home/centos/eb5_statusupdate.py'));

    $result_json_decode = json_decode($output);
    if(count($result_json_decode) === 0){
        exit();
    }
    $update_conn = new mysqli($servername, $username, $password, $dbname);
    if ($update_conn->connect_error) {
        die("Connection failed: " . $update_conn->connect_error);
    }
    $array_list = "";
    foreach ($result_json_decode as $_v) {
        $sql = sprintf("SELECT * FROM eb5table where case_id = '%s' limit 1", $_v->file_no);
        $result = $update_conn->query($sql);
        if($_v->case_status === 'Case Was Approved') {
            $array_list =  $array_list. $_v->file_no. " the case status ". $_v->case_status. "; ";
        }


        foreach ($result->fetch_all(MYSQLI_ASSOC) as $_w) {
            $update_query = "UPDATE eb5table set `approve_date` =". strtotime($_v->case_date)." where id =". $_w['id'];
            $update_conn->query($update_query);
            $update_status_query = sprintf("UPDATE eb5table set `case_status`='%s' where id='%s'", $_v->case_status, $_w['id']);
            $update_conn->query($update_status_query);
            $insert_sql = sprintf("INSERT INTO eb5update (case_id, case_status, create_time, eb5table_id) VALUES('%s', '%s', '%s', '%s')", $_v->file_no, htmlspecialchars($_v->case_status, ENT_QUOTES), strtotime($_v->case_date), $_w['id']);
            $update_conn->query($insert_sql);

        }

    }
    $chatcontent = array(
        "chat_id"   =>  '-591635630',
        "text"      =>  $array_list,
        "parse_mode"    =>  'HTML'
    );

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot1704379973:AAEGczf9-eUPqo5-DoE6eAL4_NEym3--O9k/sendMessage");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($chatcontent));

    $headers = array();
    $headers[] = 'Content-Type: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    exit();