<?php
require_once '../API/email-service.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'];
        $response = array();
        
        $db= new EmailUtilsOperation();
        try{
        if ($db->forgotPassword($email)) {
            $response['error'] = false;
            $response['message'] = 'A reset link has been sent to your email';
        } else {
            $response['error'] = true;
            $response['message'] = 'User with this email not found';
        }
    }catch(Exception $e){
        $response['error'] = true;
        $response['message'] =  $e->getMessage();
    }

    }
    echo json_encode($response);
?>
