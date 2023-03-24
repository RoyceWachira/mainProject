<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);
// Start the session
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];
        $fileName = basename($file['name']);
        $fileContent = file_get_contents($file['tmp_name']);
        $fileSize = $file['size'];
        $fileType = $file['type'];

        //get user_id from currently logged in user
        $userId = $_SESSION['userId'];

        //connect to the database
        $con = mysqli_connect('localhost', 'root', '', 'userdata');

        // Get the current datetime in MySQL datetime format
        $date = date('Y-m-d H:i:s');

        // Insert the file into the database
        $stmt = $con->prepare("INSERT INTO `files` (`user_id`, `file_name`, `file_type`, `file_content`, `file_size`,`created_at`) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issbis", $userId, $fileName, $fileType, $fileContent, $fileSize,$date);
    
        if ($stmt->execute()) {
            echo 'The file ' . $fileName . ' has been uploaded and stored in the database.';
        } else {
            echo 'Sorry, there was an error uploading your file.';
        }
    } else {
        echo 'No file was uploaded.';
    }
}


?>
