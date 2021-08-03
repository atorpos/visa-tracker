<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    include_once '../config/dbconnect.php';
    include_once  '../objects/visacases.php';
    include_once  '../objects/visacasesupdate.php';

    $get_value      =   $_GET;
    $database       =   new dbconnect();
    $db             =   $database->getConnection();
    $product        =   new visacases($db);
    $updatecases    =   new visacasesupdate($db);
    if(!$get_value['case_id']) {
        $stmt       =   $product->read();
    }
    if($get_value['case_id']) {
        $stmt   =   $product->findbyid($get_value['case_id']);
    }
    if($get_value['case_status']) {
        $stmt   =   $product->findbystatus($get_value['case_status']);
    }

    $num        =   $stmt->rowCount();
    if($num>0){
        $visa_array = array();
        $visa_array["records"]=array();
        $record_visa = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($record_visa as $_v) {
            if(is_null($_v['approve_date'])) {
                $approve_date = 'NA';
            } else {
                $approve_date = gmdate("Y-m-d\ H:i:s\Z", $_v['approve_date']);
            }
            $visa_item = [
                'id'        =>  $_v['id'],
                'case_id'   =>  $_v['case_id'],
                'case_status'   =>  $_v['case_status'],
                'case_update_date'  =>  gmdate("Y-m-d\ H:i:s\Z", $_v['update_date']),
                'case_final_date'   =>  $approve_date,
            ];
            array_push($visa_array["records"], $visa_item);
        }
        http_response_code(200);
//        echo json_encode($visa_array);
        var_dump($visa_array["records"][0]["case_status"]);

    } else {
        http_response_code(404);
        echo json_encode(
            array("message" => "No record found.")
        );
    }