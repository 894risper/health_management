<?php
session_start();

$emailErr = $passwordErr = "";
$email = "";

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root";
    $password = "risper/atieno200314246";
    $dbname = "project";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if(isset($_POST["appointment_id"]) && isset($_POST["status"])) {
        $appointment_id = test_input($_POST["appointment_id"]);
        $status = test_input($_POST["status"]);

        // Update appointment status in the database
        $sql_update = "UPDATE appointments SET status = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $status, $appointment_id);
        $stmt_update->execute();

        // Check if appointment status was successfully updated
        if ($stmt_update->affected_rows > 0) {
            // Remove the updated appointment from the dropdown list
            echo "<script>document.getElementById('appointment_id').remove(document.getElementById('appointment_id').selectedIndex);</script>";
            echo "<script>alert('Appointment status updated successfully.');</script>";
        } else {
            echo "<script>alert('Failed to update appointment status.');</script>";
        }

        // Close statement
        $stmt_update->close();
    }
    
    // Close database connection
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Appointment Status</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Update Appointment Status</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="appointment_id">Select Appointment:</label>
            <select id="appointment_id" name="appointment_id" required>
                <?php
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

                // Fetch appointments assigned to the logged-in doctor
                $doctor_email = $_SESSION['doctor_email'];
                $sql = "SELECT id, patient, appointment_date, appointment_time FROM appointments WHERE doc_email = ? AND status = 'pending'";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $doctor_email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row["id"] . "'>Patient: " . $row["patient"] . ", Date: " . $row["appointment_date"] . ", Time: " . $row["appointment_time"] . "</option>";
                    }
                } else {
                    echo "<option value='' disabled>No pending appointments found</option>";
                }

                // Close statement and database connection
                $stmt->close();
                $conn->close();
                ?>
            </select><br><br>
            
            <label for="status">Select Status:</label>
            <select id="status" name="status" required>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="cancelled">Cancelled</option>
            </select><br><br>
            
            <input type="submit" value="Update Status" name="submit">
        </form>

        <br><br>

        <h2>Confirmed Appointments (for personal use)</h2>
        <form action="#" method="post">
            <table>
                <tr>
                    <th>Patient</th>
                    <th>Appointment Date</th>
                    <th>Appointment Time</th>
                    <th>Mark as Done</th>
                </tr>
                <?php
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

                // Fetch confirmed appointments for the logged-in doctor
                $doctor_email = $_SESSION['doctor_email'];
                $sql_confirmed = "SELECT id, patient, appointment_date, appointment_time FROM appointments WHERE doc_email = ? AND status = 'confirmed' ORDER BY appointment_date DESC";
                $stmt_confirmed = $conn->prepare($sql_confirmed);
                $stmt_confirmed->bind_param("s", $doctor_email);
                $stmt_confirmed->execute();
                $result_confirmed = $stmt_confirmed->get_result();

                if ($result_confirmed->num_rows > 0) {
                    while($row_confirmed = $result_confirmed->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row_confirmed["patient"] . "</td>";
                        echo "<td>" . $row_confirmed["appointment_date"] . "</td>";
                        echo "<td>" . $row_confirmed["appointment_time"] . "</td>";
                        echo "<td><input type='checkbox' name='done_appointments[]' value='" . $row_confirmed["id"] . "'></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No confirmed appointments found</td></tr>";
                }

                // Close statement and database connection
                $stmt_confirmed->close();
                $conn->close();
                ?>
            </table>
            <br>
            
        </form>
    </div>
</body>
</html>
