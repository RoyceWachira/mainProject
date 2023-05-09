<?php

// Start the session
session_start();

// Path to access the functions in DbConnect
require_once '../API/DbOperations.php';

$response = array();

// Check for correct request method
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_SESSION['username'])) {
        $db = new DbOperation();

        if ($db) {
            $users = $db->getAllUsers();

            if ($users) {
                $response['error'] = false;
                $response['message'] = "All users fetched";
                $response['users'] = $users;
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
        $response['message'] = "Please login";
    }
} else {
    $response['error'] = true;
    $response['message'] = "Invalid Request";
}

echo json_encode($response);

?>
