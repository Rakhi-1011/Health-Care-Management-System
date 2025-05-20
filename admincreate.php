<?php
include 'connect.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

function generateRandomPassword($length = 5) {
    return substr(str_shuffle('0123456789'), 0, $length);
}

if (isset($_POST['signUp'])) {
    $name = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);

    echo "Connected to database: " . $conn->query("SELECT DATABASE()")->fetch_row()[0] . "<br>";

 
    $checkEmail = "SELECT * FROM doctor WHERE email='$email'";
    $result = $conn->query($checkEmail);

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already exists!'); window.location.href='admincreate.html';</script>";
        exit();
    } else {
        $plainPassword = generateRandomPassword(5);
        $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);


        $insertQuery = "INSERT INTO doctor (name, email, password) VALUES ('$name', '$email', '$hashedPassword')";
        echo "Query: $insertQuery<br>"; 
        if ($conn->query($insertQuery) === TRUE) {
            echo "Data inserted into doctor table.<br>"; 

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'heavenhealth47@gmail.com'; 
                $mail->Password = 'imfwbvwmxvxeubrd';
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;

                $mail->setFrom('heavenhealth47@gmail.com', 'Health Heaven');
                $mail->addAddress($email, $name);
                $mail->isHTML(true);

                $mail->Subject = 'Your Account Credentials';
                $mail->Body    = "Hi <b>$name</b>,<br><br>Your account has been created. <br><br>
                                  <b>Login Email:</b> $email <br>
                                  <b>Password:</b> $plainPassword <br><br>
                                  Please log in and update your profile.";

                $mail->send();
                echo "<script>alert('Doctor account created successfully! Credentials have been sent to the email.'); window.location.href='admincreate.html';</script>";
                exit();
            } catch (Exception $e) {
                echo "Mailer Error: " . $mail->ErrorInfo;
            }
        } else {
            echo "Database Error: " . $conn->error . "<br>";
        }
    }
}
?>
