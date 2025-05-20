<?php
session_start();
include 'connect.php';

if (isset($_POST['login'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $query = "SELECT * FROM patient WHERE email='$email'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {

            $_SESSION['patient_email'] = $user['email'];
            $_SESSION['username'] = $user['firstName'];
            $_SESSION['dashname'] = $user['firstName'];
            $_SESSION['designation'] = $user['designation'];

    
            if ($user['designation'] == 'patient') {
                header("Location: firstpage.php");
                exit();
            } elseif ($user['designation'] == 'admin') {
                header("Location: admindash.html");
                exit();
            }
        } else {
       
            echo "<script>alert('Invalid password. Please try again.'); window.location.href='newloginpage.html';</script>";
            exit();
        }
    } else {
        
        echo "<script>alert('No account found with this email. Please try again.'); window.location.href='newloginpage.html';</script>";
        exit();
    }
} else {
  
    echo "<script>alert('Unauthorized access! Please log in.'); window.location.href='newloginpage.html';</script>";
    exit();
}
?>