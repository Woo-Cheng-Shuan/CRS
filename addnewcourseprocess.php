<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('dbconnect.php');

// Debug logging
error_log("POST data received: " . print_r($_POST, true));

// Get form data and sanitize
$coursecode = trim(mysqli_real_escape_string($con, $_POST['fcode']));
$coursename = trim(mysqli_real_escape_string($con, $_POST['fname']));
$credit = intval($_POST['fcredit']);
$semester = trim(mysqli_real_escape_string($con, $_POST['fsem']));
$section_numbers = $_POST['section_number'];
$section_capacities = $_POST['section_capacity'];
$section_lecturers = $_POST['section_lecturer'];

// Debug log the arrays
error_log("Section Numbers: " . print_r($section_numbers, true));
error_log("Section Capacities: " . print_r($section_capacities, true));
error_log("Section Lecturers: " . print_r($section_lecturers, true));

// Validate data
if (empty($coursecode) || empty($coursename) || empty($credit) || empty($semester)) {
    error_log("Validation failed: Missing required fields");
    $_SESSION['error'] = "All fields are required";
    header('Location: addnewcourse.php');
    exit();
}

// Start transaction
mysqli_begin_transaction($con);

try {
    // First check if course already exists
    $check_sql = "SELECT c_code FROM tb_course WHERE c_code = ?";
    $check_stmt = mysqli_prepare($con, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "s", $coursecode);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($result) > 0) {
        throw new Exception("Course code already exists");
    }

    // Calculate total capacity from sections
    $total_capacity = array_sum($section_capacities);

    // Insert into course table
    $sql = "INSERT INTO tb_course (c_code, c_name, c_credit, c_sem) 
            VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    if ($stmt === false) {
        throw new Exception("Error preparing course insert: " . mysqli_error($con));
    }
    
    mysqli_stmt_bind_param($stmt, "ssis", 
        $coursecode, 
        $coursename, 
        $credit, 
        $semester
    );
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Error inserting course: " . mysqli_stmt_error($stmt));
    }

    // Insert sections
    for($i = 0; $i < count($section_numbers); $i++) {
        $section_sql = "INSERT INTO tb_section (s_course_code, s_number, s_maxstudent, s_sem, s_lecturer) 
                       VALUES (?, ?, ?, ?, ?)";
        $section_stmt = mysqli_prepare($con, $section_sql);
        if ($section_stmt === false) {
            throw new Exception("Error preparing section insert: " . mysqli_error($con));
        }
        
        // Convert section capacity to integer
        $capacity = intval($section_capacities[$i]);
        
        error_log("Inserting section: " . $i);
        error_log("Course code: " . $coursecode);
        error_log("Section number: " . $section_numbers[$i]);
        error_log("Capacity: " . $capacity);
        error_log("Semester: " . $semester);
        error_log("Lecturer: " . $section_lecturers[$i]);
        
        mysqli_stmt_bind_param($section_stmt, "ssiss", 
            $coursecode,           // string (s)
            $section_numbers[$i],  // string (s)
            $capacity,             // integer (i)
            $semester,             // string (s)
            $section_lecturers[$i] // string (s)
        );
        
        if (!mysqli_stmt_execute($section_stmt)) {
            throw new Exception("Error inserting section " . ($i + 1) . ": " . mysqli_stmt_error($section_stmt));
        }
        mysqli_stmt_close($section_stmt);
    }

    mysqli_commit($con);
    error_log("Course added successfully: " . $coursecode);
    $_SESSION['success'] = "Course and sections added successfully!";
    header("Location: addnewcourse.php?status=success");
    exit();
    
} catch (Exception $e) {
    error_log("Error adding course: " . $e->getMessage());
    mysqli_rollback($con);
    $_SESSION['error'] = "Error: " . $e->getMessage();
    header('Location: addnewcourse.php');
    exit();
} finally {
    if (isset($stmt)) {
        mysqli_stmt_close($stmt);
    }
    if (isset($check_stmt)) {
        mysqli_stmt_close($check_stmt);
    }
    mysqli_close($con);
}
?>