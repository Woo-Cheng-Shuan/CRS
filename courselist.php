<?php
include ('crssession.php');
if (!session_id())
{
  session_start();
}

include 'headeradvisor.php';
include 'dbconnect.php';

//Retrieve all registered courses
$sql = "SELECT * FROM tb_registration
        LEFT JOIN tb_user ON tb_registration.r_student = tb_user.u_sno
        LEFT JOIN tb_course ON tb_registration.r_course = tb_course.c_code
        LEFT JOIN tb_regstatus ON tb_registration.r_status = tb_regstatus.s_id
        WHERE r_status != '1'";

//Execute SQL statement on DB
$result = mysqli_query($con, $sql);
?>

<div class="container">

<table class="table table-striped table-hover">
  <thead>
    <tr>
      <th scope="col">Transaction ID</th>
      <th scope="col">Semester</th>
      <th scope="col">Student ID</th>
      <th scope="col">Student Name</th>      
      <th scope="col">Course</th>
      <th scope="col">Course Name</th>
      <th scope="col">Status</th>
      <th scope="col">Operation</th>

    </tr>
  </thead>


  <tbody>
    <?php
    while($row = mysqli_fetch_array($result)){
    echo"<tr>";
      echo"<td>".$row['r_tid']."</td>";
      echo"<td>".$row['r_sem']."</td>";
      echo"<td>".$row['u_sno']."</td>";
      echo"<td>".$row['u_name']."</td>";
      echo"<td>".$row['r_course']."</td>";
      echo"<td>".$row['c_name']."</td>";    
      echo"<td>".$row['s_desc']."</td>";
      echo"<td>";
      echo"<a href = 'coursemodifyprocess.php?id=".$row['r_tid']."' class = 'btn btn-warning'>Modify</a>";
      echo"</td>";
    echo"</tr>";
  }
  ?>
  </tbody>

</table>
<br><br><br><br>
</div>

<?php include 'footer.php';?>
