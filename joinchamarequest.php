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

        $chamaId = $_GET['chama_id'];
        $userId = $_GET['user_id'];

        if($db->isJoined($userId,$chamaId)){

            $response['error']=true;
            $response['message']="You are already a member of this chama";

        }elseif($db->checkJoinRequestStatus($chamaId,$userId)){

            $response['error']=true;
            $response['message']="You already requested to join this chama";

        }else{
            if($db->requestToJoinChama(
                $chamaId,
                $userId  
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