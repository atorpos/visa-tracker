<?php
    include_once '../config/dbconnect.php';
    include_once  '../objects/visacases.php';
    include_once  '../objects/visacasesupdate.php';

//    $post_contents = $_POST;
    $file_contents = file_get_contents("php://input");
    $incoming_text = json_decode($file_contents, TRUE);

    switch($incoming_text['message']['chat']['id']){
        case '-591635630':
            $database       =   new dbconnect();
            $db             =   $database->getConnection();
            $product        =   new visacases($db);
            $updatecases    =   new visacasesupdate($db);

            $messageto = $incoming_text['message']['chat']['id'];
            $received_text = str_replace("@i526bot ", "", $incoming_text['message']['text']);

            $stmt   =   $product->findbyid($received_text);

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
                $text_content = '<a href="tg://user?id='.$incoming_text['message']['from']['id'].'">@'.$incoming_text['message']['from']['first_name'].  '</a> Case: '. $received_text. ' '. $visa_array["records"][0]["case_status"];

            } else {
                $text_content = '<a href="tg://user?id='.$incoming_text['message']['from']['id'].'">@'.$incoming_text['message']['from']['first_name'].  '</a> Case not found please try again';
            }

            $chatcontent = array(
                "chat_id"   =>  $messageto,
                "text"      =>  $text_content,
                "parse_mode"    =>  "HTML"
            );


            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/{apikey}/sendMessage");
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
            break;
        default:
            break;
    }
$myfile = fopen("/var/www/vhost/dev.altawoz.com/public_html/eb5/utilites/logs/chat.txt", "w");
fwrite($myfile, $result);
fclose($myfile);
