<?php

// Start the session
session_start();

//path to access the fuctions in DbConnect
require_once '../API/DbOperations.php';

$response= array();

//check for correct request method
if($_SERVER['REQUEST_METHOD']=='GET'){
           
    if(isset($_SESSION['username'])){

        $db= new DbOperation();

        if($db->getAllUsers()){
            $response['error']=false;
            $response['message']="All Users fetched";

        }else{
                $response['error']=true;
                $response['message']="Error fetching users";
    }

}else{
    $response['error']=true;
    $response['message']="Please login ";  
}

}else{
    $response['error']=true;
    $response['message']="Invalid Request";
}



echo json_encode($response);

?>