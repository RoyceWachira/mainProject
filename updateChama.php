<?php

// Start the session
session_start();
$sid=session_id();
// Path to access the functions in DbConnect
require_once '../API/DbOperations.php';

$response = array();

// Check for correct request method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //check for fields if set or empty
    if(empty($_POST['chamaName']) or empty($_POST['chamaDescription']) or empty($_POST['contributionPeriod']) or empty($_POST['systemFlow']) or empty($_POST['contributionTarget']) ){

        $response['error']=true;
        $response['message']="Required fields are missing";
    }else{

        //sanitize the inputs
        $chamaName = htmlspecialchars($_POST['chamaName'], ENT_QUOTES, 'UTF-8');
        $chamaDescription = htmlspecialchars($_POST['chamaDescription'], ENT_QUOTES, 'UTF-8'); 
        $contributionPeriod= htmlspecialchars($_POST['contributionPeriod'], ENT_QUOTES, 'UTF-8'); 
        $systemFlow= htmlspecialchars($_POST['systemFlow'], ENT_QUOTES, 'UTF-8'); 
        $contributionTarget= htmlspecialchars($_POST['contributionTarget'], ENT_QUOTES, 'UTF-8'); 

        $db = new DbOperation();

        $chamaId = $_GET['chama_id'];

        if ($db) {
            $updatedChama = $db->updateChama($chamaName, $chamaDescription, $contributionPeriod, $contributionTarget, $systemFlow, $chamaId);

            if ($updatedChama) {
                $response['error'] = false;
                $response['message'] = "Chama Updated Successfully";
            } else {
                $response['error'] = true;
                $response['message'] = "Error updating chama";
            }
        } else {
            $response['error'] = true;
            $response['message'] = "Database connection error";
        }
    }
} else {
    $response['error'] = true;
    $response['message'] = "Invalid Request";
}

echo json_encode($response);

?>
