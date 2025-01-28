<?php
include ('crssession.php');
if (!session_id()) {
    session_start();
}

include 'headerlec.php';
include 'dbconnect.php';

// Check if user is logged in
if (!isset($_SESSION['active_user'])) {
    header('Location: login.php');
    exit();
}

// Get lecturer ID from session
$lecturer_id = $_SESSION['active_user'];

// Get course code and semester from URL
$course_code = mysqli_real_escape_string($con, $_GET['code']);
$semester = mysqli_real_escape_string($con, $_GET['sem']);

// Get course details
$sql = "SELECT c.c_code, c.c_name, c.c_credit 
        FROM tb_course c 
        JOIN tb_section s ON c.c_code = s.s_course_code
        WHERE c.c_code = ? 
        AND s.s_lecturer = ?
        AND s.s_sem = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "sss", $course_code, $lecturer_id, $semester);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$course = mysqli_fetch_assoc($result);

// Get student list
$sql = "SELECT u.u_name, u.u_sno, rs.s_desc as status_desc
        FROM tb_registration r
        JOIN tb_user u ON r.r_student = u.u_sno
        JOIN tb_regstatus rs ON r.r_status = rs.s_id
        JOIN tb_section s ON r.r_section = s.s_id
        WHERE r.r_course = ?
        AND s.s_lecturer = ?
        AND r.r_sem = ?
        ORDER BY u.u_name";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "sss", $course_code, $lecturer_id, $semester);
mysqli_stmt_execute($stmt);
$students = mysqli_stmt_get_result($stmt);
?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="assignedcourse.php">Assigned Courses</a></li>
            <li class="breadcrumb-item active" aria-current="page">Course Details</li>
        </ol>
    </nav>

    <div class="card mb-4">
        <div class="card-header">
            <h4><?php echo htmlspecialchars($course['c_code']); ?> - <?php echo htmlspecialchars($course['c_name']); ?></h4>
            <p class="mb-0">Semester: <?php echo htmlspecialchars($semester); ?></p>
            <p class="mb-0">Credits: <?php echo htmlspecialchars($course['c_credit']); ?></p>
        </div>
        <div class="card-body">
            <h5>Enrolled Students</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while($student = mysqli_fetch_array($students)) {
                            echo "<tr>";
                            echo "<td><a href='student_details.php?id=" . urlencode($student['u_sno']) . "'>" . htmlspecialchars($student['u_sno']) . "</a></td>";
                            echo "<td>".htmlspecialchars($student['u_name'])."</td>";
                            echo "<td>".htmlspecialchars($student['status_desc'])."</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?> 