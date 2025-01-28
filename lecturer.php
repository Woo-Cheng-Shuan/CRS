<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'crssession.php';
include 'headerlec.php';
include 'dbconnect.php';

// Check if user is logged in
if (!isset($_SESSION['active_user'])) {
    header('Location: login.php');
    exit();
}

// Get lecturer ID from session
$lecturer_id = $_SESSION['active_user'];

// Get lecturer information
$sql = "SELECT * FROM tb_user WHERE u_sno = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "s", $lecturer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$lecturer = mysqli_fetch_assoc($result);

if (!$lecturer) {
    // Handle case where lecturer data is not found
    header('Location: login.php');
    exit();
}

// Get total number of courses with debug output
$sql = "SELECT DISTINCT c.c_code, c.c_name 
        FROM tb_course c 
        JOIN tb_section s ON c.c_code = s.s_course_code 
        WHERE s.s_lecturer = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "s", $lecturer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Debug output
echo "<!-- Debug: Courses for Lecturer " . $lecturer_id . ": -->";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<!-- Debug: Course: " . $row['c_code'] . " - " . $row['c_name'] . " -->";
}

// Get total number of courses
$sql = "SELECT COUNT(DISTINCT s.s_course_code) as total_courses 
        FROM tb_section s 
        WHERE s.s_lecturer = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "s", $lecturer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$courses_count = mysqli_fetch_assoc($result)['total_courses'];

// Add debug output
echo "<!-- Debug: Combined courses for Lecturer " . $lecturer_id . " -->";

// Get total number of students across all courses
$sql = "SELECT COUNT(DISTINCT r.r_student) as total_students
        FROM tb_registration r
        JOIN tb_section s ON r.r_section = s.s_id
        WHERE s.s_lecturer = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "s", $lecturer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$students_count = mysqli_fetch_assoc($result)['total_students'];

// Get recent course registrations
$sql = "SELECT r.r_tid, u.u_name, c.c_code, c.c_name, rs.s_desc as status_desc, r.r_sem
        FROM tb_registration r
        JOIN tb_user u ON r.r_student = u.u_sno
        JOIN tb_section s ON r.r_section = s.s_id
        JOIN tb_course c ON s.s_course_code = c.c_code
        JOIN tb_regstatus rs ON r.r_status = rs.s_id
        WHERE s.s_lecturer = ?
        ORDER BY r.r_tid DESC
        LIMIT 5";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "s", $lecturer_id);
mysqli_stmt_execute($stmt);
$recent_registrations = mysqli_stmt_get_result($stmt);
?>

<div class="container mt-4">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Welcome, <?php echo htmlspecialchars($lecturer['u_name']); ?>!</h4>
                    <p class="card-text">Email: <?php echo htmlspecialchars($lecturer['u_email']); ?></p>
                    <p class="card-text">Contact: <?php echo htmlspecialchars($lecturer['u_contact']); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Courses</h5>
                    <h2 class="display-4"><?php echo $courses_count; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Students</h5>
                    <h2 class="display-4"><?php echo $students_count; ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Single View Details Button -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <a href="assignedcourse.php" class="btn btn-primary btn-lg">View Course Details</a>
        </div>
    </div>
</div>

<?php include 'footer.php';?>
