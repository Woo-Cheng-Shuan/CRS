<?php
//Connect to DB
include('dbconnect.php');

// Set character encoding
mysqli_set_charset($con, "utf8mb4");

//Retrieve data from form and sanitize
$funame = trim(htmlspecialchars($_POST['funame']));
$fpwd = trim($_POST['fpwd']); // Raw password input - don't use htmlspecialchars
$confirm_password = trim($_POST['confirm_password']);
$femail = trim(htmlspecialchars($_POST['femail']));
$fname = trim(htmlspecialchars($_POST['fname']));
$fcontact = trim(htmlspecialchars($_POST['fcontact']));
$fstate = trim(htmlspecialchars($_POST['fstate']));

// Server-side password validation
function validatePassword($password) {
    // Check length (8-20 characters)
    if (strlen($password) < 8 || strlen($password) > 20) {
        return ['valid' => false, 'error' => 'Password must be between 8 and 20 inputs'];
    }
    
    // Check uppercase letter
    if (!preg_match('/.*[A-Z].*/', $password)) {
        return ['valid' => false, 'error' => 'Password must contain at least one uppercase letter'];
    }
    
    // Check lowercase letter
    if (!preg_match('/.*[a-z].*/', $password)) {
        return ['valid' => false, 'error' => 'Password must contain at least one lowercase letter'];
    }
    
    // Check numbers
    if (!preg_match('/.*[0-9].*/', $password)) {
        return ['valid' => false, 'error' => 'Password must contain at least one number'];
    }
    
    return ['valid' => true, 'error' => ''];
}

// Validate password
$validation_result = validatePassword($fpwd);
if (!$validation_result['valid']) {
    header('Location: register.php?error=' . urlencode($validation_result['error']));
    exit();
}

// Verify passwords match
if ($fpwd !== $confirm_password) {
    header('Location: register.php?error=' . urlencode('Passwords do not match'));
    exit();
}

// Hash the password - remove the mb_convert_encoding and just use password_hash directly
$hashed_password = password_hash($fpwd, PASSWORD_DEFAULT);

// Determine user type based on first character of user ID
$firstChar = strtoupper(substr($funame, 0, 1));
switch ($firstChar) {
    case 'L':
        $utype = '1'; // Lecturer
        break;
    case 'S':
        $utype = '2'; // Student
        break;
    case 'A':
        $utype = '3'; // Advisor
        break;
    default:
        header('Location: register.php?error=' . urlencode('Invalid user ID format. Must start with L, S, or A'));
        exit();
}

// Email validation function
function validateEmail($email) {
    // Remove any whitespace
    $email = trim($email);
    
    // Check if email is empty
    if (empty($email)) {
        return ['valid' => false, 'error' => 'Email is required'];
    }
    
    // Check email format using PHP's filter
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['valid' => false, 'error' => 'Invalid email format'];
    }
    
    // Check email length
    if (strlen($email) > 30) {  // Match your database field length
        return ['valid' => false, 'error' => 'Email is too long (maximum 30 characters)'];
    }
    
    return ['valid' => true, 'error' => ''];
}

// Validate email
$email_validation = validateEmail($femail);
if (!$email_validation['valid']) {
    header('Location: register.php?error=' . urlencode($email_validation['error']));
    exit();
}

//SQL Insert operation with hashed password
$sql = "INSERT INTO tb_user(u_sno, u_pwd, u_email, u_name, u_contact, u_state, U_req, u_utype)
        VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP(), ?)";

// Use prepared statement
$stmt = mysqli_prepare($con, $sql);
if ($stmt === false) {
    die('Error in prepare statement: ' . mysqli_error($con));
}

mysqli_stmt_bind_param($stmt, "sssssss", $funame, $hashed_password, $femail, $fname, $fcontact, $fstate, $utype);
$result = mysqli_stmt_execute($stmt);

//Close connection
mysqli_stmt_close($stmt);
mysqli_close($con);

if ($result) {
    // Set success message in session
    session_start();
    $_SESSION['success'] = "Registration successful! Please login with your credentials.";
    
    // Show JavaScript alert and redirect
    echo "<script>
        alert('Registration successful!');
        window.location.href = 'login.php';
    </script>";
} else {
    // Registration failed
    header('Location: register.php?error=registration_failed');
}
?>