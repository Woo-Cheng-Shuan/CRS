<?php
include ('crssession.php');
if (!session_id())
{
  session_start();
}

include 'headerstudent.php';
include 'dbconnect.php';

//Get user ID
$uic = $_SESSION['active_user'];

// Get selected semester or default to showing all
$selectedSemester = isset($_GET['semester']) ? $_GET['semester'] : 'all';

// Base SQL query
$baseSQL = "SELECT tb_registration.*, tb_course.c_name, tb_regstatus.s_desc, tb_section.s_number 
            FROM tb_registration
            LEFT JOIN tb_course ON tb_registration.r_course = tb_course.c_code
            LEFT JOIN tb_regstatus ON tb_registration.r_status = tb_regstatus.s_id
            LEFT JOIN tb_section ON tb_registration.r_section = tb_section.s_id
            WHERE r_student = ? ";

// Add semester filter if specific semester selected
if($selectedSemester != 'all') {
    $baseSQL .= " AND r_sem = ?";
}

// Prepare and execute query for approved courses
$approvedSQL = $baseSQL . " AND r_status = 2 ORDER BY r_sem DESC, r_course ASC";
$stmt = mysqli_prepare($con, $approvedSQL);
if($selectedSemester != 'all') {
    mysqli_stmt_bind_param($stmt, "ss", $uic, $selectedSemester);
} else {
    mysqli_stmt_bind_param($stmt, "s", $uic);
}
mysqli_stmt_execute($stmt);
$approvedResult = mysqli_stmt_get_result($stmt);

// Prepare and execute query for pending/rejected courses
$otherSQL = $baseSQL . " AND r_status IN (1, 3) ORDER BY r_sem DESC, r_course ASC";
$stmt = mysqli_prepare($con, $otherSQL);
if($selectedSemester != 'all') {
    mysqli_stmt_bind_param($stmt, "ss", $uic, $selectedSemester);
} else {
    mysqli_stmt_bind_param($stmt, "s", $uic);
}
mysqli_stmt_execute($stmt);
$otherResult = mysqli_stmt_get_result($stmt);

// Credit Hours Summary
?>

<div class="container">
  <br><br>
  <h5>My Registered Courses</h5>

  <!-- Add semester filter -->
  <div class="row mb-4">
    <div class="col-md-6">
      <form method="GET" action="" class="d-flex">
        <select class="form-select me-2" name="semester" onchange="this.form.submit()">
          <option value="all" <?php echo $selectedSemester == 'all' ? 'selected' : ''; ?>>All Semesters</option>
          <?php
          // Get unique semesters from registration table
          $semesterSql = "SELECT DISTINCT r_sem FROM tb_registration WHERE r_student = ? ORDER BY r_sem DESC";
          $stmt = mysqli_prepare($con, $semesterSql);
          mysqli_stmt_bind_param($stmt, "s", $uic);
          mysqli_stmt_execute($stmt);
          $semesterResult = mysqli_stmt_get_result($stmt);
          
          while($semRow = mysqli_fetch_array($semesterResult)) {
            $selected = ($selectedSemester == $semRow['r_sem']) ? 'selected' : '';
            echo "<option value='".$semRow['r_sem']."' ".$selected.">".$semRow['r_sem']."</option>";
          }
          ?>
        </select>
      </form>
    </div>
  </div>

  <!-- Approved Courses Table -->
  <h6 class="mb-3">Approved Courses</h6>
  <table class="table table-striped table-hover">
    <thead>
      <tr>
        <th scope="col">Semester</th>
        <th scope="col">Course</th>
        <th scope="col">Course Name</th>
        <th scope="col">Section</th>
        <th scope="col">Status</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if(mysqli_num_rows($approvedResult) > 0) {
        while($row = mysqli_fetch_array($approvedResult)){
          echo "<tr>";
          echo "<td>".$row['r_sem']."</td>";
          echo "<td>".$row['r_course']."</td>";
          echo "<td>".$row['c_name']."</td>";
          echo "<td>".$row['s_number']."</td>";
          echo "<td><span class='text-success'>".$row['s_desc']."</span></td>";
          echo "</tr>";
        }
      } else {
        echo "<tr><td colspan='5' class='text-center'>No approved courses found</td></tr>";
      }
      ?>
    </tbody>
  </table>

  <!-- Credit Hours Summary -->
  <?php
  // Calculate total credit hours for approved courses
  $creditSQL = "SELECT SUM(c.c_credit) as total_credits 
                FROM tb_registration r
                LEFT JOIN tb_course c ON r.r_course = c.c_code
                WHERE r.r_student = ? AND r.r_status = 2";
  if($selectedSemester != 'all') {
      $creditSQL .= " AND r_sem = ?";
  }
  
  $stmt = mysqli_prepare($con, $creditSQL);
  if($selectedSemester != 'all') {
      mysqli_stmt_bind_param($stmt, "ss", $uic, $selectedSemester);
  } else {
      mysqli_stmt_bind_param($stmt, "s", $uic);
  }
  mysqli_stmt_execute($stmt);
  $creditResult = mysqli_stmt_get_result($stmt);
  $creditRow = mysqli_fetch_assoc($creditResult);
  $totalCredits = $creditRow['total_credits'] ?? 0;
  ?>
  
  <div class="alert alert-info mb-4">
    <strong>Total Credit Hours (Approved Courses): <?php echo $totalCredits; ?> credits</strong>
    <?php if($selectedSemester != 'all'): ?>
      <span class="ms-2">(for semester <?php echo $selectedSemester; ?>)</span>
    <?php endif; ?>
  </div>

  <br><br>

  <!-- Pending/Rejected Courses Table -->
  <h6 class="mb-3">Pending and Rejected Courses</h6>
  <table class="table table-striped table-hover">
    <thead>
      <tr>
        <th scope="col">Semester</th>
        <th scope="col">Course</th>
        <th scope="col">Course Name</th>
        <th scope="col">Section</th>
        <th scope="col">Status</th>
        <th scope="col">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if(mysqli_num_rows($otherResult) > 0) {
        while($row = mysqli_fetch_array($otherResult)){
          echo "<tr>";
          echo "<td>".$row['r_sem']."</td>";
          echo "<td>".$row['r_course']."</td>";
          echo "<td>".$row['c_name']."</td>";
          echo "<td>".$row['s_number']."</td>";
          echo "<td>".($row['r_status'] == 1 ? 
                "<span class='text-primary'>".$row['s_desc']."</span>" : 
                "<span class='text-danger'>".$row['s_desc']."</span>")."</td>";
          echo "<td>";
          // Only show edit/delete buttons if status is "Received" (status_id = 1)
          if($row['r_status'] == 1) {
            echo "<a href='editregistration.php?id=".$row['r_tid']."' class='btn btn-warning btn-sm me-2'>Edit</a>";
            echo "<a href='deleteregistration.php?id=".$row['r_tid']."' class='btn btn-danger btn-sm' 
                  onclick='return confirm(\"Are you sure you want to delete this registration?\")'>Delete</a>";
          }
          echo "</td>";
          echo "</tr>";
        }
      } else {
        echo "<tr><td colspan='6' class='text-center'>No pending or rejected courses found</td></tr>";
      }
      ?>
    </tbody>
  </table>

  <div class="mt-3">
    <a href="courseregister.php" class="btn btn-primary">Register New Course</a>
  </div>
  <br><br><br><br><br><br>
</div>

<?php include 'footer.php';?>
