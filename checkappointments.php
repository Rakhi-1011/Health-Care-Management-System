<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['doctor_email'])) {
    echo "<script>alert('Unauthorized access! Please log in.'); window.location.href='doctorlogin.html';</script>";
    exit();
}

$doctor_email = $_SESSION['doctor_email'];
$doctor_table = preg_replace('/[^a-zA-Z0-9_]/', '_', strtolower($doctor_email)) . "_appointments";

$query = "SELECT * FROM `$doctor_table` ORDER BY appointment_date, appointment_time";
$result = $conn->query($query);

if (!$result) {
    die("Error fetching appointments: " . $conn->error);
}
function createZip($folderPath, $zipFilePath) {
    $zip = new ZipArchive();
    if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folderPath), RecursiveIteratorIterator::LEAVES_ONLY);
        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = "prescriptions/" . substr($filePath, strlen($folderPath) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
        $zip->close();
        return true;
    }
    return false;
}

if (isset($_GET['download_folder']) && isset($_GET['email'])) {
    $patient_email = $_GET['email'];
    $folderPath = "uploads/" . preg_replace('/[^a-zA-Z0-9_@.]/', '_', strtolower($patient_email)) . "/prescriptions";
    $zipFilePath = "prescription.zip"; // Rename the ZIP file

    if (is_dir($folderPath)) {
        if (createZip($folderPath, $zipFilePath)) {
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . basename($zipFilePath) . '"');
            header('Content-Length: ' . filesize($zipFilePath));
            readfile($zipFilePath);
            unlink($zipFilePath); // Delete the ZIP file after download
            exit();
        } else {
            echo "Failed to create ZIP file.";
        }
    } else {
        echo "Folder not found.";
    }
    exit();
}

// Handle AJAX request for diseases
if (isset($_GET['get_diseases']) && isset($_GET['email'])) {
    $patient_email = $_GET['email'];
    $diseases_table = preg_replace('/[^a-zA-Z0-9_]/', '_', strtolower($patient_email)) . "_diseases";

    $query = "SELECT diseases FROM `$diseases_table`";
    $res = $conn->query($query);

    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        echo htmlspecialchars($row['diseases']);
    } else {
        echo "No diseases found.";
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Appointments | Health Heaven</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #2ecc71;
            font-weight: bold;
        }

        .table th {
            background-color: #2ecc71;
            color: white;
        }

        .no-appointments {
            color: #e74c3c;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="text-center mb-4">Your Appointments</h1>

        <?php if ($result && $result->num_rows > 0): ?>
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Patient Name</th>
                        <th>Patient Email</th>
                        <th>Appointment Date</th>
                        <th>Appointment Time</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($appointment = $result->fetch_assoc()): ?>
                        <?php
                        $patient_email = $appointment['patient_email'];
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                            <td><?php echo htmlspecialchars($patient_email); ?></td>
                            <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['appointment_time']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['created_at']); ?></td>
                            <td>
                                <a href="?download_folder=1&email=<?php echo urlencode($patient_email); ?>" class="btn btn-primary btn-sm">Download Prescriptions</a>
                                <button class="btn btn-info btn-sm" onclick="viewDiseases('<?php echo $patient_email; ?>')">See Diseases</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center no-appointments">No appointments found.</p>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="doctorhomepage.php" class="btn btn-success">Back to Dashboard</a>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="diseasesModal" tabindex="-1" aria-labelledby="diseasesModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Patient Diseases</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="diseasesContent">Loading...</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewDiseases(patientEmail) {
            fetch(`<?php echo $_SERVER['PHP_SELF']; ?>?get_diseases=1&email=${encodeURIComponent(patientEmail)}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('diseasesContent').innerHTML = data;
                    const modal = new bootstrap.Modal(document.getElementById('diseasesModal'));
                    modal.show();
                })
                .catch(error => {
                    console.error("Error fetching diseases:", error);
                    document.getElementById('diseasesContent').innerText = "Error loading diseases.";
                });
        }
    </script>
</body>

</html>
