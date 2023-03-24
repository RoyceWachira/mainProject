<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

//begin a session
session_start();

//path to access the functions in DbConnect
require_once '../API/DbOperations.php';
$response= array();

//check for correct request method
if($_SERVER['REQUEST_METHOD']=='POST'){
    if(empty($_POST['username']) or empty($_POST['password'])){

        $response['error']=true;
        $response['message']="Required fields are missing";

    }else{

        //sanitize the inputs
        $username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
        $pass = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');
        
        $db= new DbOperation();
        
        if($db->userLogin($username,$pass)==1){
            $user= $db->getUser($username);
            $response['error']=false;
            $response['message']="Login Successful!";
            $_SESSION['username']= $username;
            $_SESSION['userId']=$user['user_id'];
            $_SESSION['role']= $user['role'];
           
        }elseif($db->userLogin($username,$pass)==2){
            $user= $db->getUser($username);
            $response['error']=false;
            $response['message']="Welcome Admin!"; 
            $_SESSION['username']= $username;
            $_SESSION['userId']=$user['user_id'];
            $_SESSION['role']= $user['role'];
        }
        else{
            $response['error']=true;
            $response['message']="Invalid Username or Password";
        }

        

    }
}else{
       $response['error']=true;
       $response['message']="Invalid Request";
}

echo json_encode($response);

?>