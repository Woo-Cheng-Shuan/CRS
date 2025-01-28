<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'headermain.php';
?>

<div class="container">

  <br><br><h5>Please fill to login</h5>
  
  <?php
  // Display error message if it exists
  if(isset($_SESSION['error'])) {
      echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
      unset($_SESSION['error']);
  }
  
  // Display success message if it exists
  if(isset($_SESSION['success'])) {
      echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
      unset($_SESSION['success']);
  }
  ?>

  <form method="POST" action="loginprocess.php">
  <fieldset>
    <div>
      <label for="fusername" class="form-label mt-4">Enter staff or student ID <span class="text-danger">*</span></label>
      <input type="text" name="fusername" class="form-control" id="fusername" placeholder="Enter your staff or student ID" required>
    </div>

    <div>
      <label for="fpassword" class="form-label mt-4">Enter password <span class="text-danger">*</span></label>
      <div class="input-group">
        <input type="password" name="fpassword" class="form-control" id="fpassword" placeholder="Enter your password" autocomplete="off" required>
        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
          <i class="bi bi-eye"></i>
        </button>
      </div>
    </div>

    
    <br><br>
    <button type="submit" class="btn btn-primary">Login</button>

    <br><br><br><br><br><br>
  </fieldset>
</form>

</div>

<!-- Add this JavaScript before closing body tag -->
<script>
const togglePassword = document.querySelector('#togglePassword');
const password = document.querySelector('#fpassword');

togglePassword.addEventListener('click', function (e) {
    // Toggle the type attribute
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    
    // Toggle the icon
    this.querySelector('i').classList.toggle('bi-eye');
    this.querySelector('i').classList.toggle('bi-eye-slash');
});
</script>

<?php include 'footer.php';?>
