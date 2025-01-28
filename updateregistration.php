<?php
include ('crssession.php');
if (!session_id())
{
  session_start();
}

include 'dbconnect.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tid = $_POST['tid'];
    $semester = $_POST['semester'];
    $student = $_POST['student'];
    $course = $_POST['course'];
    $status = $_POST['status'];
    
    // Update the registration
    $sql = "UPDATE tb_registration 
            SET r_sem = ?, 
                r_student = ?, 
                r_course = ?, 
                r_status = ? 
            WHERE r_tid = ?";
    
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssii", $semester, $student, $course, $status, $tid);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Registration updated successfully.";
    } else {
        $_SESSION['error'] = "Error updating registration: " . mysqli_error($con);
    }
} else {
    $_SESSION['error'] = "Invalid request.";
}

header('Location: courselist.php');
exit();
?> 