<?php
session_start();

if (!isset($_SESSION['dashname'])) {
    header('Location: newloginpage.html'); 
    exit();
}


$user_name = $_SESSION['dashname'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard | Health Heaven</title>
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

        .custom-green {
            background-color: rgb(41, 159, 68); /* Green color */
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
                    <li class="nav-item"><a class="nav-link" href="firstpage.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="contractuspage1.html">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>


    <section id="dashboard" class="text-center custom-green py-5">
        <div class="container">
            <h1>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h1>
            <p class="lead">Manage your health and appointments with ease.</p>
        </div>
    </section>


    <section id="options" class="py-5">
        <div class="container text-center">
            <h2>What would you like to do?</h2>
            <div class="row justify-content-center mt-4">
                <!-- Appoint Doctor -->
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card p-3">
                        <a class="nav-link" href="appointdoctor.php">
                            <h4>Appoint Doctor</h4>
                        </a>
                        <p>Book an appointment with our expert doctors.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card p-3">
                        <a class="nav-link" href="patient_update_profile.php">
                            <h4>Update Profile</h4>
                        </a>
                        <p>Update Your Profile.</p>
                    </div>
                </div>
           
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card p-3">
                        <a class="nav-link" href="healthtips.html">
                            <h4>Nutrition Tips</h4>
                        </a>
                        <p>Get nutrition tips for a healthier life.</p>
                    </div>
                </div>
           
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card p-3">
                        <a class="nav-link" href="healthtips.html">
                            <h4>Fitness and Yoga Tips</h4>
                        </a>
                        <p>Get fitness and Yoga tips to get fit.</p>
                    </div>
                </div>
       
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card p-3">
                        <a class="nav-link" href="healthtips.html">
                            <h4>Mental Wellness</h4>
                        </a>
                        <p>Guided meditation and stress management techniques.</p>
                    </div>
                </div>
           
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card p-3">
                        <a class="nav-link" href="previousappointments.html">
                            <h4>Previous Appointments</h4>
                        </a>
                        <p>View your appointment history and details.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; 2025 Health Heaven. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>