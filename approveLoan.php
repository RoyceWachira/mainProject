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
    if(empty($_POST['chamaId']) or empty($_POST['loanId']) ){

        $response['error']=true;
        $response['message']="Required fields are missing";
    }else{
       
        try {
            if (isset($_SESSION['username'])) {  
                

              $db = new DbOperation();

              $chamaId = $_GET['chamaId'];
              $loanId = $_GET['loanId'];
              
          
              if (!$db->ifLoanExist($loanId)){
                $response['error']=true;
                $response['message']="This record doesn't exist";  
                
              }else if($db->approveLoan($chamaId, $loanId)) {
                echo "Loan Approved!";
                
              } else {
                  echo "Error adding member: " . $db->getLastError();
              }
            } else {
              $response['error'] = true;
              $response['message'] = "Please Login";
            }
          } catch (Exception $e) {
            $response['error'] = true;
            $response['message'] = $e->getMessage();
          }
}
}else{
    $response['error']=true;
    $response['message']="Invalid Request";
}

echo json_encode($response);

?>