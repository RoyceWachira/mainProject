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

        $userId = $_GET['user_id'];

        if ($db) {
            $chamas = $db->getUserJoinedChamas($userId);

            if ($chamas) {
                $response['error'] = false;
                $response['chamas'] = $chamas;
            } else {
                $response['error'] = true;
                $response['message'] = "Error fetching chamas";
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
