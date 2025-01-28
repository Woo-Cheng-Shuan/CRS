<?php
include('dbconnect.php');

if(isset($_GET['tid']) && isset($_GET['status'])) {
    $tid = $_GET['tid'];
    $status = $_GET['status'];
    
    // Update the registration status
    $sql = "UPDATE tb_registration 
            SET r_status = '$status' 
            WHERE r_tid = '$tid'";
    
    $result = mysqli_query($con, $sql);
    
    if($result) {
        echo "<script>
                alert('Registration status has been updated successfully!');
                window.location.href='courseapprovalprocess.php?id=" . $tid . "';
              </script>";

        // After successful status update to Approved (2)
        if(isset($_GET['email']) && $_GET['email'] == 1 && $_GET['status'] == 2) {
            // Fetch the registration details
            $sql = "SELECT r.*, u.u_name, u.u_email, c.c_name 
                    FROM tb_registration r
                    LEFT JOIN tb_user u ON r.r_student = u.u_sno
                    LEFT JOIN tb_course c ON r.r_course = c.c_code
                    WHERE r_tid = '$tid'";
            
            $result = mysqli_query($con, $sql);
            $row = mysqli_fetch_array($result);

            // Send email notification
            $to = $row['u_email'];
            $subject = "Course Registration Approved";
            $message = "
            <html>
            <head>
                <title>Course Registration Status</title>
            </head>
            <body>
                <h2>Course Registration Approved</h2>
                <p>Dear {$row['u_name']},</p>
                <p>Your course registration has been approved for the following course:</p>
                <table style='border-collapse: collapse; width: 100%;'>
                    <tr>
                        <td style='padding: 8px; border: 1px solid #ddd;'><strong>Course Code:</strong></td>
                        <td style='padding: 8px; border: 1px solid #ddd;'>{$row['r_course']}</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px; border: 1px solid #ddd;'><strong>Course Name:</strong></td>
                        <td style='padding: 8px; border: 1px solid #ddd;'>{$row['c_name']}</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px; border: 1px solid #ddd;'><strong>Semester:</strong></td>
                        <td style='padding: 8px; border: 1px solid #ddd;'>{$row['r_sem']}</td>
                    </tr>
                </table>
                <p>Best regards,<br>Course Registration System</p>
            </body>
            </html>
            ";

            // Headers for HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: Course Registration System <noreply@yourdomain.com>' . "\r\n";

            // Send email
            mail($to, $subject, $message, $headers);
        }
    } else {
        echo "<script>
                alert('Error updating registration status!');
                window.location.href='courseapprovalprocess.php?id=" . $tid . "';
              </script>";
    }
} else {
    header('Location: index.php');
}

mysqli_close($con);
?> 