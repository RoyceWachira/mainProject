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
            $loans = $db->getMemberLoans($chamaId,$userId);

            if ($loans) {
                $response['error'] = false;
                $response['loans'] = $loans;
            } else {
                $response['error'] = true;
                $response['message'] = "Error fetching loans";
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
