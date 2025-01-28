<?php
include ('crssession.php');
if (!session_id())
{
  session_start();
}

include 'headerstudent.php';
include 'dbconnect.php';

?>

<div class="container">

  <?php
  // Display error message if any
  if(isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
    echo $_SESSION['error'];
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
    unset($_SESSION['error']);
  }

  // Display success message if any
  if(isset($_SESSION['success'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
    echo $_SESSION['success'];
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
    unset($_SESSION['success']);
  }

  // Get active semester
  $sql = "SELECT active_semester FROM tb_settings ORDER BY id DESC LIMIT 1";
  $result = mysqli_query($con, $sql);
  $activeSemester = mysqli_fetch_array($result)['active_semester'];
  ?>

  <br><br><h5>Course Registration Form</h5>
  <p>Current Semester: <?php echo $activeSemester; ?></p>

  <!-- Add search form -->
  <div class="mb-4">
    <form method="GET" action="" class="row g-3">
      <div class="col-auto">
        <input type="text" class="form-control" name="search" placeholder="Search by course code or name" 
          value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
      </div>
      <div class="col-auto">
        <button type="submit" class="btn btn-primary">Search</button>
        <?php if(isset($_GET['search']) && !empty($_GET['search'])): ?>
          <a href="courseregister.php" class="btn btn-secondary">Clear Search</a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <form method="POST" action="courseregisterprocess.php" onsubmit="return confirmSubmission()">
    <input type="hidden" name="fsem" value="<?php echo $activeSemester; ?>">
    
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Select</th>
          <th>Course Code</th>
          <th>Course Name</th>
          <th>Credits</th>
          <th>Section</th>
          <th>Lecturer</th>
          <th>Enrollment</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // Base query with student count - modified to count only approved and pending registrations
        $sql = "SELECT 
                    c.*,
                    s.s_id,
                    s.s_number,
                    s.s_maxstudent,
                    u.u_name as lecturer_name,
                    COUNT(DISTINCT CASE 
                        WHEN r.r_status IN (1, 2) THEN r.r_tid  -- Count only pending (1) and approved (2) registrations
                        ELSE NULL 
                    END) as current_students
                FROM tb_course c
                LEFT JOIN tb_section s ON c.c_code = s.s_course_code AND s.s_sem = c.c_sem
                LEFT JOIN tb_user u ON s.s_lecturer = u.u_sno
                LEFT JOIN tb_registration r ON s.s_id = r.r_section 
                    AND r.r_sem = c.c_sem
                WHERE c.c_sem = ?";
        
        // Add search condition if search term exists
        if(isset($_GET['search']) && !empty($_GET['search'])) {
            $sql .= " AND (c.c_code LIKE ? OR c.c_name LIKE ?)";
        }
        
        $sql .= " GROUP BY c.c_code, s.s_id";  // Group by to get correct count
        $sql .= " ORDER BY c.c_code, s.s_number";
        
        // Add debug output
        echo "<!-- Debug: SQL Query: " . str_replace('?', $activeSemester, $sql) . " -->";
        
        $stmt = mysqli_prepare($con, $sql);
        
        if(isset($_GET['search']) && !empty($_GET['search'])) {
            $search = '%' . $_GET['search'] . '%';
            mysqli_stmt_bind_param($stmt, "sss", $activeSemester, $search, $search);
        } else {
            mysqli_stmt_bind_param($stmt, "s", $activeSemester);
        }
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $current_course = '';
        
        while($row = mysqli_fetch_array($result)) {
            // Add debug info in HTML comments
            echo "<!-- Debug: Section " . $row['s_id'] . " - Current: " . $row['current_students'] . " Max: " . $row['s_maxstudent'] . " -->";
            
            echo "<tr>";
            echo "<td>";
            if ($row['s_id']) {
                $is_full = ($row['current_students'] >= $row['s_maxstudent']);
                if ($is_full) {
                    echo '<span class="text-danger">Section Full</span>';
                } else {
                    echo '<input class="form-check-input" type="radio" name="fcourse[' . $row['c_code'] . ']" value="' . $row['s_id'] . '">';
                }
            } else {
                echo 'No sections available';
            }
            echo "</td>";
            echo "<td>" . $row['c_code'] . "</td>";
            echo "<td>" . $row['c_name'] . "</td>";
            echo "<td>" . $row['c_credit'] . "</td>";
            echo "<td>" . ($row['s_number'] ? $row['s_number'] : 'N/A') . "</td>";
            echo "<td>" . ($row['lecturer_name'] ? $row['lecturer_name'] : 'TBA') . "</td>";
            echo "<td>" . 
                ($row['s_maxstudent'] ? 
                    $row['current_students'] . '/' . $row['s_maxstudent'] . 
                    ($row['current_students'] >= $row['s_maxstudent'] ? ' <span class="text-danger">(Full)</span>' : '')
                    : 'N/A') . 
                "</td>";
            echo "</tr>";
        }
        
        if(mysqli_num_rows($result) == 0) {
            echo '<tr><td colspan="7" class="text-center">No courses found matching your search criteria.</td></tr>';
        }
        ?>
      </tbody>
    </table>

    <div class="mt-3 mb-5">
      <button type="submit" class="btn btn-primary">Register</button>
      <button type="reset" class="btn btn-warning">Clear form</button>
    </div>
  </form>

  <br><br><br><br><br><br>
</div>

<!-- Add this JavaScript before closing body tag -->
<script>
function confirmSubmission() {
    // Get all selected sections
    const selectedSections = document.querySelectorAll('input[type="radio"]:checked');
    if (selectedSections.length === 0) {
        alert('Please select at least one course section to register.');
        return false;
    }

    // Build confirmation message
    let message = 'Are you sure you want to register for the following courses?\n\n';
    selectedSections.forEach(radio => {
        const row = radio.closest('tr');
        const courseCode = row.cells[1].textContent;
        const courseName = row.cells[2].textContent;
        const section = row.cells[4].textContent;
        const lecturer = row.cells[5].textContent;
        message += `${courseCode} - ${courseName}\nSection: ${section}\nLecturer: ${lecturer}\n\n`;
    });

    return confirm(message);
}
</script>

</div>

<?php include 'footer.php';?>
