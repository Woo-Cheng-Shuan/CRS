<?php
include ('crssession.php');
// Check if a session is not already active before starting it
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_log("Advisor page - Session data: " . print_r($_SESSION, true));

// Check if user is logged in and is an advisor
if (!isset($_SESSION['u_sno']) || $_SESSION['u_utype'] != 3) {
    $_SESSION['error'] = "Unauthorized access";
    header('Location: login.php');
    exit();
}

include 'headeradvisor.php';?>

<?php
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
?>

<div class="container mt-4">
    <h2 class="mb-4">Admin Dashboard</h2>
    
    <div class="row">
        <!-- Course Management Card -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Course Management</h5>
                    <p class="card-text">Add new courses and manage existing courses in the system.</p>
                    <div class="d-grid gap-2">
                        <a href="addnewcourse.php" class="btn btn-primary mb-2">Add New Course</a>
                        <a href="modifycourselist.php" class="btn btn-outline-primary">View / Modify Course List</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Registration Management Card -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Registration Management</h5>
                    <p class="card-text">Review and manage student course registrations.</p>
                    <div class="d-grid gap-2">
                        <a href="courseapproval.php" class="btn btn-success mb-2">To Be Approved Registrations</a>
                        <a href="courselist.php" class="btn btn-outline-success">View Registered List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Quick Statistics</h5>
                    <div class="row">
                        <?php
                        include 'dbconnect.php';
                        
                        // Get pending registrations count
                        $sql_pending = "SELECT COUNT(*) as count FROM tb_registration WHERE r_status = 1";
                        $result_pending = mysqli_query($con, $sql_pending);
                        $pending_count = mysqli_fetch_array($result_pending)['count'];

                        // Get total courses count
                        $sql_courses = "SELECT COUNT(*) as count FROM tb_course";
                        $result_courses = mysqli_query($con, $sql_courses);
                        $courses_count = mysqli_fetch_array($result_courses)['count'];

                        // Get total approved registrations
                        $sql_approved = "SELECT COUNT(*) as count FROM tb_registration WHERE r_status = 2";
                        $result_approved = mysqli_query($con, $sql_approved);
                        $approved_count = mysqli_fetch_array($result_approved)['count'];

                        // Get total students count
                        $sql_students = "SELECT COUNT(*) as count FROM tb_user WHERE u_utype = 2";
                        $result_students = mysqli_query($con, $sql_students);
                        $students_count = mysqli_fetch_array($result_students)['count'];

                        mysqli_close($con);
                        ?>
                        
                        <div class="col-md-3 text-center">
                            <h6>Pending Registrations</h6>
                            <p class="h3 text-warning"><?php echo $pending_count; ?></p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h6>Approved Registrations</h6>
                            <p class="h3 text-success"><?php echo $approved_count; ?></p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h6>Total Courses</h6>
                            <p class="h3 text-primary"><?php echo $courses_count; ?></p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h6>Total Students</h6>
                            <p class="h3 text-info"><?php echo $students_count; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    transition: transform 0.2s;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

.card-title {
    color: #2c3e50;
    font-weight: bold;
}

.card-text {
    color: #7f8c8d;
}

.btn {
    width: 100%;
    margin-top: 10px;
}

.h3 {
    margin-bottom: 0;
    font-weight: bold;
}
</style>

<br><br><br><br><br><br>

<?php include 'footer.php';?>
