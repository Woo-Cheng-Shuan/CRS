<?php
include ('crssession.php');
if (!session_id())
{
  session_start();
}

include 'headerlec.php';
include 'dbconnect.php';

// Get lecturer ID from session
$lecturer_id = $_SESSION['active_user'];

// Get lecturer name
$sql = "SELECT u_name FROM tb_user WHERE u_sno = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "s", $lecturer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$lecturer = mysqli_fetch_assoc($result);

?>

<div class="container mt-4">
  <h2>My Assigned Courses</h2>
  
  <?php
  // Get unique semesters for this lecturer from sections
  $sql = "SELECT DISTINCT s.s_sem 
          FROM tb_section s
          WHERE s.s_lecturer = ?
          ORDER BY s.s_sem DESC";
  $stmt = mysqli_prepare($con, $sql);
  mysqli_stmt_bind_param($stmt, "s", $lecturer_id);
  mysqli_stmt_execute($stmt);
  $semesters = mysqli_stmt_get_result($stmt);
  
  while($sem = mysqli_fetch_array($semesters)) {
    $current_sem = $sem['s_sem'];
    ?>
    
    <div class="card mb-4">
      <div class="card-header">
        <h4>Semester <?php echo htmlspecialchars($current_sem); ?></h4>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Course Code</th>
                <th>Course Name</th>
                <th>Section</th>
                <th>Number of Students</th>
                <th>Capacity</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // Get courses and sections for this semester
              $sql = "SELECT 
                        c.c_code,
                        c.c_name,
                        s.s_number as section,
                        s.s_maxstudent as capacity,
                        (SELECT COUNT(*) 
                         FROM tb_registration r 
                         WHERE r.r_course = c.c_code 
                         AND r.r_section = s.s_id
                         AND r.r_status = 2) as student_count
                      FROM tb_section s
                      JOIN tb_course c ON s.s_course_code = c.c_code
                      WHERE s.s_lecturer = ? 
                      AND s.s_sem = ?
                      ORDER BY c.c_code, s.s_number";
              
              $stmt = mysqli_prepare($con, $sql);
              mysqli_stmt_bind_param($stmt, "ss", $lecturer_id, $current_sem);
              mysqli_stmt_execute($stmt);
              $courses = mysqli_stmt_get_result($stmt);
              
              while($course = mysqli_fetch_array($courses)) {
                echo "<tr>";
                echo "<td><a href='course_details.php?code=" . urlencode($course['c_code']) . "&sem=" . urlencode($current_sem) . "'>" . htmlspecialchars($course['c_code']) . "</a></td>";
                echo "<td>".htmlspecialchars($course['c_name'])."</td>";
                echo "<td>".htmlspecialchars($course['section'])."</td>";
                echo "<td>".$course['student_count']."</td>";
                echo "<td>".$course['capacity']."</td>";
                echo "</tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    
  <?php
  }
  ?>
  
</div>

<?php include 'footer.php'; ?>
