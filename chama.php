<?php

// Start the session
session_start();

//path to access the fuctions in DbConnect
require_once '../API/DbOperations.php';

$response= array();

//check for correct request method
if($_SERVER['REQUEST_METHOD']=='POST'){

    //check for fields if set or empty
    if(empty($_POST['chamaname']) or empty($_POST['description']) ){

        $response['error']=true;
        $response['message']="Required fields are missing";
    }else{
       

        if(isset($_SESSION['username'])){

        //sanitize the inputs
        $chamaname = htmlspecialchars($_POST['chama_name'], ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');    
        
        $db= new DbOperation();

        if($db->chamaExists($chamaname)){
            $response['error']=true;
            $response['message']="Chama already Exists";
            
        }else{
            if($db->createChama($chamaname, $description)){
                $response['error']=false;
                $response['message']="Chama Created!";
            }
        }
    }else{
        $response['error']=true;
        $response['message']="Please login ";       
    }
    }

}else{
    $response['error']=true;
    $response['message']="Invalid Request";
}

echo json_encode($response);

?>