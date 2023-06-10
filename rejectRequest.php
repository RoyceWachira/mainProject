<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Start the session
session_start();

//path to access the functions in DbConnect
require_once '../API/DbOperations.php';

$response = array();

//check for the correct request method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $db = new DbOperation();

        if ($db) {
            $chamaId = $_GET['chama_id'];
            $requestId = $_GET['request_id'];
            $userId = $_GET['user_id'];

            if (!$db->ifRequestExist($requestId)) {
                $response['error'] = true;
                $response['message'] = "This record doesn't exist";
            } else {
                if ($db->rejectToJoinChama($chamaId, $userId, $requestId)) {
                    $response['error'] = false;
                    $response['message'] = "Request Denied Successfully";
                } else {
                    $response['error'] = true;
                    $response['message'] = "Error Rejecting member";
                }
            }
        } else {
            $response['error'] = true;
            $response['message'] = "Database connection error";
        }
    } catch (Exception $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    }
} else {
    $response['error'] = true;
    $response['message'] = "Invalid Request";
}

echo json_encode($response);
?>
