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

        $chamaId= $_GET['chama_id'];

        if ($db) {
            $allrequests = $db->getJoinRequests($chamaId);

            if ($allrequests) {
                $response['error'] = false;
                $response['allrequests'] = $allrequests;
            } else {
                $response['error'] = true;
                $response['message'] = "No pending requests";
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
