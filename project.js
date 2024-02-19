// Function to validate email format
function validateEmail(email) {
    var re = /\S+@\S+\.\S+/;
    return re.test(email);
}

// Function to validate phone number format
function validatePhone(phone) {
    var re = /^\d{10}$/;
    return re.test(phone);
}

// Function to validate password strength
function validatePassword(password) {
    // Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one digit
    var re = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
    return re.test(password);
}

// Function to convert input to sentence case
function toSentenceCase(input) {
    return input.charAt(0).toUpperCase() + input.slice(1).toLowerCase();
}

// Function to display error messages
function showError(inputId, message) {
    var errorElement = document.getElementById(inputId + "-error");
    errorElement.innerHTML = message;
    errorElement.style.display = "block";
}

// Function to hide error messages
function hideError(inputId) {
    var errorElement = document.getElementById(inputId + "-error");
    errorElement.style.display = "none";
}

// Function to perform form validation
function validateForm() {
    var name = document.getElementById("name").value;
    var phone = document.getElementById("phone").value;
    var email = document.getElementById("email").value;
    var password = document.getElementById("password").value;
    var confirmPassword = document.getElementById("confirmPassword").value;
    
    var isValid = true;

    // Validate name
    if (name === "") {
        showError("name", "Name is required");
        isValid = false;
    } else {
        hideError("name");
        // Convert name to sentence case
        document.getElementById("name").value = toSentenceCase(name);
    }

    // Validate phone number
    if (!validatePhone(phone)) {
        showError("phone", "Invalid phone number format");
        isValid = false;
    } else {
        hideError("phone");
    }

    // Validate email
    if (!validateEmail(email)) {
        showError("email", "Invalid email format");
        isValid = false;
    } else {
        hideError("email");
    }

    // Validate password
    if (!validatePassword(password)) {
        showError("password", "Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one digit");
        isValid = false;
    } else {
        hideError("password");
    }

    // Confirm Password
    if (confirmPassword !== password) {
        showError("confirmPassword", "Passwords do not match");
        isValid = false;
    } else {
        hideError("confirmPassword");
    }

    return isValid;
}

// Add event listeners to input fields to trigger validation as the user types
document.getElementById("name").addEventListener("input", function() {
    validateForm();
});

document.getElementById("phone").addEventListener("input", function() {
    validateForm();
});

document.getElementById("email").addEventListener("input", function() {
    validateForm();
});

document.getElementById("password").addEventListener("input", function() {
    validateForm();
});

document.getElementById("confirmPassword").addEventListener("input", function() {
    validateForm();
});

// Add event listener to form submit button
document.getElementById("submit").addEventListener("click", function(event) {
    if (!validateForm()) {
        event.preventDefault(); // Prevent form submission if validation fails
    }
});
