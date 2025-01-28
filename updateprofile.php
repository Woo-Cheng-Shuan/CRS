<?php
include ('crssession.php');
if (!session_id()) {
    session_start();
}

include 'dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $contact = mysqli_real_escape_string($con, (string)$_POST['contact']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $state = mysqli_real_escape_string($con, $_POST['state']);
    $userid = $_SESSION['funame'];

    $sql = "UPDATE tb_user SET 
            u_name = '$name',
            u_contact = '$contact',
            u_email = '$email',
            u_state = '$state'
            WHERE u_sno = '$userid'";

    if (mysqli_query($con, $sql)) {
        $_SESSION['success'] = "Profile updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating profile: " . mysqli_error($con);
    }
    
    mysqli_close($con);
    header('Location: profile.php');
    exit();
}
?>