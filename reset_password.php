<!DOCTYPE HTML>  
<html>
<head>
    <style>
        .error {color: #FF0000;}
    </style>
</head>
<body>  

<?php
// Define variables and set to empty values
$passwordErr = $confirmPasswordErr = "";
$password = $confirmPassword = "";

// Function to sanitize user input
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
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

// Check if token is provided in the URL
if (isset($_GET["token"])) {
    $token = $_GET["token"];

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validate Password
        if (empty($_POST["password"])) {
            $passwordErr = "Password is required";
        } else {
            $password = test_input($_POST["password"]);
            // Additional password validation logic
            // Example: Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, and one digit.
            if (strlen($password) < 8 || !preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/", $password)) {
                $passwordErr = "Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one digit.";
            }
        }

        // Validate Confirm Password
        if (empty($_POST["confirmPassword"])) {
            $confirmPasswordErr = "Please confirm password";
        } else {
            $confirmPassword = test_input($_POST["confirmPassword"]);
            if ($confirmPassword !== $password) {
                $confirmPasswordErr = "Passwords do not match";
            }
        }

        // If all fields are valid, update password in database
        if (empty($passwordErr) && empty($confirmPasswordErr)) {
            // Hash the password before storing it in the database
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Prepare and bind the update statement
            $sql = "UPDATE registration SET password = ?, reset_token = NULL WHERE reset_token = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $hashedPassword, $token);

            // Execute the update statement
            if ($stmt->execute()) {
                echo "Password reset successfully!";
                // You can redirect the user to the login page or any other page after successful password reset
            } else {
                echo "Error updating password: " . $conn->error;
            }

            // Close statement
            $stmt->close();
        }
    }
} else {
    echo "Invalid token.";
}

// Close connection
$conn->close();
?>
<div class="container">

<h2>Reset Password</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?token=" . $token); ?>">  
    New Password: <input type="password" name="password">
    <span class="error">* <?php echo $passwordErr;?></span>
    <br><br>
    Confirm Password: <input type="password" name="confirmPassword">
    <span class="error">* <?php echo $confirmPasswordErr;?></span>
    <br><br>
    <input type="submit" name="submit" value="Reset Password">  
</form>
</div>
</body>
</html>
