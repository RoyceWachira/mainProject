<?php

require_once "DbConnect.php";

// Define and initialize  a DbConnect object

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';





class EmailUtilsOperation
{

    private $con;
    //constuctor which aids in db connection
    function __construct()
    {
        require_once dirname(__FILE__) . '/DbConnect.php';

        $db = new DbConnect();

        $this->con = $db->connect();
    }


    public function forgotPassword($email, $token)
    {
        // $sql = "SELECT first_name, last_name FROM users WHERE email = ?";

        // $stmt = $this->con->prepare("SELECT first_name, last_name FROM users WHERE email = ?");
        // $stmt->bind_param("s", $email);
        // $stmt->execute();
        // $stmt->bind_result($firstName, $lastName);
        // $stmt->fetch();


        $mail = new PHPMailer(true);
        //Create an instance; passing `true` enables exceptions
        

        try {

            $mail->isSMTP(); // Set mailer to use SMTP
            $mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers
            $mail->SMTPAuth = true; // Enable SMTP authentication
            $mail->Username = 'marvinwachira6@gmail.com';
            $mail->Password = 'M@rvin#644';
            $mail->SMTPSecure = 'tls'; // Enable TLS encryption
            $mail->Port = 587; // TCP port to connect to

            $mail->setFrom('marvinwachira6@gmail.com', 'Mervin Wachira');
            $mail->addAddress($email);

            $mail->isHTML(true); // Set email format to HTML

            // Email subject and body content
            $mail->Subject = 'Password Reset';
            $mail->Body = '<p>Click the following link to reset your password:</p>'
                . '<a href="http://localhost/Api/reset-password.php?';

            if (!$mail->send()) {
                echo 'Mailer Error: ' . $mail->ErrorInfo;
            } else {
                echo 'An Email has been sent to your email address';
            }

            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

}
?>


if (isset(isset($_GET['token'])) {
        $email = $cuser->test_input($_GET['email']);
        $token = $cuser->test_input($_GET['token']);}