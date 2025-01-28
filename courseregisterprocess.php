<?php
include ('crssession.php');
if (!session_id())
{
  session_start();
}

include 'dbconnect.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';
include 'mail_config.php';

// Function to send registration email
function sendRegistrationEmail($student_email, $student_name, $course_code, $course_name, $semester) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;

        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($student_email, $student_name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "Course Registration Confirmation";
        
        // HTML email body
        $mail->Body = "
        <p>Dear {$student_name},</p>
        <p>Your course registration has been received for the following course:</p>
        <p><strong>Course:</strong> {$course_code} - {$course_name}<br>
        <strong>Semester:</strong> {$semester}</p>
        <p>Your registration is pending approval. You will be notified once it has been processed.</p>
        <p>Best regards,<br>Course Registration System</p>";

        // Plain text version for non-HTML mail clients
        $mail->AltBody = "Dear {$student_name},\n\n" .
                        "Your course registration has been received for the following course:\n" .
                        "Course: {$course_code} - {$course_name}\n" .
                        "Semester: {$semester}\n\n" .
                        "Your registration is pending approval. You will be notified once it has been processed.\n\n" .
                        "Best regards,\nCourse Registration System";

        return $mail->send();
    } catch (Exception $e) {
        error_log("Email sending failed: {$mail->ErrorInfo}");
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_SESSION['active_user'];
    $semester = $_POST['fsem'];
    $registration_errors = [];
    $has_successful_registration = false;

    // Get student information for email
    $student_sql = "SELECT u_name, u_email FROM tb_user WHERE u_sno = ?";
    $stmt = mysqli_prepare($con, $student_sql);
    mysqli_stmt_bind_param($stmt, "s", $student_id);
    mysqli_stmt_execute($stmt);
    $student_result = mysqli_stmt_get_result($stmt);
    $student_info = mysqli_fetch_assoc($student_result);

    // Check if any courses were selected
    if (!isset($_POST['fcourse']) || empty($_POST['fcourse'])) {
        $_SESSION['error'] = "Please select at least one course.";
        header('Location: courseregister.php');
        exit();
    }

    foreach ($_POST['fcourse'] as $course_code => $section_id) {
        // First, check if section has available space
        $sql = "SELECT 
                s.s_maxstudent,
                COUNT(DISTINCT CASE WHEN r.r_status IN (1, 2) THEN r.r_tid ELSE NULL END) as current_students,
                c.c_code,
                c.c_name
                FROM tb_section s
                LEFT JOIN tb_course c ON s.s_course_code = c.c_code
                LEFT JOIN tb_registration r ON s.s_id = r.r_section 
                    AND r.r_sem = s.s_sem 
                    AND r.r_status IN (1, 2)
                WHERE s.s_id = ?
                GROUP BY s.s_id";
        
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $section_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $section_info = mysqli_fetch_assoc($result);

        // Check if section is full
        if ($section_info['current_students'] >= $section_info['s_maxstudent']) {
            $registration_errors[] = "Section for course {$course_code} is full.";
            continue;
        }

        // Check if student is already registered for this course
        $sql = "SELECT r_tid FROM tb_registration 
                WHERE r_student = ? 
                AND r_course = ? 
                AND r_sem = ? 
                AND r_status IN (1, 2)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $student_id, $course_code, $semester);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $registration_errors[] = "You are already registered for course {$course_code}.";
            continue;
        }

        // Determine if registration should be auto-approved
        $status = ($section_info['current_students'] < $section_info['s_maxstudent']) ? 2 : 1; // 2 for approved, 1 for pending

        // Insert the registration with appropriate status
        $sql = "INSERT INTO tb_registration (r_student, r_course, r_section, r_sem, r_status) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ssisi", $student_id, $course_code, $section_id, $semester, $status);
        
        if (mysqli_stmt_execute($stmt)) {
            $has_successful_registration = true;
            
            // Customize email message based on status
            $status_message = ($status == 2) ? 
                "Your registration has been automatically approved as there is sufficient capacity." :
                "Your registration is pending approval. You will be notified once it has been processed.";
            
            // Send confirmation email with appropriate message
            $mail = new PHPMailer(true);

            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host       = SMTP_HOST;
                $mail->SMTPAuth   = true;
                $mail->Username   = SMTP_USERNAME;
                $mail->Password   = SMTP_PASSWORD;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = SMTP_PORT;

                // Recipients
                $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
                $mail->addAddress($student_info['u_email'], $student_info['u_name']);

                // Content
                $mail->isHTML(true);
                $mail->Subject = "Course Registration Status";
                
                // HTML email body
                $mail->Body = "
                <p>Dear {$student_info['u_name']},</p>
                <p>Your course registration details:</p>
                <p><strong>Course:</strong> {$course_code} - {$section_info['c_name']}<br>
                <strong>Semester:</strong> {$semester}</p>
                <p>{$status_message}</p>
                <p>Best regards,<br>Course Registration System</p>";

                $mail->AltBody = strip_tags(str_replace('<br>', "\n", $mail->Body));

                $mail->send();
            } catch (Exception $e) {
                error_log("Email sending failed: {$mail->ErrorInfo}");
            }
        } else {
            $registration_errors[] = "Error registering for course {$course_code}: " . mysqli_error($con);
        }
    }

    // Set appropriate message based on registration results
    if (!empty($registration_errors)) {
        $_SESSION['error'] = "Registration issues: (" . implode(", ", $registration_errors) . ")";
    }
    if ($has_successful_registration) {
        $_SESSION['success'] = "Successfully registered for selected course(s). Please check your email for confirmation.";
    }

    header('Location: courseregister.php');
    exit();
} else {
    header('Location: courseregister.php');
    exit();
}

mysqli_close($con);
?>