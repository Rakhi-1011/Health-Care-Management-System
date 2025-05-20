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
} else {
    echo "<script>alert('Doctor not found!'); window.location.href='doctorlogin.html';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Homepage | Health Heaven</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        h1,
        h2,
        h4 {
            font-weight: bold;
        }

        h1 {
            color: #f8f8f9;
        }

        h2 {
            color: #3498db;
        }

        h4 {
            color: #2ecc71;
        }

        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }

        footer {
            margin-top: auto;
        }

        .profile-section {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #3498db;
        }

        .profile-details {
            font-size: 1.2rem;
        }

        .custom-green {
            background-color:rgb(41, 159, 68); 
            color: white;
        }
    </style>
</head>

<body>
  
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Health Heaven</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="firstpage.html">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="contractuspage1.html">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="newloginpage.html">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    
    <section id="dashboard" class="text-center text-white custom-green py-5">
        <div class="container">
            <h1>Welcome Doctor</h1>
            <p class="lead">Manage your profile and appointments with ease.</p>
        </div>
    </section>

  
    <section class="profile-section container mt-4">
        <img src="<?php echo $doctor['picture'] ? $doctor['picture'] : 'default-profile.png'; ?>" alt="Profile Picture" class="profile-picture">
        <div class="profile-details">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($doctor['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($doctor['email']); ?></p>
            <p><strong>Specialist:</strong> <?php echo htmlspecialchars($doctor['specialist'] ?: 'Not updated'); ?></p>
            <p><strong>Experience:</strong> <?php echo htmlspecialchars($doctor['experience'] ?: 'Not updated'); ?></p>
        </div>
    </section>

   
    <section id="options" class="py-5">
        <div class="container text-center">
            <h2>What would you like to do?</h2>
            <div class="row mt-4">
                <!-- Update Profile -->
                <div class="col-md-6 col-lg-4">
                    <div class="card p-3">
                        <a class="nav-link" href="updateprofile.php">
                            <h4>Update Profile</h4>
                        </a>
                        <p>Keep your profile up to date with the latest information.</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="card p-3">
                        <a class="nav-link" href="checkappointments.php">
                            <h4>Check Appointments</h4>
                        </a>
                        <p>View and manage your upcoming appointments.</p>
                    </div>
                </div>
            
                <div class="col-md-6 col-lg-4">
                    <div class="card p-3">
                        <a class="nav-link" href="uploadhealthtips.html">
                            <h4>Upload Health Tips</h4>
                        </a>
                        <p>Share your knowledge by uploading health tips.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; 2025 Health Heaven. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>