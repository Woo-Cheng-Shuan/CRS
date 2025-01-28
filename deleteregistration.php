<?php
include ('crssession.php');
if (!session_id())
{
  session_start();
}

include 'dbconnect.php';

if(isset($_GET['id'])) {
    $registration_id = $_GET['id'];
    $student_id = $_SESSION['active_user'];
    
    // Verify the registration belongs to the student and is in "Received" status
    $sql = "DELETE FROM tb_registration 
            WHERE r_tid = ? AND r_student = ? AND r_status = 1";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "is", $registration_id, $student_id);
    
    if(mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Registration has been deleted successfully.";
    } else {
        $_SESSION['error'] = "Error deleting registration: " . mysqli_error($con);
    }
} else {
    $_SESSION['error'] = "Invalid request.";
}

header('Location: courseview.php');
exit();
?> 