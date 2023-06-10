<?php

// Start the session
session_start();

//path to access the fuctions in DbConnect
require_once '../API/DbOperations.php';

$response= array();

//check for correct request method
if($_SERVER['REQUEST_METHOD']=='POST'){

    //check for fields if set or empty
    if(empty($_POST['chama_name']) or empty($_POST['description']) or empty($_POST['contribution_period']) or empty($_POST['system_flow']) or empty($_POST['contribution_target']) ){

        $response['error']=true;
        $response['message']="Required fields are missing";
    }else{

        //sanitize the inputs
        $chama_name = htmlspecialchars($_POST['chama_name'], ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8'); 
        $contribution_period= htmlspecialchars($_POST['contribution_period'], ENT_QUOTES, 'UTF-8'); 
        $system_flow= htmlspecialchars($_POST['system_flow'], ENT_QUOTES, 'UTF-8'); 
        $contribution_target= htmlspecialchars($_POST['contribution_target'], ENT_QUOTES, 'UTF-8');

        $db= new DbOperation();

        $user_id=$_GET['user_id'];

        if($db->chamaExists($chama_name)){
            $response['error']=true;
            $response['message']="Chama already Exists";
            
        }else{
            if($db->createChama($chama_name, $description,$user_id,$contribution_period, $contribution_target,$system_flow)){
                $response['error']=false;
                $response['message']="Chama Created Successfully";
            }
        }
           
    
    }

}else{
    $response['error']=true;
    $response['message']="Invalid Request";
}

echo json_encode($response);

?>