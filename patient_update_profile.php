<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['patient_email'])) {
    echo "<script>alert('Unauthorized access! Please log in.'); window.location.href='newloginpage.html';</script>";
    exit();
}

$email = $_SESSION['patient_email'];

$query = "SELECT * FROM patient WHERE email='$email'";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $patient = $result->fetch_assoc();
} else {
    echo "<script>alert('Patient not found!'); window.location.href='firstpage.html';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $mydiseases = $conn->real_escape_string($_POST['mydiseases']);

    $prescriptionImage = '';
    if (isset($_FILES['prescription']) && $_FILES['prescription']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/' . $email . '/prescriptions/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $fileName = basename($_FILES['prescription']['name']);
        $prescriptionImage = $uploadDir . $fileName;
        move_uploaded_file($_FILES['prescription']['tmp_name'], $prescriptionImage);
    }

    $updatePatientQuery = "UPDATE patient SET firstName='$name', email='$email' WHERE email='$email'";
    if ($conn->query($updatePatientQuery) === TRUE) {
        $tableNamePrefix = str_replace(['.','@'], '_', $email);
        $diseasesTable = $tableNamePrefix . "_diseases";
        $diseasesQuery = "SELECT diseases FROM `$diseasesTable` WHERE email='$email'";
        $diseasesResult = $conn->query($diseasesQuery);
        if ($diseasesResult && $diseasesResult->num_rows > 0) {
            $updateDiseasesQuery = "UPDATE `$diseasesTable` SET diseases='$mydiseases' WHERE email='$email'";
            $conn->query($updateDiseasesQuery);
        } else {
            $insertDiseasesQuery = "INSERT INTO `$diseasesTable` (email, diseases) VALUES ('$email', '$mydiseases')";
            $conn->query($insertDiseasesQuery);
        }

        if (!empty($prescriptionImage)) {
            $tableNamePrefix = str_replace(['.','@'], '_', $email);
            $prescriptionTable = $tableNamePrefix . "_prescriptions";
            $checkPrescriptionQuery = "SELECT * FROM `$prescriptionTable` WHERE email='$email'";
            $prescriptionResult = $conn->query($checkPrescriptionQuery);
            if ($prescriptionResult && $prescriptionResult->num_rows > 0) {
                $updatePrescriptionQuery = "UPDATE `$prescriptionTable` SET prescription_image='$prescriptionImage' WHERE email='$email'";
                $conn->query($updatePrescriptionQuery);
            } else {
                $insertPrescriptionQuery = "INSERT INTO `$prescriptionTable` (email, prescription_image) VALUES ('$email', '$prescriptionImage')";
                $conn->query($insertPrescriptionQuery);
            }
        }
    } else {
        echo "Error updating profile: " . $conn->error;
    }
    header("Location: firstpage.php");
    exit();
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
        .form-group textarea,
        .form-group select {
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
                <label for="name" class="form-label">Name</label>
                <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($patient['firstName'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($patient['email'] ?? ''); ?>" readonly>
                <small class="form-text text-muted">Email cannot be changed.</small>
            </div>
            <div class="mb-3">
                <label for="mydiseases" class="form-label">My Diseases</label>
                <textarea id="mydiseases" name="mydiseases" class="form-control" rows="4" required><?php
                    $tableNamePrefix = str_replace(['.','@'], '_', $email);
                    $diseasesTable = $tableNamePrefix . "_diseases";
                    $diseasesQuery = "SELECT diseases FROM `$diseasesTable` WHERE email='$email'";
                    $diseasesResult = $conn->query($diseasesQuery);
                    if ($diseasesResult && $diseasesResult->num_rows > 0) {
                        $diseasesData = $diseasesResult->fetch_assoc();
                        echo htmlspecialchars($diseasesData['diseases'] ?? '');
                    }
                ?></textarea>
            </div>
            <div class="mb-3">
                <label for="prescription" class="form-label">Prescription Picture</label>
                <input type="file" id="prescription" name="prescription" class="form-control">
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </div>
        </form>
    </div>
</body>
</html>
