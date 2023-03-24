<?php

//path to access the fuctions in DbConnect
require_once '../API/DbOperations.php';
$response= array();

//check for correct request method
if($_SERVER['REQUEST_METHOD']=='POST'){

    //check for fields if set or empty
    if(empty($_POST['firstName']) or empty($_POST['lastName']) or empty($_POST['userName']) or empty($_POST['phoneNumber']) or empty($_POST['email']) or empty($_POST['gender']) or empty($_POST['password'])){

        $response['error']=true;
        $response['message']="Required fields are missing";     


}else{
        //checks for correct email format
        if(!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)){
            $response['error']=true;
            $response['message']="Invalid Email Address";          
        }else{

        //sanitize the inputs
        $firstName = htmlspecialchars($_POST['firstName'], ENT_QUOTES, 'UTF-8');
        $lastName = htmlspecialchars($_POST['lastName'], ENT_QUOTES, 'UTF-8');    
        $userName = htmlspecialchars($_POST['userName'], ENT_QUOTES, 'UTF-8');
        $phoneNumber = htmlspecialchars($_POST['phoneNumber'], ENT_QUOTES, 'UTF-8');
        $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
        $gender = htmlspecialchars($_POST['gender'], ENT_QUOTES, 'UTF-8');
        $password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8'); 
 
        $db= new DbOperation();

        //create an admin if they don't exist in db
        $db->createDefaultAdmin();

        //calls function to check if user exists in the db
        if($db->isUserExist($email)){
            $response['error']=true;
            $response['message']="User already Exists";
        }else{

            //calls function to register user
            if($db->createUser(
            $firstName,
            $lastName,
            $userName,
            $phoneNumber,
            $email,
            $gender,
            $password   
        )){
            $response['error']=false;
            $response['message']="User Registered Successfully";
        }
    
    
    }
 }   
}
}else{
    $response['error']=true;
    $response['message']="Invalid Request";
}

echo json_encode($response);

?>