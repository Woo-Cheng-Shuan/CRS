<?php
include ('crssession.php');
if (!session_id())
{
  session_start();
}

include 'headeradvisor.php';?>

<div class="container">
  <div class="row mt-4">
    <div class="col">
      <?php
      // Check for success message
      if(isset($_GET['status']) && $_GET['status'] == 'success') {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                Course has been successfully added!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
      }
      ?>
      <h3>Course List</h3>
      <table class="table table-striped">
        <thead>
          <tr>
            <th style="width: 10%">Semester</th>
            <th style="width: 10%">Course Code</th>
            <th style="width: 20%">Course Name</th>
            <th style="width: 5%">Credit</th>
            <th style="width: 5%">Sections</th>
            <th style="width: 20%">Lecturer</th>
            <th style="width: 10%">Students Capacity</th>
            <th style="width: 10%">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Connect to database
          include('dbconnect.php');
          
          // Get course list with sections and current student count
          $sql = "SELECT 
                    c.*,
                    s.s_number as section,
                    s.s_maxstudent as capacity,
                    u.u_name as lecturer_name,
                    (SELECT COUNT(*) 
                     FROM tb_registration r 
                     WHERE r.r_course = c.c_code 
                     AND r.r_section = s.s_id
                     AND r.r_status = 2) as current_students
                  FROM tb_course c 
                  LEFT JOIN tb_section s ON c.c_code = s.s_course_code
                  LEFT JOIN tb_user u ON s.s_lecturer = u.u_sno 
                  ORDER BY c.c_code, s.s_number";
          $result = mysqli_query($con, $sql);
          
          while($row = mysqli_fetch_array($result)) {
            echo "<tr>";
            echo "<td>".$row['c_sem']."</td>";
            echo "<td>".$row['c_code']."</td>";
            echo "<td>".$row['c_name']."</td>";
            echo "<td>".$row['c_credit']."</td>";
            echo "<td>".$row['section']."</td>";
            echo "<td>".$row['lecturer_name']."</td>";
            echo "<td>".$row['current_students']."/".$row['capacity']."</td>";
            echo "<td>
                    <a href='editcourse.php?code=".$row['c_code']."' class='btn btn-warning btn-sm'>Edit</a>
                    <a href='deletecourse.php?code=".$row['c_code']."' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this course?\")'>Delete</a>
                  </td>";
            echo "</tr>";
          }
          ?>
        </tbody>
      </table>

      <div class="mt-3">
        <a href="addnewcourse.php" class="btn btn-primary">Add New Course</a>
      </div>
    </div>
  </div>
</div>

<br><br><br><br><br><br>

<?php include 'footer.php';?>
