<?php
include 'connect.php';
session_start();


if (!isset($_SESSION['doctor_email'])) {
    echo "<script>alert('Unauthorized access! Please log in.'); window.location.href='doctorlogin.html';</script>";
    exit();
}

$email = $_SESSION['doctor_email'];


$query = "SELECT * FROM doctor WHERE email='$email'";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $doctor = $result->fetch_assoc();
} else {
    echo "<script>alert('Doctor not found!'); window.location.href='doctorhomepage.php';</script>";
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $specialist = $conn->real_escape_string($_POST['specialist']);
    $experience = $conn->real_escape_string($_POST['experience']);
    $hospital_name = $conn->real_escape_string($_POST['hospital_name']);
    $aboutme = $conn->real_escape_string($_POST['aboutme']);
    $registration_number = $conn->real_escape_string($_POST['registration_number']);
    $time = $conn->real_escape_string($_POST['time']);


    $profilePicture = $doctor['picture'];
    if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $fileName = basename($_FILES['profilePicture']['name']);
        $profilePicture = $uploadDir . $fileName;

 
        if (move_uploaded_file($_FILES['profilePicture']['tmp_name'], $profilePicture)) {
           
        } else {
            echo "<script>alert('Failed to upload profile picture.');</script>";
        }
    }


    $updateQuery = "UPDATE doctor SET 
                    specialist='$specialist', 
                    experience='$experience', 
                    hospital_name='$hospital_name', 
                    aboutme='$aboutme', 
                    registration_number='$registration_number', 
                    time='$time', 
                    picture='$profilePicture'
                    WHERE email='$email'";

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 700px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .form-group label {
            font-weight: bold;
        }

        .form-group input,
        .form-group textarea {
            border-radius: 8px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            padding: 10px 20px;
            border-radius: 8px;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }

        .profile-picture-preview {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 10px;
        }

        .profile-picture-preview img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #007bff;
        }

        .form-title {
            text-align: center;
            margin-bottom: 20px;
            color: #007bff;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="form-title">Update Profile</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="specialist" class="form-label">Specialist</label>
                <input type="text" id="specialist" name="specialist" class="form-control" value="<?php echo htmlspecialchars($doctor['specialist'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="experience" class="form-label">Experience (in years)</label>
                <input type="number" id="experience" name="experience" class="form-control" value="<?php echo htmlspecialchars($doctor['experience'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="hospital_name" class="form-label">Hospital Name</label>
                <input type="text" id="hospital_name" name="hospital_name" class="form-control" value="<?php echo htmlspecialchars($doctor['hospital_name'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="aboutme" class="form-label">About Me</label>
                <textarea id="aboutme" name="aboutme" class="form-control" rows="4" required><?php echo htmlspecialchars($doctor['aboutme'] ?? ''); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="registration_number" class="form-label">Registration Number</label>
                <input type="text" id="registration_number" name="registration_number" class="form-control" value="<?php echo htmlspecialchars($doctor['registration_number'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="time" class="form-label">Available Time</label>
                <input type="text" id="time" name="time" class="form-control" value="<?php echo htmlspecialchars($doctor['time'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="profilePicture" class="form-label">Profile Picture</label>
                <input type="file" id="profilePicture" name="profilePicture" class="form-control">
                <?php if (!empty($doctor['picture'])): ?>
                    <div class="profile-picture-preview">
                        <p>Current Picture:</p>
                        <img src="<?php echo htmlspecialchars($doctor['picture']); ?>" alt="Profile Picture">
                    </div>
                <?php endif; ?>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </div>
        </form>
    </div>
</body>

</html>

