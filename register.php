<?php include 'headermain.php';?>

<div class="container">

  <br><br><h5>Please fill all following details</h5>
  <form method="POST" action="registerprocess.php" onsubmit="return validateForm()">
  <fieldset>
    <div>
      <label for="exampleInputEmail1" class="form-label mt-4">Please enter your staff or student ID <span class="text-danger">*</span></label>
      <input type="text" name="funame" class="form-control" id="idInput" aria-describedby="emailHelp" 
        placeholder="Enter your staff or student ID" required 
        oninvalid="this.setCustomValidity('Please enter your ID')" 
        oninput="this.setCustomValidity('')">
      <div id="idError" class="text-danger"></div>
    </div>

    <div>
      <label for="password" class="form-label mt-4">Create your password <span class="text-danger">*</span></label>
      <div class="input-group">
        <input type="password" name="fpwd" class="form-control" id="password" 
          placeholder="Create password" autocomplete="off" required
          oninvalid="this.setCustomValidity('Please enter a password')" 
          oninput="this.setCustomValidity('')">
        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
          <i class="bi bi-eye"></i>
        </button>
      </div>
      <small class="form-text text-muted">
        Password must contain:
        <ul>
          <li>At least 8-20 inputs</li>
          <li>At least one uppercase letter</li>
          <li>At least one lowercase letter</li>
          <li>At least one number</li>
        </ul>
      </small>
      <div id="passwordStrengthError" class="text-danger"></div>
    </div>

    <div>
      <label for="confirmPassword" class="form-label mt-4">Confirm your password <span class="text-danger">*</span></label>
      <div class="input-group">
        <input type="password" name="confirm_password" class="form-control" id="confirmPassword" 
          placeholder="Confirm password" autocomplete="off" required
          oninvalid="this.setCustomValidity('Please confirm your password')" 
          oninput="this.setCustomValidity('')">
        <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
          <i class="bi bi-eye"></i>
        </button>
      </div>
      <div id="passwordError" class="text-danger"></div>
    </div>

    <div>
      <label for="emailInput" class="form-label mt-4">Email <span class="text-danger">*</span></label>
      <input type="email" name="femail" class="form-control" id="emailInput" 
        placeholder="Enter email" required
        oninvalid="this.setCustomValidity('Please enter a valid email address')" 
        oninput="this.setCustomValidity('')">
      <div id="emailError" class="text-danger"></div>
    </div>

    <div>
      <label for="nameInput" class="form-label mt-4">Enter your full name <span class="text-danger">*</span></label>
      <input type="text" name="fname" class="form-control" id="nameInput" 
        placeholder="Enter your full name according to IC" required
        oninvalid="this.setCustomValidity('Please enter your full name')" 
        oninput="this.setCustomValidity('')">
    </div>

    <div>
      <label for="contactInput" class="form-label mt-4">Enter your contact number <span class="text-danger">*</span></label>
      <input type="text" name="fcontact" class="form-control" id="contactInput" 
        placeholder="Your mobile or fixed line number" required
        oninvalid="this.setCustomValidity('Please enter your contact number')" 
        oninput="this.setCustomValidity('')">
    </div>

    <div>
      <label for="stateSelect" class="form-label mt-4">Select your state <span class="text-danger">*</span></label>
      <select class="form-select" name="fstate" id="stateSelect" required
        oninvalid="this.setCustomValidity('Please select your state')" 
        oninput="this.setCustomValidity('')">
        <option value="">Select a state</option>
        <option value="Johor">Johor</option>
        <option value="Kedah">Kedah</option>
        <option value="Kelantan">Kelantan</option>
        <option value="Melaka">Melaka</option>
        <option value="Negeri Sembilan">Negeri Sembilan</option>
        <option value="Pahang">Pahang</option>
        <option value="Pulau Pinang">Pulau Pinang</option>
        <option value="Perak">Perak</option>
        <option value="Perlis">Perlis</option>
        <option value="Sabah">Sabah</option>
        <option value="Sarawak">Sarawak</option>
        <option value="Selangor">Selangor</option>
        <option value="Terengganu">Terengganu</option>
        <option value="W.P. Kuala Lumpur">W.P. Kuala Lumpur</option>
        <option value="W.P. Labuan">W.P. Labuan</option>
        <option value="W.P. Putrajaya">W.P. Putrajaya</option>
      </select>
    </div>

    <div class="mt-4">
      <p><span class="text-danger">*</span> indicates required field</p>
    </div>

    <div class="mt-4">
      <button type="submit" class="btn btn-primary">Submit</button>
      <button type="reset" class="btn btn-warning">Clear form</button>
    </div>

    <br><br><br><br><br><br>
  </fieldset>
</form>

</div>

<script>
// Password validation function
function validatePassword() {
    const password = document.getElementById('password').value.trim();
    const confirmPassword = document.getElementById('confirmPassword').value.trim();
    const errorElement = document.getElementById('passwordError');
    const strengthError = document.getElementById('passwordStrengthError');
    
    // Clear previous error messages
    errorElement.textContent = '';
    strengthError.textContent = '';
    
    // Password strength validation
    const minLength = 8;
    const maxLength = 20;
    
    let errors = [];
    
    // Only validate password requirements when there's input
    if (password.length > 0) {
        if (password.length < minLength || password.length > maxLength) {
            errors.push(`Password must be between ${minLength} and ${maxLength} inputs`);
        }
        if (!(/[A-Z]/.test(password))) {
            errors.push("Password must contain at least one uppercase letter");
        }
        if (!(/[a-z]/.test(password))) {
            errors.push("Password must contain at least one lowercase letter");
        }
        if (!(/[0-9]/.test(password))) {
            errors.push("Password must contain at least one number");
        }
        
        // Show strength errors if any
        if (errors.length > 0) {
            strengthError.innerHTML = errors.join('<br>');
            return false;
        }
    }
    
    // Only check password match if both fields have input
    if (password.length > 0 && confirmPassword.length > 0 && password !== confirmPassword) {
        errorElement.textContent = 'Passwords do not match!';
        return false;
    }
    
    // For form submission, ensure all validations pass and both fields are filled
    if (document.activeElement.type === 'submit') {
        if (password.length === 0 || confirmPassword.length === 0) {
            errorElement.textContent = 'Both password fields are required';
            return false;
        }
        if (errors.length > 0 || password !== confirmPassword) {
            return false;
        }
    }
    
    return true;
}

// Add real-time validation for both password fields
document.getElementById('password').addEventListener('input', validatePassword);
document.getElementById('confirmPassword').addEventListener('input', validatePassword);

// Make sure the form's onsubmit handler prevents submission when validation fails
document.querySelector('form').onsubmit = function(e) {
    const email = document.getElementById('emailInput').value.trim();
    
    if (!validateEmail(email)) {
        e.preventDefault();
        document.getElementById('emailError').textContent = 'Please enter a valid email address';
        return false;
    }
    
    if (!validatePassword()) {
        e.preventDefault();
        return false;
    }
    return true;
};

// Toggle password visibility for both fields
const togglePassword = document.querySelector('#togglePassword');
const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
const password = document.querySelector('#password');
const confirmPassword = document.querySelector('#confirmPassword');

togglePassword.addEventListener('click', function (e) {
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    this.querySelector('i').classList.toggle('bi-eye');
    this.querySelector('i').classList.toggle('bi-eye-slash');
});

toggleConfirmPassword.addEventListener('click', function (e) {
    const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
    confirmPassword.setAttribute('type', type);
    this.querySelector('i').classList.toggle('bi-eye');
    this.querySelector('i').classList.toggle('bi-eye-slash');
});

// Add this to your existing JavaScript
function validateEmail(email) {
    // Regular expression for email validation
    const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return emailRegex.test(email);
}

document.getElementById('emailInput').addEventListener('input', function() {
    const email = this.value.trim();
    const errorElement = document.getElementById('emailError');
    
    if (email.length > 0) {
        if (!validateEmail(email)) {
            errorElement.textContent = 'Please enter a valid email address';
            this.setCustomValidity('Invalid email format');
        } else {
            errorElement.textContent = '';
            this.setCustomValidity('');
        }
    } else {
        errorElement.textContent = '';
        this.setCustomValidity('');
    }
});

// Add this new function to validate the entire form
function validateForm() {
    const email = document.getElementById('emailInput').value.trim();
    const isPasswordValid = validatePassword();
    const isEmailValid = validateEmail(email);
    
    if (!isEmailValid) {
        document.getElementById('emailError').textContent = 'Please enter a valid email address';
        return false;
    }
    
    if (!isPasswordValid) {
        return false;
    }
    
    return true;
}
</script>

<?php include 'footer.php';?>
