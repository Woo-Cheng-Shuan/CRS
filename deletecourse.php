<?php
include('dbconnect.php');

$code = $_GET['code'];

// Check if there are any registrations for this course
$check_sql = "SELECT COUNT(*) as count FROM tb_registration WHERE r_course = '$code'";
$result = mysqli_query($con, $check_sql);
$row = mysqli_fetch_array($result);

if ($row['count'] > 0) {
    echo "<script>
            alert('Cannot delete course. There are students registered for this course.');
            window.location.href='addnewcourse.php';
          </script>";
} else {
    $sql = "DELETE FROM tb_course WHERE c_code = '$code'";
    mysqli_query($con, $sql);
    header('Location: addnewcourse.php');
}

mysqli_close($con);
?> 