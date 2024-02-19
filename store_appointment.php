<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if user is logged in
    if(isset($_SESSION['user_email'])) {
        // Get form data
        $patient = $_POST['patient'];
        $doctor_info = explode(" - ", $_POST['doctor']);
        $doctor_name = $doctor_info[0];
        $doctor_email = $doctor_info[1]; 
        $date = $_POST['date'];
        $time = $_POST['time'];

        // Database connection parameters
        $servername = "localhost";
        $username = "root";
        $password = "risper/atieno200314246";
        $dbname = "project";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Insert appointment into appointments table
        $sql = "INSERT INTO appointments (patient, doc_name, doc_email, appointment_date, appointment_time, status) VALUES (?, ?, ?, ?, ?, 'pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $patient, $doctor_name, $doctor_email, $date, $time);

        if ($stmt->execute()) {
            // Appointment stored successfully
            $_SESSION['appointment_success'] = true;
            header("Location: appointment.php");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        // User is not logged in
        echo "You must be logged in to book an appointment.";
    }
} else {
    // Invalid request method
    echo "Invalid request method.";
}
?>
