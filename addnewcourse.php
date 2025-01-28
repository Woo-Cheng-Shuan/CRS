<?php
include ('crssession.php');
if (!session_id())
{
  session_start();
}

// Check if user is logged in and is an advisor
if (!isset($_SESSION['active_user']) || $_SESSION['u_utype'] != 3) {
    $_SESSION['error'] = "Unauthorized access";
    header('Location: login.php');
    exit();
}

$uic = $_SESSION['active_user'];

include 'headeradvisor.php';
include 'dbconnect.php';
?>

<div class="container mt-4">
  <div class="row">
    <div class="col">
      <h3>Add New Course</h3>
      <form method="POST" action="addnewcourseprocess.php">
        <div class="mb-3">
          <label for="semester" class="form-label">Semester</label>
          <input type="text" class="form-control" id="semester" name="fsem" required>
        </div>
        <div class="mb-3">
          <label for="coursecode" class="form-label">Course Code</label>
          <input type="text" class="form-control" id="coursecode" name="fcode" required>
        </div>
        <div class="mb-3">
          <label for="coursename" class="form-label">Course Name</label>
          <input type="text" class="form-control" id="coursename" name="fname" required>
        </div>
        <div class="mb-3">
          <label for="credit" class="form-label">Credit Hours</label>
          <input type="number" class="form-control" id="credit" name="fcredit" required min="1" max="4">
        </div>
        
        <div class="mb-3">
          <label class="form-label">Course Sections</label>
          <div id="sectionsContainer">
            <div class="section-row row mb-2">
              <div class="col-md-3">
                <input type="text" class="form-control" name="section_number[]" placeholder="Section Number (e.g., 01)" required>
              </div>
              <div class="col-md-3">
                <input type="number" class="form-control" name="section_capacity[]" placeholder="Max Students" required>
              </div>
              <div class="col-md-4">
                <select class="form-control" name="section_lecturer[]" required>
                  <option value="">Select Lecturer</option>
                  <?php
                  // Get lecturer list from tb_user where u_utype = 1 (Lecturer)
                  $sql_lec = "SELECT u_sno, u_name FROM tb_user WHERE u_utype = 1";
                  $result_lec = mysqli_query($con, $sql_lec);
                  
                  while($row_lec = mysqli_fetch_array($result_lec)) {
                    echo "<option value='".$row_lec['u_sno']."'>".$row_lec['u_name']."</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="col-md-2">
                <button type="button" class="btn btn-danger remove-section" style="display: none;">Remove</button>
              </div>
            </div>
          </div>
          <button type="button" class="btn btn-secondary mt-2" id="addSection">Add Another Section</button>
        </div>
        <button type="submit" class="btn btn-primary">Add Course</button>
      </form>
    </div>
  </div>
</div>

<div class="container mt-4">
  <div class="row">
    <div class="col">
      <h3>Manage Active Semester</h3>
      <form method="POST" action="updatesemester.php">
        <div class="mb-3">
          <label for="activeSemester" class="form-label">Set Active Semester for Registration</label>
          <select class="form-control" id="activeSemester" name="active_semester" required>
            <?php
            // Get unique semesters from both registration and course tables
            $sql = "SELECT DISTINCT semester FROM (
                SELECT r_sem as semester FROM tb_registration 
                UNION 
                SELECT c_sem as semester FROM tb_course
            ) as semesters ORDER BY semester DESC";
            $result = mysqli_query($con, $sql);
            
            if (!$result) {
                echo "Error in semester query: " . mysqli_error($con);
            }
            
            // Get current active semester
            $activeSql = "SELECT active_semester FROM tb_settings ORDER BY id DESC LIMIT 1";
            $activeResult = mysqli_query($con, $activeSql);
            
            if ($activeResult && mysqli_num_rows($activeResult) > 0) {
                $activeSemester = mysqli_fetch_array($activeResult)['active_semester'];
            } else {
                $activeSemester = ''; // Default value if no active semester is set
            }
            
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_array($result)) {
                    $selected = ($row['semester'] == $activeSemester) ? 'selected' : '';
                    echo "<option value='".$row['semester']."' ".$selected.">".$row['semester']."</option>";
                }
            } else {
                echo "<option value=''>No semesters available</option>";
            }
            ?>
          </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Active Semester</button>
      </form>
      
      <div class="mt-3">
        <p><strong>Current Active Semester:</strong> <?php echo $activeSemester ? $activeSemester : 'None set'; ?></p>
      </div>
    </div>
  </div>
</div>

<br><br><br>

<script>
document.getElementById('addSection').addEventListener('click', function() {
    const container = document.getElementById('sectionsContainer');
    const newRow = document.createElement('div');
    newRow.className = 'section-row row mb-2';
    
    // Get the lecturer options from the first select element
    const firstSelect = document.querySelector('select[name="section_lecturer[]"]');
    const lecturerOptions = firstSelect.innerHTML;
    
    newRow.innerHTML = `
        <div class="col-md-3">
            <input type="text" class="form-control" name="section_number[]" placeholder="Section Number (e.g., 01)" required>
        </div>
        <div class="col-md-3">
            <input type="number" class="form-control" name="section_capacity[]" placeholder="Max Students" required>
        </div>
        <div class="col-md-4">
            <select class="form-control" name="section_lecturer[]" required>
                ${lecturerOptions}
            </select>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger remove-section">Remove</button>
        </div>
    `;
    
    container.appendChild(newRow);
    
    // Show all remove buttons if there's more than one section
    const removeButtons = document.querySelectorAll('.remove-section');
    if (removeButtons.length > 1) {
        removeButtons.forEach(button => button.style.display = 'block');
    }
});

// Event delegation for remove buttons
document.getElementById('sectionsContainer').addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-section')) {
        const sectionRows = document.querySelectorAll('.section-row');
        if (sectionRows.length > 1) {
            e.target.closest('.section-row').remove();
            
            // Hide remove button if only one section remains
            const removeButtons = document.querySelectorAll('.remove-section');
            if (removeButtons.length === 1) {
                removeButtons[0].style.display = 'none';
            }
        }
    }
});
</script>

<?php include 'footer.php';?>

