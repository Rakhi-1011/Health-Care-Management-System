<?php
include 'connect.php';
session_start();


if (!isset($_SESSION['patient_email'])) {
    echo "<script>alert('Unauthorized access! Please log in.'); window.location.href='newloginpage.html';</script>";
    exit();
}


if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}


$patient_email = $_SESSION['patient_email'];
$patient_query = "SELECT * FROM patient WHERE email='$patient_email'";
$patient_result = $conn->query($patient_query);

if (!$patient_result) {
    die("Error fetching patient details: " . $conn->error);
}

if ($patient_result->num_rows > 0) {
    $patient = $patient_result->fetch_assoc();
} else {
    die("Patient not found.");
}


$doctor_query = "SELECT * FROM doctor";
$doctor_result = $conn->query($doctor_query);

if (!$doctor_result) {
    die("Error fetching doctors: " . $conn->error);
}


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $patient_name = $conn->real_escape_string($_POST['patient_name']);
    $patient_email = $conn->real_escape_string($_POST['patient_email']);
    $doctor_name = $conn->real_escape_string($_POST['doctor_id']);
    $appointment_date = $conn->real_escape_string($_POST['appointment_date']);
    $appointment_time = $conn->real_escape_string($_POST['appointment_time']);

    
    $doctor_email_query = "SELECT email FROM doctor WHERE name='$doctor_name'";
    $doctor_email_result = $conn->query($doctor_email_query);

    if ($doctor_email_result && $doctor_email_result->num_rows > 0) {
        $doctor_email_row = $doctor_email_result->fetch_assoc();
        $doctor_email = $doctor_email_row['email'];

      
        $insertQuery = "INSERT INTO appointments (patient_name, patient_email, doctor_name, appointment_date, appointment_time) 
                        VALUES ('$patient_name', '$patient_email', '$doctor_name', '$appointment_date', '$appointment_time')";

        if ($conn->query($insertQuery) === TRUE) {
    
            $doctor_table = preg_replace('/[^a-zA-Z0-9_]/', '_', strtolower($doctor_email)) . "_appointments";
            $createTableQuery = "CREATE TABLE IF NOT EXISTS `$doctor_table` (
                id INT AUTO_INCREMENT PRIMARY KEY,
                patient_name VARCHAR(255) NOT NULL,
                patient_email VARCHAR(255) NOT NULL,
                appointment_date DATE NOT NULL,
                appointment_time TIME NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";

            if ($conn->query($createTableQuery) === TRUE) {
              
                $insertDoctorTableQuery = "INSERT INTO `$doctor_table` (patient_name, patient_email, appointment_date, appointment_time) 
                                           VALUES ('$patient_name', '$patient_email', '$appointment_date', '$appointment_time')";
                $conn->query($insertDoctorTableQuery);
            }

    
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
                $mail->addAddress($patient_email, $patient_name);
                $mail->isHTML(true);

                $mail->Subject = 'Appointment Confirmation';
                $mail->Body = "Hi <b>$patient_name</b>,<br><br>Your appointment has been successfully booked.<br><br>
                               <b>Doctor:</b> $doctor_name <br>
                               <b>Date:</b> $appointment_date <br>
                               <b>Time:</b> $appointment_time <br><br>
                               Thank you for choosing Health Heaven.";

                $mail->send();
                echo "<script>alert('Appointment booked successfully! A confirmation email has been sent.'); window.location.href='firstpage.php';</script>";
            } catch (Exception $e) {
                echo "Mailer Error: " . $mail->ErrorInfo;
            }
        } else {
            echo "Database Error: " . $conn->error;
        }
    } else {
        echo "<script>alert('Doctor not found!'); window.location.href='appointdoctor.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Appointments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4;
        }

        .container {
            margin-top: 50px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .card-img-top {
    height: 400px;
    object-fit: cover; 
    width: 100%; 
}

        .doctor-card {
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="text-center text-success">Book a Doctor Appointment</h2>

        <div class="row text-center my-4">
         
            <?php while ($doctor = $doctor_result->fetch_assoc()) : ?>
                <div class="col-md-4">
                    <div class="card doctor-card" data-bs-toggle="modal" data-bs-target="#doctorModal<?php echo htmlspecialchars($doctor['registration_number']); ?>">
                        <img src="<?php echo !empty($doctor['picture']) ? htmlspecialchars($doctor['picture']) : 'default-doctor.png'; ?>" 
                             class="card-img-top" 
                             alt="Doctor Image">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($doctor['name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($doctor['specialist']); ?> - <?php echo htmlspecialchars($doctor['experience']); ?> Years Experience</p>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="doctorModal<?php echo htmlspecialchars($doctor['registration_number']); ?>" tabindex="-1" aria-labelledby="doctorModalLabel<?php echo htmlspecialchars($doctor['registration_number']); ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="doctorModalLabel<?php echo htmlspecialchars($doctor['registration_number']); ?>"><?php echo htmlspecialchars($doctor['name']); ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Specialist:</strong> <?php echo htmlspecialchars($doctor['specialist']); ?></p>
                                <p><strong>Experience:</strong> <?php echo htmlspecialchars($doctor['experience']); ?> Years</p>
                                <p><strong>Hospital:</strong> <?php echo htmlspecialchars($doctor['hospital_name']); ?></p>
                                <p><strong>About Me:</strong> <?php echo htmlspecialchars($doctor['aboutme']); ?></p>
                                <p><strong>Registration Number:</strong> <?php echo htmlspecialchars($doctor['registration_number']); ?></p>
                                <p><strong>Available Time:</strong> <?php echo htmlspecialchars($doctor['time']); ?></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="container mt-5">
           
            <form id="appointment-form" method="POST" action="appointdoctor.php">
                <h3 class="text-center">Book an Appointment</h3>

                
                <div class="mb-3">
                    <label for="patient_name" class="form-label">Your Name</label>
                    <input type="text" class="form-control" id="patient_name" name="patient_name" value="<?php echo htmlspecialchars($patient['firstName']); ?>" readonly>
                </div>

              
                <div class="mb-3">
                    <label for="patient_email" class="form-label">Your Email</label>
                    <input type="email" class="form-control" id="patient_email" name="patient_email" value="<?php echo htmlspecialchars($patient['email']); ?>" readonly>
                </div>

               
                <div class="mb-3">
                    <label for="doctor_id" class="form-label">Select a Doctor</label>
                    <select class="form-select" id="doctor_id" name="doctor_id" required>
                        <option value="">Select a Doctor</option>
                        <?php
                        $doctor_result->data_seek(0); // Reset the result pointer
                        while ($doctor = $doctor_result->fetch_assoc()) :
                        ?>
                            <option value="<?php echo htmlspecialchars($doctor['name']); ?>">
                                <?php echo htmlspecialchars($doctor['name']); ?> - <?php echo htmlspecialchars($doctor['specialist']); ?> (<?php echo htmlspecialchars($doctor['experience']); ?> Years Experience)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

              
                <div class="mb-3">
                    <label for="appointment_date" class="form-label">Select Date</label>
                    <input type="date" class="form-control" id="appointment_date" name="appointment_date" required>
                </div>

             
                <div class="mb-3">
                    <label for="appointment_time" class="form-label">Select Time</label>
                    <input type="time" class="form-control" id="appointment_time" name="appointment_time" required>
                </div>

                <button type="submit" class="btn btn-success w-100">Book Appointment</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>