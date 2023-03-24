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
    if(empty($_POST['chamaRole'])){

        $response['error']=true;
        $response['message']="Missing Fields";
    
    }else{

        if (isset($_SESSION['username'])) { 
            
        //sanitize the inputs
        $chamaRole = htmlspecialchars($_POST['chamaRole'], ENT_QUOTES, 'UTF-8');

        $db= new DbOperation();

        $chamaId = $_GET['chama_id'];
        $memberId = $_GET['member_id'];

            if($db->updateRole(
            $chamaId,
            $memberId,
            $chamaRole
        )){
            $response['error']=false;
            $response['message']="Role Updated Successfully";
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