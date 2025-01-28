<?php
include ('crssession.php');
if (!session_id())
{
  session_start();
}

include 'headerstudent.php';
include 'dbconnect.php';

// Get user ID from session
$uic = $_SESSION['active_user'];

// Get user details from database
$sql = "SELECT * FROM tb_user WHERE u_sno = '$uic'";
$result = mysqli_query($con, $sql);
$user = mysqli_fetch_assoc($result);
?>

<div class="container">
  <!-- Add message display section -->
  <?php
  // Display success message if any
  if(isset($_SESSION['success'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
    echo $_SESSION['success'];
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
    unset($_SESSION['success']);
  }

  // Display error message if any
  if(isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
    echo $_SESSION['error'];
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
    unset($_SESSION['error']);
  }
  ?>

  <br><br>
  <h5>My Profile</h5>

  <div class="card mt-4">
    <div class="card-body">
      <div class="row mb-3">
        <div class="col-sm-3">
          <strong>Student ID:</strong>
        </div>
        <div class="col-sm-9">
          <?php echo htmlspecialchars($user['u_sno']); ?>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-sm-3">
          <strong>Name:</strong>
        </div>
        <div class="col-sm-9">
          <?php echo htmlspecialchars($user['u_name']); ?>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-sm-3">
          <strong>Contact Number:</strong>
        </div>
        <div class="col-sm-9">
          <?php echo htmlspecialchars($user['u_contact']); ?>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-sm-3">
          <strong>Email:</strong>
        </div>
        <div class="col-sm-9">
          <?php echo htmlspecialchars($user['u_email']); ?>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-sm-3">
          <strong>State:</strong>
        </div>
        <div class="col-sm-9">
          <?php echo htmlspecialchars($user['u_state']); ?>
        </div>
      </div>
    </div>
  </div>

  <br><br><br><br>
</div>
<!-- Add Edit Profile Button -->
<div class="container">
  <div class="text-center">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
      Edit Profile
    </button>
  </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="updateprofile.php" method="POST">
        <div class="modal-body">
          <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['u_name']); ?>" required>
          </div>
          <div class="mb-3">
            <label for="contact" class="form-label">Contact Number</label>
            <input type="text" class="form-control" id="contact" name="contact" value="<?php echo htmlspecialchars($user['u_contact']); ?>" required>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['u_email']); ?>" required>
          </div>
          <div class="mb-3">
            <label for="state" class="form-label">State</label>
            <select class="form-select" id="state" name="state" required>
              <?php
              $states = array("Johor", "Kedah", "Kelantan", "Melaka", "Negeri Sembilan", "Pahang", "Perak", "Perlis", "Pulau Pinang", "Sabah", "Sarawak", "Selangor", "Terengganu", "Kuala Lumpur", "Labuan", "Putrajaya");
              foreach($states as $state) {
                $selected = ($state == $user['u_state']) ? 'selected' : '';
                echo "<option value=\"$state\" $selected>$state</option>";
              }
              ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>


<?php include 'footer.php';?>
