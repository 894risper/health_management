<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    // Redirect user to login page or handle the case where user is not logged in
    header("Location: login.php");
    exit();
}

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Appointment Status</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>View Appointment Status</h2>
        <table>
            <thead>
                <tr>
                    <th>Appointment ID</th>
                    <th>Patient Name</th>
                    <th>Doctor Name</th>
                    <th>Appointment Date</th>
                    <th>Appointment Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch appointment status for the logged-in user based on their name
                $patient_name = $_SESSION['user_name'];
                $sql = "SELECT appointments.id, appointments.patient, appointments.appointment_date, appointments.appointment_time, appointments.status, doctors.name AS doctor_name 
                        FROM appointments 
                        INNER JOIN doctors ON appointments.doc_email = doctors.email 
                        WHERE appointments.patient = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $patient_name);
                $stmt->execute();
                $result = $stmt->get_result();

                // Check if appointments are found
                if ($result->num_rows > 0) {
                    // Output appointment status data
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["patient"] . "</td>";
                        echo "<td>" . $row["doctor_name"] . "</td>";
                        echo "<td>" . $row["appointment_date"] . "</td>";
                        echo "<td>" . $row["appointment_time"] . "</td>";
                        echo "<td>" . $row["status"] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    // No appointments found
                    echo "<tr><td colspan='6'>No appointments found</td></tr>";
                }

                // Close prepared statement and database connection
                $stmt->close();
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
