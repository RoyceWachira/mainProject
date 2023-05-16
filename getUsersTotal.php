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

        if ($db) {
            $totalCount = $db->getTotalUserCount();

            if ($totalCount) {
                $response['error'] = false;
                $response['totalUsers'] = $totalCount;

            } else {
                $response['error'] = true;
                $response['message'] = "Error fetching total";
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
