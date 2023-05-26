<?php

// Start the session
session_start();
$sid=session_id();
// Path to access the functions in DbConnect
require_once '../API/DbOperations.php';

$response = array();

// Check for correct request method
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

        $db = new DbOperation();

        $chamaId = $_GET['chama_id'];
        $userId= $_GET['user_id'];
        if ($db) {
            $fines = $db->getMemberFines($chamaId,$userId);

            if ($fines) {
                $response['error'] = false;
                $response['fines'] = $fines;
            } else {
                $response['error'] = true;
                $response['message'] = "No loans charged on you";
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
