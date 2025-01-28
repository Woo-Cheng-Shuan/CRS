<?php
include('dbconnect.php');

$code = $_POST['fcode'];
$sem = $_POST['fsem'];
$name = $_POST['fname'];
$max = $_POST['fmax'];
$lec = $_POST['flec'];

$sql = "UPDATE tb_course 
        SET c_sem = '$sem', 
            c_name = '$name', 
            c_maxstudent = '$max', 
            c_lec = '$lec' 
        WHERE c_code = '$code'";

mysqli_query($con, $sql);
mysqli_close($con);

header('Location: addnewcourse.php');
?> 