<?php
session_start();
include 'connect.php';

if (isset($_POST['login'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];


    $query = "SELECT * FROM doctor WHERE email='$email'";
    $result = $conn->query($query);

    if ($result) {
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

       
            if (password_verify($password, $user['password'])) {
                // Store doctor email in session
                $_SESSION['doctor_email'] = $user['email'];
                $_SESSION['doctor_name'] = $user['name'];
                header("Location: doctorhomepage.php");
                exit();
            } else {
            
                echo "<script>alert('Invalid password!'); window.location.href='doctorlogin.html';</script>";
                exit();
            }
        } else {
            
            echo "<script>alert('No account found with this email!'); window.location.href='doctorlogin.html';</script>";
            exit();
        }
    } else {
      
        echo "<script>alert('Database query failed!'); window.location.href='doctorlogin.html';</script>";
        exit();
    }
} else {
  
    echo "<script>alert('Unauthorized access!'); window.location.href='doctorlogin.html';</script>";
    exit();
}

if (!isset($_SESSION['doctor_email'])) {
    echo "<script>alert('Unauthorized access! Please log in.'); window.location.href='doctorlogin.html';</script>";
    exit();
}

$email = $_SESSION['doctor_email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $specialist = $conn->real_escape_string($_POST['specialist']);
    $experience = $conn->real_escape_string($_POST['experience']);
    $hospital_name = $conn->real_escape_string($_POST['hospital_name']);
    $aboutme = $conn->real_escape_string($_POST['aboutme']);
    $registration_number = $conn->real_escape_string($_POST['registration_number']);
    $time = $conn->real_escape_string($_POST['time']);

 
    $profilePicture = null;
    if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $profilePicture = $uploadDir . basename($_FILES['profilePicture']['name']);
        move_uploaded_file($_FILES['profilePicture']['tmp_name'], $profilePicture);
    }


    $updateQuery = "UPDATE doctor SET 
                    specialist='$specialist', 
                    experience='$experience', 
                    hospital_name='$hospital_name', 
                    aboutme='$aboutme', 
                    registration_number='$registration_number', 
                    time='$time'";

    if ($profilePicture) {
        $updateQuery .= ", picture='$profilePicture'";
    }

    $updateQuery .= " WHERE email='$email'";

    if ($conn->query($updateQuery) === TRUE) {
        echo "<script>alert('Profile updated successfully!'); window.location.href='doctorhomepage.php';</script>";
    } else {
        echo "Error updating profile: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile | Health Heaven</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f9ed;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            width: 500px;
            padding: 2rem;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 1.5rem;
        }

        .form-wrapper {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .input-group label {
            font-weight: 600;
            color: #2c3e50;
        }

        .input-group input,
        .input-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
        }

        .input-group textarea {
            resize: none;
            height: 100px;
        }

        .submit-btn {
            background-color: #48bb78;
            color: white;
            padding: 0.8rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
        }

        .submit-btn:hover {
            background-color: #38a169;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Update Profile</h2>
        <form method="post" action="updateprofile.php" enctype="multipart/form-data" class="form-wrapper">
            <div class="input-group">
                <label for="specialist">Specialist</label>
                <input type="text" id="specialist" name="specialist" placeholder="Enter your specialization" required>
            </div>
            <div class="input-group">
                <label for="experience">Experience (in years)</label>
                <input type="number" id="experience" name="experience" placeholder="Enter your experience" required>
            </div>
            <div class="input-group">
                <label for="hospital_name">Hospital Name</label>
                <input type="text" id="hospital_name" name="hospital_name" placeholder="Enter hospital name" required>
            </div>
            <div class="input-group">
                <label for="aboutme">About Me</label>
                <textarea id="aboutme" name="aboutme" placeholder="Write about yourself"></textarea>
            </div>
            <div class="input-group">
                <label for="registration_number">Registration Number</label>
                <input type="text" id="registration_number" name="registration_number" placeholder="Enter your registration number" required>
            </div>
            <div class="input-group">
                <label for="time">Available Time</label>
                <input type="text" id="time" name="time" placeholder="Enter your available time" required>
            </div>
            <div class="input-group">
                <label for="profilePicture">Profile Picture</label>
                <input type="file" id="profilePicture" name="profilePicture" accept="image/*">
            </div>
            <button type="submit" class="submit-btn">Update Profile</button>
        </form>
    </div>
</body>

</html>