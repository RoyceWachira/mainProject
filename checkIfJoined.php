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
        $chamaId= $_GET['chama_id'];

        if ($db) {
            $user = $db->isJoined($userId,$chamaId);

            if ($user) {
                $response['error'] = false;
                $response['message'] = "This user is already a member";
            } else {
                $response['error'] = true;
                $response['message'] = "This user is not a member";
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
