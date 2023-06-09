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
    if(empty($_POST['contribution_amount'])){

        $response['error']=true;
        $response['message']="Missing Fields";
    
    }else{
            
        //sanitize the inputs
        $contribution_amount = htmlspecialchars($_POST['contribution_amount'], ENT_QUOTES, 'UTF-8');  

        $db= new DbOperation();

        $chamaId = $_GET['chama_id'];
        $userId=$_GET['user_id'];

        if ($db) {
            $contribution = $db->makeContribution($chamaId,$userId,$contribution_amount);

            if($contribution){
            $response['error']=false;
            $response['message']=$contribution;
        }else{
            $response['error']=true;
            $response['message']="Error making contribution";
        }
        }else{
            
        }

    }
}else{
    $response['error']=true;
    $response['message']="Invalid Request";
}

echo json_encode($response);

?>