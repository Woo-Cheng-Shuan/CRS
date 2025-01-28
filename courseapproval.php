<?php
include ('crssession.php');
if (!session_id())
{
  session_start();
}

include 'headeradvisor.php';
include 'dbconnect.php';

// First, let's auto-approve eligible registrations
$auto_approve_sql = "UPDATE tb_registration r
                    JOIN tb_section s ON r.r_section = s.s_id
                    LEFT JOIN (
                        SELECT r_section, COUNT(*) as enrolled
                        FROM tb_registration
                        WHERE r_status IN (1, 2)
                        GROUP BY r_section
                    ) counts ON s.s_id = counts.r_section
                    SET r.r_status = 2
                    WHERE r.r_status = 1 
                    AND (counts.enrolled IS NULL OR counts.enrolled < s.s_maxstudent)";
mysqli_query($con, $auto_approve_sql);

// Then retrieve remaining pending registrations
$sql = "SELECT r.*, u.u_sno, u.u_name, c.c_name, rs.s_desc,
        s.s_maxstudent,
        (SELECT COUNT(*) 
         FROM tb_registration 
         WHERE r_section = r.r_section 
         AND r_status IN (1, 2)) as current_students
        FROM tb_registration r
        LEFT JOIN tb_user u ON r.r_student = u.u_sno
        LEFT JOIN tb_course c ON r.r_course = c.c_code
        LEFT JOIN tb_regstatus rs ON r.r_status = rs.s_id
        LEFT JOIN tb_section s ON r.r_section = s.s_id
        WHERE r.r_status = 1
        ORDER BY r.r_tid DESC";

//Execute SQL statement on DB
$result = mysqli_query($con, $sql);
?>

<div class="container">
  <?php if(isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?php 
        echo $_SESSION['success'];
        unset($_SESSION['success']);
      ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <br><br>
  <h5>Course Registration Approval</h5>

  <table class="table table-striped table-hover">
    <thead>
      <tr>
        <th scope="col">Semester</th>
        <th scope="col">Student ID</th>
        <th scope="col">Student Name</th>      
        <th scope="col">Course</th>
        <th scope="col">Course Name</th>
        <th scope="col">Current/Max Students</th>
        <th scope="col">Status</th>
        <th scope="col">Operation</th>
      </tr>
    </thead>

    <tbody>
      <?php
      if(mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_array($result)){
          echo "<tr>";
          echo "<td>".$row['r_sem']."</td>";
          echo "<td>".$row['u_sno']."</td>";
          echo "<td>".$row['u_name']."</td>";
          echo "<td>".$row['r_course']."</td>";
          echo "<td>".$row['c_name']."</td>";
          echo "<td>".$row['current_students']."/".$row['s_maxstudent']."</td>";
          echo "<td>".$row['s_desc']."</td>";
          echo "<td>";
          echo "<a href='courseapprovalprocess.php?id=".$row['r_tid']."' class='btn btn-primary btn-sm'>Approval</a>";
          echo "</td>";
          echo "</tr>";
        }
      } else {
        echo "<tr><td colspan='8' class='text-center'>No pending registrations found</td></tr>";
      }
      ?>
    </tbody>
  </table>
</div>

<?php include 'footer.php';?>

