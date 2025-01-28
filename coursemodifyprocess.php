<?php
include ('crssession.php');
if (!session_id()) {
    session_start();
}

include 'headeradvisor.php';
include 'dbconnect.php';

//Get user ID
if(isset($_GET['id'])) {
    $tid = $_GET['id'];
}

//Retrieve all registered courses
$sql = "SELECT * FROM tb_registration
        LEFT JOIN tb_user ON tb_registration.r_student = tb_user.u_sno
        LEFT JOIN tb_course ON tb_registration.r_course = tb_course.c_code
        LEFT JOIN tb_regstatus ON tb_registration.r_status = tb_regstatus.s_id
        WHERE r_tid = '$tid'";

//Execute SQL statement on DB
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_array($result);
?>

<div class="container">
<br><br>
<form action="updateregistration.php" method="POST">
    <input type="hidden" name="tid" value="<?php echo $row['r_tid'];?>">
    
    <table class="table table-striped table-hover">
        <tr>
            <td>Transaction ID</td>
            <td><?php echo $row['r_tid'];?></td>
        </tr>

        <tr>
            <td>Semester</td>
            <td>
                <input type="text" class="form-control" name="semester" value="<?php echo $row['r_sem'];?>" required>
            </td>
        </tr>

        <tr>
            <td>Student ID</td>
            <td>
                <select class="form-control" name="student" required>
                    <?php
                    $sql_students = "SELECT u_sno, u_name FROM tb_user WHERE u_utype = 2";
                    $result_students = mysqli_query($con, $sql_students);
                    while($student = mysqli_fetch_array($result_students)) {
                        $selected = ($student['u_sno'] == $row['u_sno']) ? 'selected' : '';
                        echo "<option value='".$student['u_sno']."' ".$selected.">".$student['u_sno']." - ".$student['u_name']."</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>

        <tr>
            <td>Course</td>
            <td>
                <select class="form-control" name="course" required>
                    <?php
                    $sql_courses = "SELECT c_code, c_name FROM tb_course";
                    $result_courses = mysqli_query($con, $sql_courses);
                    while($course = mysqli_fetch_array($result_courses)) {
                        $selected = ($course['c_code'] == $row['c_code']) ? 'selected' : '';
                        echo "<option value='".$course['c_code']."' ".$selected.">".$course['c_code']." - ".$course['c_name']."</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>

        <tr>
            <td>Status</td>
            <td>
                <select class="form-control" name="status" required>
                    <?php
                    $sql_status = "SELECT s_id, s_desc FROM tb_regstatus";
                    $result_status = mysqli_query($con, $sql_status);
                    while($status = mysqli_fetch_array($result_status)) {
                        $selected = ($status['s_id'] == $row['r_status']) ? 'selected' : '';
                        echo "<option value='".$status['s_id']."' ".$selected.">".$status['s_desc']."</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>

        <tr>
            <td colspan="2" class="text-center">
                <button type="submit" class="btn btn-primary">Update Registration</button>
                <a href="courselist.php" class="btn btn-secondary">Cancel</a>
            </td>
        </tr>
    </table>
</form>
</div>

<?php include 'footer.php';?> 