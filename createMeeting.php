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
    if(empty($_POST['meetingDate']) or empty($_POST['meetingTime'])or empty($_POST['meetingVenue']) or empty($_POST['meetingPurpose']) or empty($_POST['chama_id']) ){

        $response['error']=true;
        $response['message']="Missing Fields";
    
    }else{

        if (isset($_SESSION['username'])) { 
            
        //sanitize the inputs
        $meetingPurpose = htmlspecialchars($_POST['meetingPurpose'], ENT_QUOTES, 'UTF-8');
        $meetingTime = date('H-i-s',strtotime(htmlspecialchars($_POST['meetingTime'], ENT_QUOTES, 'UTF-8')));
        $meetingVenue = htmlspecialchars($_POST['meetingVenue'], ENT_QUOTES, 'UTF-8');    
        $meetingDate = date('Y-m-d',strtotime(htmlspecialchars($_POST['meetingDate'], ENT_QUOTES, 'UTF-8')));
        $chama_id= htmlspecialchars($_POST['chama_id'], ENT_QUOTES, 'UTF-8');

        $db= new DbOperation();



            if($db->createMeeting(
            $meetingDate,
            $meetingTime,
            $meetingVenue,
            $meetingPurpose,
            $chama_id   
        )){
            $response['error']=false;
            $response['message']="Meeting Created Successfully";
        }

}else{
    $response['error']=true;
    $response['message']="Pleasee login";
}
    }
}else{
    $response['error']=true;
    $response['message']="Invalid Request";
}

echo json_encode($response);

?>