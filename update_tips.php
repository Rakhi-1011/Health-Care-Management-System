<?php
session_start();
include 'connect.php';

// Check if the doctor is logged in
if (!isset($_SESSION['doctor_email'])) {
    echo "<script>alert('Unauthorized access! Please log in.'); window.location.href='doctorlogin.html';</script>";
    exit();
}

// Fetch the logged-in doctor's details
$email = $_SESSION['doctor_email'];
$query = "SELECT * FROM doctor WHERE email='$email'";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $doctor = $result->fetch_assoc();
    $doctor_name = $doctor['name'];
} else {
    echo "<script>alert('Doctor not found!'); window.location.href='doctorlogin.html';</script>";
    exit();
}

// Handle form submission
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $conn->real_escape_string($_POST['category']);
    $age_range = $conn->real_escape_string($_POST['age_range']);
    $tip = $conn->real_escape_string($_POST['tip']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $pregnancy = isset($_POST['pregnancy']) ? $conn->real_escape_string($_POST['pregnancy']) : null;

    // Table name based on category
    $table = preg_replace('/[^a-zA-Z0-9_]/', '_', strtolower($category)) . "_tips";

    // Create table if not exists
    $createTable = "CREATE TABLE IF NOT EXISTS `$table` (
        id INT AUTO_INCREMENT PRIMARY KEY,
        doctor_name VARCHAR(255) NOT NULL,
        age_range VARCHAR(50) NOT NULL,
        gender VARCHAR(10) NOT NULL,
        pregnancy_status VARCHAR(20),
        tip TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->query($createTable);

    // Insert tip
    $insert = "INSERT INTO `$table` (doctor_name, age_range, gender, pregnancy_status, tip) VALUES ('$doctor_name', '$age_range', '$gender', " . ($pregnancy ? "'$pregnancy'" : "NULL") . ", '$tip')";
    if ($conn->query($insert) === TRUE) {
        header("Location: doctorhomepage.php");
        exit();
    } else {
        $message = "<div class='alert alert-danger mt-3'>Failed to upload tip: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Health Tips | Health Heaven</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f4f4;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            margin-top: 40px;
            padding: 32px 24px;
            max-width: 600px;
        }
        .custom-green {
            background-color: rgb(41, 159, 68);
            color: white;
        }
        .form-label {
            font-weight: 500;
        }
        .btn-custom {
            background: rgb(41, 159, 68);
            color: #fff;
            font-weight: 600;
            border: none;
        }
        .btn-custom:hover {
            background: #228c3c;
            color: #fff;
        }
        footer {
            margin-top: auto;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Health Heaven</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="doctorhomepage.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="contractuspage1.html">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="newloginpage.html">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="custom-green py-4 text-center">
        <h1 class="mb-0">Upload Health Tips</h1>
        <p class="lead mb-0">Share your expertise with patients by uploading helpful tips.</p>
    </section>

    <div class="container mt-4">
        <form method="POST" autocomplete="off">
            <div class="mb-3">
                <label for="category" class="form-label">Select Category</label>
                <select class="form-select" id="category" name="category" required>
                    <option value="">Choose...</option>
                    <option value="Mental Wellness">Mental Wellness</option>
                    <option value="Fitness and Yoga Tips">Fitness and Yoga Tips</option>
                    <option value="Nutrition Tips">Nutrition Tips</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="age_range" class="form-label">Enter Age Range</label>
                <input type="text" class="form-control" id="age_range" name="age_range" placeholder="e.g. 18-25, 30-40" required>
            </div>
            <div class="mb-3">
                <label for="gender" class="form-label">Gender</label>
                <select class="form-select" id="gender" name="gender" required onchange="togglePregnancy(this.value)">
                    <option value="">Select...</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div class="mb-3" id="pregnancyDiv" style="display:none;">
                <label for="pregnancy" class="form-label">Pregnancy Status</label>
                <select class="form-select" id="pregnancy" name="pregnancy">
                    <option value="">Select...</option>
                    <option value="Pregnant">Pregnant</option>
                    <option value="Not Pregnant">Not Pregnant</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="tip" class="form-label">Enter Your Health Tip</label>
                <textarea class="form-control" id="tip" name="tip" rows="5" placeholder="Write your tip here..." required></textarea>
            </div>
            <button type="submit" class="btn btn-custom w-100">Upload Tip</button>
            <?php echo $message; ?>
        </form>
    </div>

    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; 2025 Health Heaven. All rights reserved.</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePregnancy(gender) {
            document.getElementById('pregnancyDiv').style.display = (gender === 'Female') ? 'block' : 'none';
        }
        // On page load, if form was submitted and Female was selected, show pregnancyDiv
        window.onload = function() {
            var gender = document.getElementById('gender').value;
            togglePregnancy(gender);
        }
    </script>
</body>
</html>