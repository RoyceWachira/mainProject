<?php
require_once '../API/email-service.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'];
        $response = array();
        
        $db= new EmailUtilsOperation();


        if ($db->forgotPassword($email)) {
            $response['error'] = false;
            $response['message'] = 'Password reset email sent.';
        } else {
            $response['error'] = true;
            $response['message'] = 'User with this email not found';
        }
        echo json_encode($response);
    }
?>
