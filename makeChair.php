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

        try {
            $db = new DbOperation();
    
            if ($db) {
        $chamaId = $_GET['chama_id'];
        $memberId = $_GET['member_id'];
        $userId= $_GET['user_id'];

            if($db->makeChairperson(
            $chamaId,
            $memberId,
            $userId
        )){
            $response['error']=false;
            $response['message']="New ChairPerson changed Successfully";
        }else{
            $response['error'] = true;
            $response['message'] = "Error changing Chairperson";
        }
    }else{
        $response['error'] = true;
        $response['message'] = "Database connection error";
    }
    }catch(Exception $e){
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    }


}else{
    $response['error']=true;
    $response['message']="Invalid Request";
}

echo json_encode($response);

?>