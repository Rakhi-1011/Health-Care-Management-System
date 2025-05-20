<?php
include 'connect.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

function generateRandomPassword($length = 5) {
    return substr(str_shuffle('12345'), 0, $length);
}

if (isset($_POST['signUp'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);

    // Check if the email already exists
    $checkEmail = "SELECT * FROM patient WHERE email='$email'";
    $result = $conn->query($checkEmail);

    if ($result->num_rows > 0) {
        echo "Email already exists!";
        header("Location: regis.html");
        exit();
    } else {
        // Generate a random password
        $plainPassword = generateRandomPassword(10);
        $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

        // Insert the patient into the database
        $insertQuery = "INSERT INTO patient (firstName, email, password, designation)
                        VALUES ('$username', '$email', '$hashedPassword', 'patient')";

        if ($conn->query($insertQuery) === TRUE) {
            $tableNamePrefix = preg_replace('/[^a-zA-Z0-9_]/', '_', strtolower($email));
            $diseasesTable = $tableNamePrefix . "_diseases";
            $prescriptionsTable = $tableNamePrefix . "_prescriptions";

            // Create diseases table
            $createDiseasesTableQuery = "CREATE TABLE IF NOT EXISTS `$diseasesTable` (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                diseases VARCHAR(1000),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";

            // Create prescriptions table
            $createPrescriptionsTableQuery = "CREATE TABLE IF NOT EXISTS `$prescriptionsTable` (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(200),
                prescription_image VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";

            // Execute the table creation queries
            if ($conn->query($createDiseasesTableQuery) === TRUE && $conn->query($createPrescriptionsTableQuery) === TRUE) {
                // Insert initial data into the diseases table (name and email)
                $insertDiseasesInfoQuery = "INSERT INTO `$diseasesTable` (name, email) VALUES ('$username', '$email')";
                if ($conn->query($insertDiseasesInfoQuery) !== TRUE) {
                    echo "Error inserting initial info into diseases table: " . $conn->error;
                }

                // Insert initial data into the prescriptions table (only email initially)
                $insertPrescriptionsInfoQuery = "INSERT INTO `$prescriptionsTable` (email) VALUES ('$email')";
                if ($conn->query($insertPrescriptionsInfoQuery) !== TRUE) {
                    echo "Error inserting initial info into prescriptions table: " . $conn->error;
                }

                // Send email with account credentials
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
                    $mail->addAddress($email, $username);
                    $mail->isHTML(true);

                    $mail->Subject = 'Your Account Credentials';
                    $mail->Body     = "Hi <b>$username</b>,<br><br>Your account has been created. <br><br>
                                        <b>Login Email:</b> $email <br>
                                        <b>Password:</b> $plainPassword <br><br>
                                        Please change your password after logging in.";

                    $mail->send();
                    header("Location: newloginpage.html");
                    exit();
                } catch (Exception $e) {
                    echo "Mailer Error: " . $mail->ErrorInfo;
                }
            } else {
                echo "Error creating tables: " . $conn->error;
            }
        } else {
            echo "Database Error: " . $conn->error;
        }
    }
}
?>
