<?php

// Start the session
session_start();
$sid=session_id();
// Path to access the functions in DbConnect
require_once '../API/DbOperations.php';

$response = array();

// Check for correct request method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $firstName = htmlspecialchars($_POST['firstName'], ENT_QUOTES, 'UTF-8');
    $lastName = htmlspecialchars($_POST['lastName'], ENT_QUOTES, 'UTF-8');    
    $userName = htmlspecialchars($_POST['userName'], ENT_QUOTES, 'UTF-8');
    $phoneNumber = htmlspecialchars($_POST['phoneNumber'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $gender = htmlspecialchars($_POST['gender'], ENT_QUOTES, 'UTF-8');   

        $db = new DbOperation();

        $userId = $_GET['user_id'];

        if ($db) {
            $updatedUser = $db->updateUser($userId, $firstName, $lastName, $userName, $phoneNumber, $email, $gender);

            if ($updatedUser) {
                $response['error'] = false;
                $response['message'] = "User Updated Successfully";
            } else {
                $response['error'] = true;
                $response['message'] = "Error updating user";
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
