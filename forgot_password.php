<!DOCTYPE HTML>  
<html>
<head>
    <link rel="stylesheet" href="project.css">
</head>
<body>  

<?php
// Define variables and set to empty values
$emailErr = "";
$email = "";

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

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate Email
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = test_input($_POST["email"]);
    }

    // If email is provided, generate a reset token and send reset link to the user's email
    if (empty($emailErr)) {
        // Generate a random reset token
        $resetToken = bin2hex(random_bytes(20));

        // Prepare and bind the update statement to save the reset token in the database
        $sql = "UPDATE registration SET reset_token = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("ss", $resetToken, $email);

        // Execute the update statement
        if ($stmt->execute()) {
            // Send reset link to the user's email (you can implement this part)
            echo "Reset link sent to your email.";
        } else {
            echo "Error updating reset token: " . $stmt->error;
        }

        // Close statement
        $stmt->close();
    }
}

// Close connection
$conn->close();
?>
<div class="container">
<h2>Forgot Password</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
    E-mail: <input type="text" name="email" value="<?php echo $email;?>">
    <span class="error">* <?php echo $emailErr;?></span>
    <br><br>
    <input type="submit" name="submit" value="Send Reset Link">  
</form>
</div>
</body>
</html>
