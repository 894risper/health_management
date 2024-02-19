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

    if(isset($_POST["email"]) && isset($_POST["password"])) {
        $email = test_input($_POST["email"]);
        $password = test_input($_POST["password"]);

        // Prepare SQL statement to prevent SQL injection
        $sql_patient = "SELECT * FROM registration WHERE email = ? AND password = ?";
        $stmt_patient = $conn->prepare($sql_patient);
        $stmt_patient->bind_param("ss", $email, $password);
        $stmt_patient->execute();
        $result_patient = $stmt_patient->get_result();

        if ($result_patient && $result_patient->num_rows > 0) {
            // Fetch user data from the registration table
            $user_data = $result_patient->fetch_assoc();
            $_SESSION['user_name'] = $user_data['name'];
            $_SESSION['user_email'] = $email; // Store user's email in session

            // Additional fields can be stored in session if needed
            $_SESSION['user_phone'] = $user_data['phoneNo'];

            // Redirect user to dashboard or homepage
            header("Location: project.html");
            exit();
        } 

        // Check if the user is a doctor
        $sql_doctor = "SELECT * FROM doctors WHERE email = ? AND password = ?";
        $stmt_doctor = $conn->prepare($sql_doctor);
        $stmt_doctor->bind_param("ss", $email, $password);
        $stmt_doctor->execute();
        $result_doctor = $stmt_doctor->get_result();

        if ($result_doctor && $result_doctor->num_rows > 0) {
            // Fetch doctor data from the doctors table
            $doctor_data = $result_doctor->fetch_assoc();
            $_SESSION['doctor_name'] = $doctor_data['name'];
            $_SESSION['doctor_email'] = $email; // Store doctor's email in session
            $_SESSION['doctor_speciality'] = $doctor_data['speciality']; // Store doctor's speciality
            $_SESSION['doctor_phone'] = $doctor_data['phoneNo']; // Store doctor's phone number

            // Redirect to doctor dashboard
            header("Location:appointment.html");
            exit();
        } else {
            echo "Invalid email or password";
        }
    } else {
        echo "Email and password are required";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="project.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $email; ?>">
            <span class="error"><?php echo $emailErr; ?></span><br><br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password">
            <span class="error"><?php echo $passwordErr; ?></span><br><br>
            <input type="submit" value="Login">
        </form>
        <br>
        <a href="forgot_password.php">Forgot password?</a>
    </div>
</body>
</html>
