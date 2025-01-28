<?php
include ('crssession.php');
if (!session_id()) {
    session_start();
}

include 'headerlec.php';
include 'dbconnect.php';

// Get lecturer ID from session - updated to use correct session variable
$lecturer_id = $_SESSION['u_sno']; // Changed from 'funame' to 'u_sno'

// Get student ID from URL
$student_id = mysqli_real_escape_string($con, $_GET['id']);

// Get student details
$sql = "SELECT u.u_sno, u.u_name, u.u_email, u.u_contact, u.u_state, u.u_utype 
        FROM tb_user u
        WHERE u.u_sno = '$student_id'";
$result = mysqli_query($con, $sql);
$student = mysqli_fetch_assoc($result);

// Get student's enrolled courses - updated query to use section table
$sql = "SELECT c.c_code, c.c_name, r.r_sem, rs.s_desc as status_desc
        FROM tb_registration r
        JOIN tb_course c ON r.r_course = c.c_code
        JOIN tb_regstatus rs ON r.r_status = rs.s_id
        JOIN tb_section s ON r.r_section = s.s_id
        WHERE r.r_student = '$student_id'
        AND s.s_lecturer = '$lecturer_id'
        ORDER BY r.r_sem DESC";
$courses = mysqli_query($con, $sql);
?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="assignedcourse.php">Assigned Courses</a></li>
            <li class="breadcrumb-item"><a href="javascript:history.back()">Course Details</a></li>
            <li class="breadcrumb-item active" aria-current="page">Student Details</li>
        </ol>
    </nav>

    <div class="card mb-4">
        <div class="card-header">
            <h4>Student Information</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Student ID:</strong> <?php echo htmlspecialchars($student['u_sno']); ?></p>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($student['u_name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($student['u_email']); ?></p>
                    <p><strong>Contact:</strong> <?php echo htmlspecialchars($student['u_contact']); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>State:</strong> <?php echo htmlspecialchars($student['u_state']); ?></p>
                    <p><strong>User Type:</strong> <?php echo ($student['u_utype'] == 2 ? 'Student' : ''); ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h4>Enrolled Courses (Your Courses)</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Semester</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while($course = mysqli_fetch_array($courses)) {
                            echo "<tr>";
                            echo "<td>".htmlspecialchars($course['c_code'])."</td>";
                            echo "<td>".htmlspecialchars($course['c_name'])."</td>";
                            echo "<td>".htmlspecialchars($course['r_sem'])."</td>";
                            echo "<td>".htmlspecialchars($course['status_desc'])."</td>";
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