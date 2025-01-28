<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Temporary debugging
error_log("POST data received: " . print_r($_POST, true));
error_log("Session data: " . print_r($_SESSION, true));

// Only include one database connection
include 'dbconnect.php';

// Check if connection is successful
if (!$con) {
    error_log("Database connection failed: " . mysqli_connect_error());
    $_SESSION['error'] = "System error. Please try again later.";
    header('Location: login.php');
    exit();
}

// Set character encoding
mysqli_set_charset($con, "utf8mb4");

//Retrieve data from login form
$fusername = trim($_POST['fusername']);
$fpassword = trim($_POST['fpassword']); 

error_log("Login attempt - Username: " . $fusername);

//SQL query
$sql = "SELECT * FROM tb_user WHERE u_sno=?";
$stmt = mysqli_prepare($con, $sql);

if ($stmt === false) {
    error_log("Prepare statement failed: " . mysqli_error($con));
    $_SESSION['error'] = "System error. Please try again later.";
    header('Location: login.php');
    exit();
}

mysqli_stmt_bind_param($stmt, "s", $fusername);
$execute_result = mysqli_stmt_execute($stmt);

if (!$execute_result) {
    error_log("Execute failed: " . mysqli_stmt_error($stmt));
    $_SESSION['error'] = "System error. Please try again later.";
    header('Location: login.php');
    exit();
}

$result = mysqli_stmt_get_result($stmt);
error_log("Number of rows found: " . mysqli_num_rows($result));

if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    error_log("Found user - Type: " . $row['u_utype']);
    error_log("Stored password: " . $row['u_pwd']);
    error_log("Submitted password: " . $fpassword);
    
    // First try password_verify (for hashed passwords)
    if (password_verify($fpassword, $row['u_pwd'])) {
        error_log("Password verified with password_verify()");
        $password_match = true;
    } 
    // Then try direct comparison (for plain text passwords)
    else if ($fpassword === $row['u_pwd']) {
        error_log("Password matched with direct comparison");
        $password_match = true;
    } else {
        error_log("Password did not match either method");
        $password_match = false;
    }
    
    if ($password_match) {
        error_log("Password matched! Setting session variables...");
        $_SESSION['u_sno'] = $row['u_sno'];
        $_SESSION['u_utype'] = $row['u_utype'];
        $_SESSION['active_user'] = $row['u_sno'];
        
        $session_id = session_id();
        $update_sql = "UPDATE tb_user SET session_id = ?, last_activity = NOW() WHERE u_sno = ?";
        $update_stmt = mysqli_prepare($con, $update_sql);
        mysqli_stmt_bind_param($update_stmt, "ss", $session_id, $row['u_sno']);
        mysqli_stmt_execute($update_stmt);
        
        error_log("About to redirect for user type: " . $row['u_utype']);
        
        switch($row['u_utype']) {
            case 1: // Lecturer
                header('Location: lecturer.php');
                exit();
            case 2: // Student
                header('Location: student.php');
                exit();
            case 3: // Advisor
                header('Location: advisor.php');
                exit();
            default:
                error_log("Invalid user type: " . $row['u_utype']);
                $_SESSION['error'] = "Invalid user type";
                header('Location: login.php');
                exit();
        }
    } else {
        error_log("Password mismatch!");
        $_SESSION['error'] = "Invalid username or password";
        header('Location: login.php');
        exit();
    }
} else {
    error_log("No user found with username: " . $fusername);
    $_SESSION['error'] = "Invalid username or password";
    header('Location: login.php');
    exit();
}

mysqli_stmt_close($stmt);
mysqli_close($con);
?>