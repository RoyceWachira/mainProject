<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);
// Start the session
session_start();

//path to access the fuctions in DbConnect
require_once '../API/DbOperations.php';
$response= array();

//check for correct request method
if($_SERVER['REQUEST_METHOD']=='POST'){ 

        $db= new DbOperation();

        $chama_id = $_GET['chama_id'];
        $user_id = $_GET['user_id'];

        if($db->isJoined($user_id,$chama_id)){

            $response['error']=true;
            $response['message']="You are already a member of this chama";

        }elseif($db->checkJoinRequestStatus($chama_id,$user_id)){

            $response['error']=true;
            $response['message']="You already requested to join this chama";

        }else{
            if($db->requestToJoinChama(
                $chama_id,
                $user_id  
            )){
                $response['error']=false;
                $response['message']="Request Submitted Successfully";
            }

        }

    }else{
    $response['error']=true;
    $response['message']="Invalid Request";
}

echo json_encode($response);

?>