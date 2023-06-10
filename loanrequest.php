<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);
// Start the session
session_start();

//path to access the fuctions in DbConnect
require_once '../API/DbOperations.php';
$response= array();

//check for correct request method
if($_SERVER['REQUEST_METHOD']=='POST'){

    //check for fields if set or empty
    if(empty($_POST['loanAmount'])or empty($_POST['loanRepayPeriod'])){

        $response['error']=true;
        $response['message']="Missing Fields";
    
    }else{
            
        //sanitize the inputs
        $loanAmount = htmlspecialchars($_POST['loanAmount'], ENT_QUOTES, 'UTF-8');
        $loanRepayPeriod = htmlspecialchars($_POST['loanRepayPeriod'], ENT_QUOTES, 'UTF-8');   

        $db= new DbOperation();

        $chamaId = $_GET['chama_id'];
        $userId = $_GET['user_id'];

            if($db->requestLoan(
            $chamaId,
            $userId,
            $loanAmount,
            $loanRepayPeriod
        )){
            $response['error']=false;
            $response['message']="Loan Requested Successfully";
        }

    }
}else{
    $response['error']=true;
    $response['message']="Invalid Request";
}

echo json_encode($response);

?>