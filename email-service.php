<?php

require_once "DbConnect.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

class EmailUtilsOperation
{
    private $con;

    // constructor which aids in db connection
    function __construct()
    {
        require_once dirname(__FILE__) . '/DbConnect.php';
        $db = new DbConnect();
        $this->con = $db->connect();
    }

    public function forgotPassword($email)
    {
        $stmt = $this->con->prepare("SELECT user_id, first_name, last_name FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            throw new Exception("Error executing the SELECT statement: " . $stmt->error);
        } else {
            $stmt->bind_result($userId, $firstName, $lastName);
            $stmt->fetch();
            $stmt->close();
        }

        if (!$userId) {
            echo "User with email $email not found.";
            return;
        }

        // generate a random token
        $token = bin2hex(random_bytes(16));

        // insert the token into the reset_password_tokens table
        $expiryDate = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        $insertStmt = $this->con->prepare("INSERT INTO reset_password_tokens (user_id, token, expirationDate) VALUES (?, ?, ?)");
        $insertStmt->bind_param("iss", $userId, $token, $expiryDate);
        if (!$insertStmt->execute()) {
            throw new Exception("Error executing the INSERT statement: " . $insertStmt->error);
        } else {
            $insertStmt->close();
        }


        // send the email
        $mail = new PHPMailer(true);

        try {
            $mail->SMTPDebug  = 2;
            $mail->isSMTP(); // Set mailer to use SMTP
            $mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers
            $mail->SMTPAuth=true;
            $mail->Username = 'projectchama66@gmail.com';
            $mail->Password = 'jsfefsuphnejtjuz';
            $mail->SMTPSecure = 'ssl'; // Enable TLS encryption
            $mail->Port = 465; // TCP port to connect to

            $mail->setFrom('projectchama66@gmail.com');
            $mail->addAddress($email);

            $mail->isHTML(true); // Set email format to HTML

            // Email subject and body content
            $mail->Subject = 'Password Reset Notification';
            $email_template= "
                <h2>Hello $firstName $lastName</h2>
                <h3>You are receiving this email because we received a password reset request for your account.</h3>
                <p>Please click the link below to reset your password:</p>
                <a href='http://localhost/Api/reset-password.php?token=$token'>Click Me</a>
                <br><br>
                <p>Note that the link will expire in 15 minutes.</p>
            ";
            $mail->Body = $email_template;

            if (!$mail->send()) {
                echo 'Mailer Error: ' . $mail->ErrorInfo;
                return false;
            } else {
                echo 'An email has been sent to your email address';
                return true;
            }

        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

    }
}
if (isset($_POST['password_reset'])){
    $new_password= mysqli_real_escape_string($this->con,$_POST['password']);
    $confirm_password= mysqli_real_escape_string($this->con, $_POST['cpassword']);
    $token= mysqli_real_escape_string($this->con, $_POST['password_token']);
    $response = array();
    $time = date('Y-m-d H:i:s');
    if(!empty($token)){
        if(!empty($new_password) && !empty($confirm_password)){
         $stmt= $this->con->prepare("SELECT expirationDate, user_id FROM reset_password_tokens WHERE token = ? ");
         $stmt->bind_param("s", $token); 
         if (!$stmt->execute()) {
            throw new Exception("Error executing the SELECT statement: " . $stmt->error);
        } else {
            $stmt->bind_result($expiry, $userID);
            $stmt->fetch();
            $stmt->close();
        }
         if($time < $expiry){
            if($new_password== $confirm_password){
                $stmt = $this->con->prepare("UPDATE user SET password='$new_password' WHERE user_id = ? ");
                $stmt->bind_param("s", $userID);
                $stmt->execute();
                $response['error'] = false;
                $response['message'] = 'New Password Updated Successfully...';
            }else{
                $response['error'] = true;
                $response['message'] = 'Password and Confirm Password do not match';
            }

         }else{
            $response['error'] = true;
            $response['message'] = 'Invalid Token';
         }
         
        }else{
            $response['error'] = true;
            $response['message'] = 'Please Fill in all required fields';
        }
    }else{
        $response['error'] = true;
        $response['message'] = 'No Token Available';
    }
        if (isset($_POST['password_reset'])){
            $new_password= mysqli_real_escape_string($this->con,$_POST['password']);
            $confirm_password= mysqli_real_escape_string($this->con, $_POST['cpassword']);
            $token= mysqli_real_escape_string($this->con, $_POST['password_token']);
            $response = array();
            $time = date('Y-m-d H:i:s');
            if(!empty($token)){
                if(!empty($new_password) && !empty($confirm_password)){
                 $stmt= $this->con->prepare("SELECT expirationDate, user_id FROM reset_password_tokens WHERE token = ? ");
                 $stmt->bind_param("s", $token); 
                 if (!$stmt->execute()) {
                    throw new Exception("Error executing the SELECT statement: " . $stmt->error);
                } else {
                    $stmt->bind_result($expiry, $userID);
                    $stmt->fetch();
                    $stmt->close();
                }
                 if($time < $expiry){
                    if($new_password== $confirm_password){
                        $stmt = $this->con->prepare("UPDATE user SET password='$new_password' WHERE user_id = ? ");
                        $stmt->bind_param("s", $userID);
                        $stmt->execute();
                        $response['error'] = false;
                        $response['message'] = 'New Password Updated Successfully...';
                    }else{
                        $response['error'] = true;
                        $response['message'] = 'Password and Confirm Password do not match';
                    }

                 }else{
                    $response['error'] = true;
                    $response['message'] = 'Invalid Token';
                 }
                 
                }else{
                    $response['error'] = true;
                    $response['message'] = 'Please Fill in all required fields';
                }
            }else{
                $response['error'] = true;
                $response['message'] = 'No Token Available';
            }

        }
        echo json_encode($response);
}
?>