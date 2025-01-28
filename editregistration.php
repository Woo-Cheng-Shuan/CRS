<?php
include ('crssession.php');
if (!session_id())
{
  session_start();
}

include 'headerstudent.php';
include 'dbconnect.php';

$registration_id = isset($_GET['id']) ? $_GET['id'] : 0;
$student_id = $_SESSION['active_user'];

// Get registration details
$sql = "SELECT r.*, c.c_name, s.s_number 
        FROM tb_registration r
        LEFT JOIN tb_course c ON r.r_course = c.c_code
        LEFT JOIN tb_section s ON r.r_section = s.s_id
        WHERE r.r_tid = ? AND r.r_student = ? AND r.r_status = 1";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "is", $registration_id, $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) == 0) {
    $_SESSION['error'] = "Invalid registration or you don't have permission to edit this registration.";
    header('Location: courseview.php');
    exit();
}

$registration = mysqli_fetch_assoc($result);

// Get available sections for the course
$sql = "SELECT s.*, 
        COUNT(DISTINCT CASE WHEN r.r_status IN (1, 2) THEN r.r_tid ELSE NULL END) as current_students
        FROM tb_section s
        LEFT JOIN tb_registration r ON s.s_id = r.r_section
        WHERE s.s_course_code = ? AND s.s_sem = ?
        GROUP BY s.s_id
        HAVING current_students < s.s_maxstudent OR s.s_id = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "ssi", $registration['r_course'], $registration['r_sem'], $registration['r_section']);
mysqli_stmt_execute($stmt);
$sections_result = mysqli_stmt_get_result($stmt);
?>

<div class="container">
  <br><br>
  <h5>Edit Course Registration</h5>

  <form method="POST" action="updateregistration.php">
    <input type="hidden" name="registration_id" value="<?php echo $registration_id; ?>">
    
    <div class="mb-3">
      <label class="form-label">Course</label>
      <input type="text" class="form-control" value="<?php echo $registration['r_course'] . ' - ' . $registration['c_name']; ?>" readonly>
    </div>

    <div class="mb-3">
      <label class="form-label">Section</label>
      <select class="form-select" name="section_id" required>
        <?php while($section = mysqli_fetch_array($sections_result)) { ?>
          <option value="<?php echo $section['s_id']; ?>" 
                  <?php echo ($section['s_id'] == $registration['r_section']) ? 'selected' : ''; ?>>
            Section <?php echo $section['s_number']; ?> 
            (<?php echo $section['current_students']; ?>/<?php echo $section['s_maxstudent']; ?> students)
          </option>
        <?php } ?>
      </select>
    </div>

    <button type="submit" class="btn btn-primary">Update Registration</button>
    <a href="courseview.php" class="btn btn-secondary">Cancel</a>
  </form>
  <br><br>
</div>

<?php include 'footer.php'; ?> 