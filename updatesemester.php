<?php
include('dbconnect.php');

if(isset($_POST['active_semester'])) {
    $semester = $_POST['active_semester'];
    
    $sql = "INSERT INTO tb_settings (active_semester) VALUES ('$semester')";
    mysqli_query($con, $sql);
    
    header('Location: addnewcourse.php');
    exit();
} else {
    echo "No semester selected";
}

mysqli_close($con);
?> 