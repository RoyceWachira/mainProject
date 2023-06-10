<?php

// Start the session
session_start();
$sid=session_id();
// Path to access the functions in DbConnect
require_once '../API/DbOperations.php';
require_once('../Api/tcpdf/tcpdf.php');

$response = array();

// Check for correct request method
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

        $db = new DbOperation();

        $contributionId = $_GET['contribution_id'];
        $userId= $_GET['user_id'];

        if ($db) {
            $pdf = $db->printContribution($contributionId,$userId);

            if ($pdf) {
                $response['error'] = false;
                $response['pdf'] = $pdf;
            } else {
                $response['error'] = true;
                $response['message'] = "Error generating pdf";
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
