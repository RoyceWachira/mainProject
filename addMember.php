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
    if(empty($_POST['chama_id']) or empty($_POST['user_id']) ){

        $response['error']=true;
        $response['message']="Required fields are missing";
    }else{
       
        try {
            if (isset($_SESSION['username'])) {  
                

              $db = new DbOperation();

              $chama_id = $_GET['chama_id'];
              $user_id = $_GET['user_id'];
              
          
              if ($db->ifMemberExist($user_id)){
                $response['error']=true;
                $response['message']="Member already Exists";  
                
              }else if($db->addUserToChama($chama_id, $user_id)) {
                echo "Member added successfully";
                
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