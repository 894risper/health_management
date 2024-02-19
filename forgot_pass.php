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

    // If email is provided, generate and send password reset token
    if (empty($emailErr)) {
        // Check if the email exists in the database
        $checkEmailQuery = "SELECT * FROM registration WHERE email='$email'";
        $result = $conn->query($checkEmailQuery);
        
        if ($result->num_rows > 0) {
            // Generate a unique token for password reset
            $token = bin2hex(random_bytes(32));

            // Store the token in the database for the user
            $updateTokenQuery = "UPDATE registration SET reset_token='$token' WHERE email='$email'";
            if ($conn->query($updateTokenQuery) === TRUE) {
                // Send the token to the user's email address (Implement your email sending logic here)
                echo "A password reset link has been sent to your email.";
            } else {
                echo "Error updating record: " . $conn->error;
            }
        } else {
            echo "Email not found.";
        }
    }
}

// Close connection
$conn->close();
?>

<h2>Forgot Password</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
    E-mail: <input type="text" name="email" value="<?php echo $email;?>">
    <span class="error">* <?php echo $emailErr;?></span>
    <br><br>
    <input type="submit" name="submit" value="Reset Password">  
</form>

</body>
</html>
