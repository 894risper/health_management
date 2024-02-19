<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>
    <link rel="stylesheet" href="appointment.css">
    <!-- Include jQuery from CDN -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // Function to fetch and update appointment status
            function updateAppointmentStatus() {
                $.ajax({
                    url: 'view_appointment_status.php', // PHP script to fetch status from database
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        // Update appointment status in the HTML
                        if(response.success) {
                            $('#appointmentStatus').html(response.data);
                        } else {
                            $('#appointmentStatus').html('Error fetching appointment status.');
                        }
                    },
                    error: function() {
                        $('#appointmentStatus').html('Error fetching appointment status.');
                    }
                });
            }

            // Update appointment status every 5 seconds
            setInterval(updateAppointmentStatus, 5000);
        });
    </script>
</head>
<body>
    <div class="container">
        <h2>Book Appointment</h2>

        <form action="store_appointment.php" method="post">
            <?php
            // Check if user is logged in
            if(isset($_SESSION['user_email'])) {
                // Get user's name from registration table based on email
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

                $user_email = $_SESSION['user_email'];
                $sql = "SELECT name FROM registration WHERE email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $user_email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $user_name = $row['name'];
                    echo "<label for='patient'>Patient Name:</label>";
                    echo "<input type='text' id='patient' name='patient' value='$user_name' readonly><br><br>";
                }

                $stmt->close();
                $conn->close();
            }
            ?>

            <label for="doctor">Select Doctor:</label>
            <select id="doctor" name="doctor" required>
                <option value="">Select Doctor</option>
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

                // Fetch doctors' names, emails, and specialties from the doctors table
                $sql = "SELECT name, email, speciality FROM doctors";
                $result = $conn->query($sql);

                // Output data of each row
                while($row = $result->fetch_assoc()) {
                    // Combine name, email, and specialty for the option value
                    $option_value = $row["name"] . " - " . $row["email"] . " - " . $row["speciality"];
                    echo "<option value='" . $option_value . "'>" . $option_value . "</option>";
                }

                // Close connection
                $conn->close();
                ?>
            </select><br><br>

            <label for="date">Choose Date:</label>
            <input type="date" id="date" name="date" required><br><br>

            <label for="time">Choose Time:</label>
            <input type="time" id="time" name="time" required><br><br>

            <input type="submit" value="Book Appointment">
        </form>

        <?php
        // Check if appointment was successfully submitted
        if (isset($_SESSION['appointment_success']) && $_SESSION['appointment_success'] === true) {
            echo "<p>Appointment submitted successfully!</p>";
            
            // Reset the session variable
            unset($_SESSION['appointment_success']);
        }
        ?>
    
</body>
</html>
