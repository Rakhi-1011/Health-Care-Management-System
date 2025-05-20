<?php
include 'connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_name = $conn->real_escape_string($_POST['patient_name']);
    $patient_email = $conn->real_escape_string($_POST['patient_email']);
    $doctor_id = $conn->real_escape_string($_POST['doctor_id']);
    $appointment_date = $conn->real_escape_string($_POST['appointment_date']);
    $appointment_time = $conn->real_escape_string($_POST['appointment_time']);

    $query = "INSERT INTO appointments (patient_name, patient_email, doctor_id, appointment_date, appointment_time) 
              VALUES ('$patient_name', '$patient_email', '$doctor_id', '$appointment_date', '$appointment_time')";

    if ($conn->query($query) === TRUE) {
        echo "<script>alert('Appointment booked successfully!'); window.location.href='appointdoctor.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>