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
    $db = new DbOperation();

    $chamaId = $_GET['chama_id'];
    $userId= $_GET['user_id'];

    if ($db) {
        try {

            if ($db->leaveChama($chamaId,$userId)) {
                $response['error'] = false;
                $response['message'] = "You have left Chama Successfully";
            } else {
                $response['error'] = true;
                $response['message'] = "Error leaving Chama";
            }
        } catch (Exception $e) {
            $response['error'] = true;
            $response['message'] = "Error: " . $e->getMessage();
        }
    } else {
        $response['error'] = true;
        $response['message'] = "Database connection error";
    }
} else {
    $response['error'] = true;
    $response['message'] = "Invalid Request";
}

echo json_encode($response);

?>