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
    if(empty($_POST['withdrawalReason']) || empty($_POST['withdrawalAmount'])){

        $response['error']=true;
        $response['message']="Missing Fields";
    
    }else{
            
        //sanitize the inputs
        $withdrawalAmount = htmlspecialchars($_POST['withdrawalAmount'], ENT_QUOTES, 'UTF-8');  
        $withdrawalReason = htmlspecialchars($_POST['withdrawalReason'], ENT_QUOTES, 'UTF-8');  

        $db= new DbOperation();

        $chamaId = $_GET['chama_id'];
        $userId=$_GET['user_id'];

        if ($db) {
            try{
            $withdrawal = $db->makeWithdrawal($chamaId,$userId,$withdrawalAmount,$withdrawalReason);

            if($withdrawal){
            $response['error']=false;
            $response['message']=$withdrawal;
        }else{
            $response['error']=true;
            $response['message']="Error making contribution";
        }
    } catch (Exception $e) {
        $response['error'] = true;
        $response['message'] = "Error: " . $e->getMessage();
    }
}}
    
}else{
    $response['error']=true;
    $response['message']="Invalid Request";
}

echo json_encode($response);

?>