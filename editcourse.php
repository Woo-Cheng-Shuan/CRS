<?php
include ('crssession.php');
if (!session_id()) {
    session_start();
}

include 'headeradvisor.php';
include 'dbconnect.php';

$code = $_GET['code'];
$sql = "SELECT * FROM tb_course WHERE c_code = '$code'";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_array($result);
?>

<div class="container mt-4">
    <div class="row">
        <div class="col">
            <h3>Edit Course</h3>
            <form method="POST" action="editcourseprocess.php">
                <input type="hidden" name="fcode" value="<?php echo $row['c_code']; ?>">
                <div class="mb-3">
                    <label for="semester" class="form-label">Semester</label>
                    <input type="text" class="form-control" id="semester" name="fsem" value="<?php echo $row['c_sem']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="coursename" class="form-label">Course Name</label>
                    <input type="text" class="form-control" id="coursename" name="fname" value="<?php echo $row['c_name']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="maxstudents" class="form-label">Maximum Students</label>
                    <input type="number" class="form-control" id="maxstudents" name="fmax" value="<?php echo $row['c_maxstudent']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="lecturer" class="form-label">Lecturer</label>
                    <select class="form-control" id="lecturer" name="flec" required>
                        <?php
                        $sql_lec = "SELECT u_sno, u_name FROM tb_user WHERE u_utype = 1";
                        $result_lec = mysqli_query($con, $sql_lec);
                        
                        while($row_lec = mysqli_fetch_array($result_lec)) {
                            $selected = ($row_lec['u_sno'] == $row['c_lec']) ? 'selected' : '';
                            echo "<option value='".$row_lec['u_sno']."' ".$selected.">".$row_lec['u_name']."</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update Course</button>
                <a href="addnewcourse.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div> 