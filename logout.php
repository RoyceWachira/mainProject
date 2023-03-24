<?php

session_start();
require_once '../API/DbOperations.php';
$response = array();

$db = new DbOperation();

if($_SERVER['REQUEST_METHOD']=='POST'){
if($db->logout()){
    $response['error'] = false;
    $response['message'] = "Successfully logged out.";
}else{
    $response['error'] = true;
    $response['message'] = "Error logging out.";
}
}else{
    $response['error']=true;
    $response['message']="Invalid Request";
}
echo json_encode($response);

?>