<?php
include ('crssession.php');
if (!session_id())
{
  session_start();
}

include 'headeradvisor.php';
include 'dbconnect.php';

//Get user ID
if(isset($_GET['id'])) {
  $tid = $_GET['id'];
}

//Retrieve registration details with current student count
$sql = "SELECT r.*, u.u_name, c.*, s.s_desc,
        (SELECT COUNT(*) FROM tb_registration 
         WHERE r_course = r.r_course 
         AND r_status = 2) as current_students
        FROM tb_registration r
        LEFT JOIN tb_user u ON r.r_student = u.u_sno
        LEFT JOIN tb_course c ON r.r_course = c.c_code
        LEFT JOIN tb_regstatus s ON r.r_status = s.s_id
        WHERE r_tid = '$tid'";

//Execute SQL statement on DB
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_array($result);

// Auto-approve if there's capacity
if($row['r_status'] == 1) { // Only check if status is "Received"
    if($row['current_students'] < $row['c_maxstudent']) {
        // Update status to Approved (2)
        $update_sql = "UPDATE tb_registration SET r_status = 2 WHERE r_tid = '$tid'";
        mysqli_query($con, $update_sql);
        
        // Refresh the page to show updated status
        header("Location: courseapprovalprocess.php?id=$tid&auto=1");
        exit();
    }
}
?>

<div class="container">
<br><br>
<?php
// Show auto-approval message
if(isset($_GET['auto']) && $_GET['auto'] == 1) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            Registration has been automatically approved as there is sufficient capacity in the course.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
}
?>
<table class="table table-striped table-hover">
  <tr>
    <td>Transaction ID</td>
    <td><?php echo $row['r_tid'];?></td>
  </tr>

  <tr>
    <td>Semester</td>
    <td><?php echo $row['r_sem'];?></td>
  </tr>

  <tr>
    <td>Student ID</td>
    <td><?php echo $row['u_sno'];?></td>
  </tr>

  <tr>
    <td>Student Name</td>
    <td><?php echo $row['u_name'];?></td>
  </tr>

  <tr>
    <td>Course</td>
    <td><?php echo $row['r_course'];?></td>
  </tr>

  <tr>
    <td>Course Name</td>
    <td><?php echo $row['c_name'];?></td>
  </tr>

  <tr>
    <td>Course Capacity</td>
    <td><?php echo $row['current_students']."/".$row['c_maxstudent'];?></td>
  </tr>

  <tr>
    <td>Status</td>
    <td><?php echo $row['s_desc'];?></td>
  </tr>

  <tr>
    <td colspan="2" class="text-center">
      <?php if($row['r_status'] == 1) { // Only show buttons if status is still "Received" ?>
        <a href="updatestatus.php?tid=<?php echo $tid; ?>&status=2" class="btn btn-success">Approve</a>
        <a href="updatestatus.php?tid=<?php echo $tid; ?>&status=3" class="btn btn-danger">Reject</a>
      <?php } ?>
    </td>
  </tr>
</table>
</div>

<?php include 'footer.php';?>

