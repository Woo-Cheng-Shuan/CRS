<?php
// Check if a session is not already active before starting it
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if(!isset($_SESSION['active_user'])) {
    header('Location: login.php');
    exit();
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Check if session is valid
include('dbconnect.php');
$funame = $_SESSION['active_user'];
$session_id = session_id();

$sql = "SELECT * FROM tb_user WHERE u_sno = '$funame' AND session_id = '$session_id'";
$result = mysqli_query($con, $sql);

if(mysqli_num_rows($result) == 0) {
    // Invalid session, force logout
    session_destroy();
    header('Location: login.php');
    exit();
}

include 'headerstudent.php';

// Get user info
$uic = $_SESSION['active_user'];
$sql = "SELECT u_name FROM tb_user WHERE u_sno = '$uic'";
$result = mysqli_query($con, $sql);

// Add error checking
if (!$result) {
    die("Query failed: " . mysqli_error($con));
}

$user = mysqli_fetch_assoc($result);
if (!$user) {
    // If no user found, redirect to login
    session_destroy();
    header('Location: login.php');
    exit();
}

// Debug output
echo "<!-- Debug: User ID: " . $uic . " -->";
echo "<!-- Debug: SQL Query: " . $sql . " -->";
?>

<div class="container">
  <div class="row mt-4">
    <div class="col">
      <h3>Welcome, <?php echo htmlspecialchars($user['u_name']); ?>!</h3>
    </div>
  </div>

  <div class="row mt-4">
    <!-- Course Registration Card -->
    <div class="col-md-4 mb-4">
      <div class="card h-100">
        <div class="card-body text-center">
          <i class="fas fa-book fa-3x mb-3 text-success"></i>
          <h5 class="card-title">Course Registration</h5>
          <p class="card-text">Register for new courses this semester</p>
          <a href="courseregister.php" class="btn btn-success">Register Courses</a>
        </div>
      </div>
    </div>

    <!-- View Registered Courses Card -->
    <div class="col-md-4 mb-4">
      <div class="card h-100">
        <div class="card-body text-center">
          <i class="fas fa-list-alt fa-3x mb-3 text-info"></i>
          <h5 class="card-title">My Courses</h5>
          <p class="card-text">View and manage your registered courses</p>
          <a href="courseview.php" class="btn btn-info">View Courses</a>
        </div>
      </div>
    </div>

    <!-- Profile Card -->
    <div class="col-md-4 mb-4">
      <div class="card h-100">
        <div class="card-body text-center">
          <i class="fas fa-user-circle fa-3x mb-3 text-primary"></i>
          <h5 class="card-title">My Profile</h5>
          <p class="card-text">View and update your personal information</p>
          <a href="profile.php" class="btn btn-primary">Go to Profile</a>
        </div>
      </div>
    </div>
  </div>

  <div class="row mt-4">
    <!-- Registration Status -->
    <div class="col-md-6 mb-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Recent Course Registrations</h5>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Course</th>
                  <th>Semester</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // Get recent registrations
                $sql = "SELECT r.*, c.c_name, s.s_desc 
                        FROM tb_registration r
                        JOIN tb_course c ON r.r_course = c.c_code
                        JOIN tb_regstatus s ON r.r_status = s.s_id
                        WHERE r.r_student = '$uic'
                        ORDER BY r.r_tid DESC LIMIT 5";
                $result = mysqli_query($con, $sql);
                
                while($row = mysqli_fetch_array($result)) {
                  echo "<tr>";
                  echo "<td>".$row['c_name']."</td>";
                  echo "<td>".$row['r_sem']."</td>";
                  echo "<td><span class='badge bg-".
                       ($row['r_status'] == 1 ? 'warning' : 
                        ($row['r_status'] == 2 ? 'success' : 'danger'))."'>".
                       $row['s_desc']."</span></td>";
                  echo "</tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Active Semester Info -->
    <div class="col-md-6 mb-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Current Semester Information</h5>
          <?php
          // Get active semester
          $sql = "SELECT active_semester FROM tb_settings ORDER BY id DESC LIMIT 1";
          $result = mysqli_query($con, $sql);
          $activeSemester = mysqli_fetch_array($result)['active_semester'];
          
          // Get course count for active semester
          $sql = "SELECT COUNT(*) as course_count 
                 FROM tb_registration 
                 WHERE r_student = '$uic' AND r_sem = '$activeSemester'";
          $result = mysqli_query($con, $sql);
          $courseCount = mysqli_fetch_array($result)['course_count'];
          ?>
          <p><strong>Active Semester:</strong> <?php echo $activeSemester; ?></p>
          <p><strong>Registered Courses:</strong> <?php echo $courseCount; ?></p>
          <a href="courseregister.php" class="btn btn-outline-primary">Register More Courses</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php';?>
