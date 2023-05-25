<?php

// Start the session
session_start();

//path to access the fuctions in DbConnect
require_once '../API/DbOperations.php';

$response= array();

//check for correct request method
if($_SERVER['REQUEST_METHOD']=='POST'){

    //check for fields if set or empty
    if(empty($_POST['fineAmount']) or empty($_POST['fineReason'])){

        $response['error']=true;
        $response['message']="Required fields are missing";
    }else{

        //sanitize the inputs
        $fineAmount = htmlspecialchars($_POST['fineAmount'], ENT_QUOTES, 'UTF-8');
        $fineReason = htmlspecialchars($_POST['fineReason'], ENT_QUOTES, 'UTF-8'); 

        $db= new DbOperation();

        $userId=$_GET['user_id'];
        $chamaId=$_GET['chama_id'];

        if($db->chargeFine($chamaId, $userId, $fineAmount, $fineReason)){
                $response['error']=false;
                $response['message']="Fine Charged Successfully";
            }else{
                $response['error']=true;
                $response['message']="Error charging fine";    
            }
           
    
    }

}else{
    $response['error']=true;
    $response['message']="Invalid Request";
}

echo json_encode($response);

?>