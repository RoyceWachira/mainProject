<?php

// Start the session
session_start();
$sid = session_id();

// Path to access the functions in DbConnect
require_once '../API/DbOperations.php';

$response = array();

// Check for correct request method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new DbOperation();

    $chamaId = $_GET['chama_id'];

    if ($db) {
        try {
            
            $allocate= $db->performAllocation($chamaId);

            if ($allocate) {
                $response['error'] = false;
                $response['message'] = "Allocation initiated successfully";
            } else {
                $response['error'] = true;
                $response['message'] = "Error allocating funds";
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
