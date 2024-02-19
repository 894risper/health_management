<!DOCTYPE HTML>  
<html>
<head>
<link rel="stylesheet" href="project.css">
</head>
<body>  

<?php
// Define variables and set to empty values
$nameErr = $phoneErr = $emailErr = $passwordErr = $confirmPasswordErr = "";
$name = $phone = $email = $password = $confirmPassword = "";

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
    // Validate Name
    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
    } else {
        $name = test_input($_POST["name"]);
        if (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
            $nameErr = "Only letters and white space allowed";
        }
    }

    // Validate Phone
    if (empty($_POST["phone"])) {
        $phoneErr = "Phone number is required";
    } else {
        $phone = test_input($_POST["phone"]);
        if (!preg_match("/^[0-9]+$/", $phone)) {
            $phoneErr = "Only digits are allowed for the phone number";
        }
    }

    // Validate Email
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = test_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }

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

    // Confirm Password
    if (empty($_POST["confirmPassword"])) {
        $confirmPasswordErr = "Please confirm password";
    } else {
        $confirmPassword = test_input($_POST["confirmPassword"]);
        if ($confirmPassword !== $password) {
            $confirmPasswordErr = "Passwords do not match";
        }
    }

    // If all fields are valid, check if user is already registered
    if (empty($nameErr) && empty($phoneErr) && empty($emailErr) && empty($passwordErr) && empty($confirmPasswordErr)) {
        // Check if user already exists
        $checkUserQuery = "SELECT * FROM registration WHERE email='$email'";
        $result = $conn->query($checkUserQuery);
        if ($result->num_rows > 0) {
            // User is already registered, provide a message and link to login page
            echo "<p>You are already registered. ";
            echo "You can <a href='login.php'>login here</a>.</p>";
        } else {
            // User is not registered, proceed with registration
            // Prepare and bind the insert statement
            $insertQuery = "INSERT INTO registration (name, email, password, phoneNo) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("ssss", $name, $email, $password, $phone);

            // Execute the insert statement
            if ($stmt->execute()) {
                echo "<p>Registration successful! <br>";
                echo "You can now <a href='login.php'>login</a>.</p>";
            } else {
                echo "<p>Error: " . $insertQuery . "<br>" . $conn->error . "</p>";
            }

            // Close statement
            $stmt->close();
        }
    }
}

// Close connection
$conn->close();
?>

<div class="container">
<h2>REGISTRATION</h2>
<p><span class="error">* required field</span></p>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
  Name: <input type="text" name="name" value="<?php echo $name;?>">
  <span class="error">* <?php echo $nameErr;?></span>
  <br><br>
  Phone No: <input type="text" name="phone" value="<?php echo $phone;?>">
  <span class="error">* <?php echo $phoneErr;?></span>
  <br><br>
  E-mail: <input type="text" name="email" value="<?php echo $email;?>">
  <span class="error">* <?php echo $emailErr;?></span>
  <br><br>
  Password: <input type="password" name="password">
  <span class="error">* <?php echo $passwordErr;?></span>
  <br><br>
  Confirm Password: <input type="password" name="confirmPassword">
  <span class="error">* <?php echo $confirmPasswordErr;?></span>
  <br><br>
  <input type="submit" name="submit" value="Submit">  
</form>
<p>Already registered? <a href="login.php">Login here</a>.</p>
</div>
</body>
</html>
