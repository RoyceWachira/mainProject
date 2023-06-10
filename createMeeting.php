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

    //check for fields if set or empty
    if(empty($_POST['meetingDate']) or empty($_POST['meetingTime'])or empty($_POST['meetingVenue']) or empty($_POST['meetingPurpose']) ){

        $response['error']=true;
        $response['message']="Missing Fields";
    
    }else{

            
        //sanitize the inputs
        $meetingPurpose = htmlspecialchars($_POST['meetingPurpose'], ENT_QUOTES, 'UTF-8');
        $meetingTime = htmlspecialchars($_POST['meetingTime'], ENT_QUOTES, 'UTF-8');
        $meetingVenue = htmlspecialchars($_POST['meetingVenue'], ENT_QUOTES, 'UTF-8');    
        $meetingDate = htmlspecialchars($_POST['meetingDate'], ENT_QUOTES, 'UTF-8');

        $db= new DbOperation();

        $chamaId = $_GET['chama_id'];
        $userId = $_GET['user_id'];

            if($db->createMeeting(
            $meetingDate,
            $meetingTime,
            $meetingVenue,
            $meetingPurpose,
            $chamaId,
            $userId
        )){
            $response['error']=false;
            $response['message']="Meeting Created Successfully";
        }

    }
}else{
    $response['error']=true;
    $response['message']="Invalid Request";
}

echo json_encode($response);

?>