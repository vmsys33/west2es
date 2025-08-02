<?php
// Start session at the very beginning before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="assets/images/logo1.png">
  <title>Cadiz West 2 Elementary School</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

   <style>
    /* Custom styles */
    * { 
      margin: 0;
      padding: 0;
      box-sizing: border-box; 
    }

    .header {
      text-align: center; 
    } 

    .banner {
      position: relative; 
      overflow-x: hidden; /* Hide horizontal overflow to prevent white space */
      width: 100%; /* Full width */
      height: 100vh; /* Full viewport height */
    }

    .banner img {
      width: 100%; /* Full width */
      height: 100%; /* Full height */
      object-fit: cover; /* Maintain aspect ratio and cover area */
      position: absolute;
      top: 0;
      left: 0;
    }

    .banner-content { 
      color: white; 
      position: absolute; 
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%); /* Vertically and horizontally center the content */
      display: flex;
      flex-direction: column;
      align-items: center;
      width: 100%; 
    }

    #loginBtn { 
      background-color: rgba(0, 123, 255, 0.8); 
      border: none;
      padding: 12px 25px;
      font-size: 18px;
      border-radius: 5px; 
      cursor: pointer; 
      position: absolute;
      left: 50%; /* Center horizontally */
      top: 102px; /* Move the button down */
      transform: translateX(-50%); /* Keep it horizontally centered */
      }

    /* Media query for desktop */
    @media (min-width: 768px) { 
      .banner-content {
        top: 45%;  
      }
    }

    /* Media query for mobile */
    @media (max-width: 767px) { 
      .banner-content {
        top: 40%;  
      }
    }

    /* Modal Styles */
    .modal-content {
      background-color: white;
      border-radius: 10px; 
      padding: 20px;
    }

    .modal-header {
      border-bottom: none; 
    }

    .modal-title {
      color: black; 
      text-align: center; 
      margin-bottom: 20px;
    }

    .modal-body .btn {
      display: block; 
      width: 100%;
      margin-bottom: 10px;
      background-color: white; 
      color: black; 
      border: none;
      padding: 15px;
      font-size: 16px;
      border-radius: 5px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
      transition: transform 0.2s; 
    }

    .modal-body .btn:hover {
      transform: translateY(-2px); 
    }

    /* Form Styles */
    .form-label {
      color: black;
      font-weight: bold;
    }

    .form-control {
      border: none;
      border-radius: 5px;
      padding: 10px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Registration Form Specific Styles */
    #registerModal .modal-body {
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    #registerModal .form-control {
      margin-bottom: 15px;
    }

    #registerModal .col-md-6 {
      margin-bottom: 15px;
    }
    
    /* Adjust button size on mobile */
    @media (max-width: 767px) {
        #loginBtn {
          padding: 15px 30px; /* Increase padding */
          font-size: 20px; /* Increase font size */
           position: absolute;
          left: 50%;  /* Center horizontally */
          top: 110px; /* Move the button down more */
          transform: translateX(-50%) translateY(20px); /* Fine-tune Y position */
              }
      }

    body {
      background: linear-gradient(135deg, #e0e7ff 0%, #f8fafc 100%);
      font-family: 'Poppins', 'Segoe UI', Arial, sans-serif;
    }
    #myModal .modal-content {
      border-radius: 18px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.12);
      border: none;
      padding-bottom: 0;
    }
    #myModal .modal-header {
      border-top-left-radius: 18px;
      border-top-right-radius: 18px;
      background: linear-gradient(90deg, #2563eb 0%, #1e40af 100%);
      position: relative;
    }
    #myModal .modal-title {
      font-family: 'Poppins', 'Segoe UI', Arial, sans-serif;
      font-size: 2rem;
      font-weight: 600;
      letter-spacing: 0.5px;
    }
    #myModal .modal-header img {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      margin-right: 12px;
      background: #fff;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    #myModal .modal-body {
      padding: 2.5rem 2rem 1.5rem 2rem;
    }
    #myModal .modal-footer {
      border-bottom-left-radius: 18px;
      border-bottom-right-radius: 18px;
      background: #f1f5f9;
      padding-top: 0.5rem;
      padding-bottom: 1rem;
    }
    #myModal .btn-custom {
      border-radius: 12px;
      font-size: 1.1rem;
      font-weight: 500;
      padding: 0.85rem 0;
      margin-bottom: 0.5rem;
      transition: background 0.2s, color 0.2s, box-shadow 0.2s;
      box-shadow: 0 2px 8px rgba(30,64,175,0.04);
    }
    #myModal .btn-custom:hover {
      background: #2563eb;
      color: #fff;
      box-shadow: 0 4px 16px rgba(30,64,175,0.10);
    }
    #myModal .btn-close {
      filter: invert(1);
      opacity: 0.7;
      transition: opacity 0.2s;
    }
    #myModal .btn-close:hover {
      opacity: 1;
    }


  </style>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">


</head>

<body>

  <div class="header banner">
    <img src="assets/images/west2.png" alt="School Image"> 
    <div class="banner-content"> 
      <button id="loginBtn" class="btn btn-success">Menu</button>
      <span id="bannerClose" style="position:absolute;top:20px;right:30px;cursor:pointer;font-size:2rem;z-index:2;color:#fff;" title="Close banner">&times;</span>
    </div>
  </div>


  



<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <!-- Modal Header -->
            <div class="modal-header w-100 d-flex align-items-center justify-content-center" style="background: none; border-bottom: none;">
                <img src="assets/images/logo1.png" alt="Logo" style="width:48px; height:48px; border-radius:50%; margin-right:12px; background:#fff; box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                <h5 class="modal-title mb-0" id="myModalLabel" style="font-family:'Poppins','Segoe UI',Arial,sans-serif;font-size:2rem;font-weight:600;letter-spacing:0.5px;color:#222;">West II Elementary School</h5>
                <button type="button" class="btn-close position-absolute end-0 me-3 mt-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Modal Body -->
            <div class="modal-body p-4">
                <p class="text-center mb-4" style="font-size:1.1rem;">Please choose how you’d like to proceed. Select one of the options below:</p>
                <!-- Action Buttons -->
                <div class="row g-3">
                    <div class="col-12">
                        <button class="btn btn-custom btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#adminLoginModal">
                            <i class="bi bi-person-badge-fill me-2"></i> Admin Login
                        </button>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-custom btn-outline-secondary w-100" data-bs-toggle="modal" data-bs-target="#facultyLoginModal">
                            <i class="bi bi-person-fill me-2"></i> Faculty Login
                        </button>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-custom btn-outline-info w-100 text-dark" data-bs-toggle="modal" data-bs-target="#registerModal">
                            <i class="bi bi-pencil-square me-2"></i> Register
                        </button>
                    </div>
                </div>
            </div>
            <!-- Modal Footer -->
            <div class="modal-footer justify-content-center border-top-0">
                <small class="text-muted">For assistance, contact the school administrator.</small>
            </div>
        </div>
    </div>
</div>




<?php include 'modals/admin_login_modal.php'; ?>


<?php include 'modals/faculty_login_modal.php'; ?>

  
<?php include 'modals/registration_modal.php'; ?>








  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz"
    crossorigin="anonymous"></script>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


  <script>

    

    // Get the modals
    var myModal = new bootstrap.Modal(document.getElementById('myModal'));
    var adminLoginModal = new bootstrap.Modal(document.getElementById('adminLoginModal'));
    var facultyLoginModal = new bootstrap.Modal(document.getElementById('facultyLoginModal'));
    var registerModal = new bootstrap.Modal(document.getElementById('registerModal'));

    // Get the button that opens the main modal
    var btn = document.getElementById("loginBtn");

    // When the user clicks the button, open the main modal 
    btn.onclick = function () {
      myModal.show();
    }

    // Function to show password reset form
    function showResetForm(userType = null) {
      // Auto-detect user type if not provided
      if (!userType) {
        // Check which modal is currently open or was last active
        const adminModal = document.getElementById('adminLoginModal');
        const facultyModal = document.getElementById('facultyLoginModal');
        
        // Check if admin modal is visible
        if (adminModal && adminModal.classList.contains('show')) {
          userType = 'admin';
        }
        // Check if faculty modal is visible
        else if (facultyModal && facultyModal.classList.contains('show')) {
          userType = 'faculty';
        }
        // If neither is visible, try to detect from the event target
        else {
          // Get the element that triggered this function
          const activeElement = document.activeElement;
          if (activeElement && activeElement.closest('#adminLoginModal')) {
            userType = 'admin';
          } else if (activeElement && activeElement.closest('#facultyLoginModal')) {
            userType = 'faculty';
          } else {
            // Fallback: check if any modal is in the DOM and visible
            const visibleModals = document.querySelectorAll('.modal.show');
            for (let modal of visibleModals) {
              if (modal.id === 'adminLoginModal') {
                userType = 'admin';
                break;
              } else if (modal.id === 'facultyLoginModal') {
                userType = 'faculty';
                break;
              }
            }
          }
        }
        
        // If still no userType detected, default to faculty
        if (!userType) {
          userType = 'faculty';
        }
      }
      
      // Show a simple Bootstrap modal
      const modalHtml = `
        <div class="modal fade" id="resetFormModal" tabindex="-1" aria-labelledby="resetFormModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="resetFormModalLabel">Request Password Reset - ${userType.charAt(0).toUpperCase() + userType.slice(1)}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form id="resetForm">
                  <div class="mb-3">
                    <label for="resetEmail" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="resetEmail" name="email" placeholder="yourname@deped.gov.ph" required>
                    <div class="form-text" id="emailHelp">
                      ${userType === 'admin' ? 'Enter your admin email address' : 'Enter your faculty email address'}
                    </div>
                    <div class="invalid-feedback" id="emailError">
                      Please enter a valid email address.
                    </div>
                  </div>
                  <input type="hidden" name="request_reset" value="1">
                  <input type="hidden" name="user_type" value="${userType}">
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="submitResetBtn" class="btn btn-primary">Send Reset Link</button>
              </div>
            </div>
          </div>
        </div>
      `;
      
      // Remove existing modal if any
      const existingModal = document.getElementById('resetFormModal');
      if (existingModal) {
        existingModal.remove();
      }
      
      // Add modal to page
      document.body.insertAdjacentHTML('beforeend', modalHtml);
      
      // Show the modal
      const modal = new bootstrap.Modal(document.getElementById('resetFormModal'));
      modal.show();
      
      // Focus on email input
      setTimeout(() => {
        document.getElementById('resetEmail').focus();
      }, 500);
      
      // Enhanced email validation function
      function validateEmailForUserType(email, userType) {
        // Basic email format validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
          return { valid: false, message: 'Please enter a valid email address.' };
        }
        
        // Check for expected domain patterns based on user type
        if (userType === 'admin') {
          // Admin emails should typically be from deped.gov.ph or the system admin email
          if (!email.includes('@deped.gov.ph') && !email.includes('vb48gointomasterlevel@gmail.com')) {
            return { 
              valid: false, 
              message: 'Admin accounts typically use @deped.gov.ph email addresses. Please check your email or contact the administrator.' 
            };
          }
        } else if (userType === 'faculty') {
          // Faculty emails should be from deped.gov.ph
          if (!email.includes('@deped.gov.ph')) {
            return { 
              valid: false, 
              message: 'Faculty accounts must use @deped.gov.ph email addresses.' 
            };
          }
        }
        
        return { valid: true, message: '' };
      }
      
      // Handle form submission with enhanced validation
      document.getElementById('submitResetBtn').addEventListener('click', function() {
        const email = document.getElementById('resetEmail').value.trim();
        const emailInput = document.getElementById('resetEmail');
        const emailError = document.getElementById('emailError');
        const emailHelp = document.getElementById('emailHelp');
        
        // Clear previous error states
        emailInput.classList.remove('is-invalid');
        emailError.textContent = '';
        
        // Validate email for user type
        const validation = validateEmailForUserType(email, userType);
        
        if (!validation.valid) {
          emailInput.classList.add('is-invalid');
          emailError.textContent = validation.message;
          emailInput.focus();
          return;
        }
        
        // Show loading state
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';
        
        // Send AJAX request
        fetch('functions/forget_password.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'email=' + encodeURIComponent(email) + '&request_reset=1&user_type=' + encodeURIComponent(userType)
        })
        .then(response => response.json())
        .then(data => {
          // Close the modal
          modal.hide();
          
          // Handle the response based on status
          if (data.status === 'success') {
            // Success case
            Swal.fire({
              icon: 'success',
              title: 'Password Reset Link Sent!',
              html: `
                <div style='text-align: left;'>
                  <p><strong>Check your email inbox!</strong></p>
                  <p>We've sent a password reset link to:</p>
                  <p style='background-color: #f8f9fa; padding: 10px; border-radius: 5px; margin: 10px 0;'><strong>${data.message}</strong></p>
                  <p><strong>What to do next:</strong></p>
                  <ul style='text-align: left; margin: 10px 0;'>
                    <li>Check your email inbox (and spam folder)</li>
                    <li>Click the password reset link in the email</li>
                    <li>Create a new password</li>
                    <li>Log in with your new password</li>
                  </ul>
                  <p style='color: #6c757d; font-size: 14px;'><em>Note: The reset link will expire in 1 hour for security reasons.</em></p>
                </div>
              `,
              showConfirmButton: true,
              confirmButtonText: 'Got it!',
              width: '500px'
            });
          } else {
            // Error or warning case
            Swal.fire({
              icon: data.type,
              title: data.type === 'warning' ? 'Password Reset Notice' : 'Password Reset Failed',
              text: data.message,
              showConfirmButton: true,
              confirmButtonText: 'OK'
            });
          }
        })
        .catch(error => {
          // Reset button state
          this.disabled = false;
          this.innerHTML = 'Send Reset Link';
          
          Swal.fire('Error', 'An error occurred while processing your request.', 'error');
        });
      });
      
      // Real-time email validation on input
      document.getElementById('resetEmail').addEventListener('input', function() {
        const email = this.value.trim();
        const emailError = document.getElementById('emailError');
        
        // Clear error state when user starts typing
        if (email.length > 0) {
          this.classList.remove('is-invalid');
          emailError.textContent = '';
        }
      });
      
      // Validate on blur (when user leaves the field)
      document.getElementById('resetEmail').addEventListener('blur', function() {
        const email = this.value.trim();
        if (email.length > 0) {
          const validation = validateEmailForUserType(email, userType);
          if (!validation.valid) {
            this.classList.add('is-invalid');
            document.getElementById('emailError').textContent = validation.message;
          }
        }
      });
    }
  </script>


<!-- <script>
document.addEventListener('DOMContentLoaded', function () {
    const registrationForm = document.getElementById('registrationForm');

    if (registrationForm) {
        registrationForm.addEventListener('submit', function (e) {
            // Check form validity
            if (!registrationForm.checkValidity()) {
                e.preventDefault(); // Prevent form submission
                registrationForm.classList.add('was-validated'); // Add Bootstrap validation styles
                return; // Stop further execution if the form is invalid
            }

            // If the form is valid, proceed with AJAX for database submission
            e.preventDefault(); // Prevent default form submission

            // Create a FormData object from the form
            const formData = new FormData(registrationForm);

            // Send the form data via AJAX
            fetch('functions/register_process.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json()) // Parse the JSON response
                .then(data => {
                    if (data.status === 'success') {
                        // Show SweetAlert success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Registration Successful!',
                            text: data.message
                        }).then(() => {
                            // Redirect to index.php
                            window.location.href = 'index.php';
                        });
                    } else {
                        // Show SweetAlert error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Registration Failed!',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'An error occurred!',
                        text: 'Something went wrong. Please try again.',
                        showConfirmButton: true
                    });
                });
        });
    }
});
</script> -->

<script>
    document.addEventListener('DOMContentLoaded', function () {
    const registrationForm = document.getElementById('registrationForm');

    if (registrationForm) {
        registrationForm.addEventListener('submit', function (e) {
            // Check form validity
            if (!registrationForm.checkValidity()) {
                e.preventDefault(); // Prevent form submission
                registrationForm.classList.add('was-validated'); // Add Bootstrap validation styles
                return; // Stop further execution if the form is invalid
            }

            // If the form is valid, proceed with AJAX for database submission
            e.preventDefault(); // Prevent default form submission

            // Create a FormData object from the form
            const formData = new FormData(registrationForm);

            // Log form data to check if it's being sent
            for (const [key, value] of formData.entries()) {
                console.log(key + ": " + value);
            }

            // Send the form data via AJAX
            fetch('functions/register_process.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json()) // Parse the JSON response
                .then(data => {
                    console.log(data); // Log the response to check for errors or issues
                    if (data.status === 'success') {
                        // Show SweetAlert success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Registration Successful!',
                            text: data.message
                        }).then(() => {
                            // Redirect to index.php
                            window.location.href = 'index.php';
                        });
                    } else {
                        // Show SweetAlert error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Registration Failed!',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    console.error("Error: ", error); // Log any AJAX error
                    Swal.fire({
                        icon: 'error',
                        title: 'An error occurred!',
                        text: 'Something went wrong. Please try again.',
                        showConfirmButton: true
                    });
                });
        });
    }
});

</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const adminLoginForm = document.getElementById('adminLoginForm');

    if (adminLoginForm) {
        adminLoginForm.addEventListener('submit', function (e) {
            e.preventDefault(); // Prevent default form submission

            if (!adminLoginForm.checkValidity()) {
                adminLoginForm.classList.add('was-validated'); // Show validation errors
                return;
            }

            // Create a FormData object
            const formData = new FormData(adminLoginForm);

            // Send the form data via AJAX
            fetch('functions/admin_login_process.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json()) // Parse the JSON response
                .then(data => {
                    if (data.status === 'success') {
                        // Show success SweetAlert and redirect
                        Swal.fire({
                            icon: 'success',
                            title: 'Login Successful!',
                            text: data.message
                        }).then(() => {
                            window.location.href = 'pages/dashboard-overview.php'; // Redirect to admin dashboard
                        });
                    } else {
                        // Show error SweetAlert
                        Swal.fire({
                            icon: 'error',
                            title: 'Login Failed!',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    // Handle unexpected errors
                    Swal.fire({
                        icon: 'error',
                        title: 'An error occurred!',
                        text: 'Something went wrong. Please try again later.',
                        showConfirmButton: true
                    });
                });
        });
    }
});
</script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const facultyLoginForm = document.getElementById('facultyLoginForm');

    if (facultyLoginForm) {
        facultyLoginForm.addEventListener('submit', function (e) {
            e.preventDefault(); // Prevent default form submission

            if (!facultyLoginForm.checkValidity()) {
                facultyLoginForm.classList.add('was-validated'); // Show validation errors
                return;
            }

            // Create a FormData object
            const formData = new FormData(facultyLoginForm);

            // Send the form data via AJAX
            fetch('functions/faculty_login_process.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json()) // Parse the JSON response
                .then(data => {
                    if (data.status === 'success') {
                        // Show success SweetAlert and redirect
                        Swal.fire({
                            icon: 'success',
                            title: 'Login Successful!',
                            text: data.message
                        }).then(() => {
                            window.location.href = 'pages/dashboard-overview.php'; // Redirect to faculty dashboard
                        });
                    } else {
                        // Show error SweetAlert
                        Swal.fire({
                            icon: 'error',
                            title: 'Login Failed!',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    // Handle unexpected errors
                    Swal.fire({
                        icon: 'error',
                        title: 'An error occurred!',
                        text: 'Something went wrong. Please try again later.',
                        showConfirmButton: true
                    });
                });
        });
    }
});
</script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');
        const message = urlParams.get('message');

        if (status && message) {
            Swal.fire({
                icon: status === 'success' ? 'success' : 'error',
                title: status === 'success' ? 'Success!' : 'Error!',
                text: message,
                showConfirmButton: true
            }).then(() => {
                // Clear URL parameters after showing the alert
                window.history.replaceState({}, document.title, window.location.pathname);
            });
        }

        // Check if there's a reset token in URL and open reset password modal
        const resetToken = urlParams.get('token');
        if (resetToken) {
            // Set the token in the hidden input
            document.getElementById('resetToken').value = resetToken;
            // Open the reset password modal
            const resetPasswordModal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
            resetPasswordModal.show();
        }
    });
</script>

<script>
    // Reset Password Form Validation
    document.addEventListener('DOMContentLoaded', function () {
        const resetPasswordForm = document.getElementById('resetPasswordForm');
        const newPasswordInput = document.getElementById('newPassword');
        const confirmPasswordInput = document.getElementById('confirmPassword');

        if (resetPasswordForm) {
            // Real-time password confirmation validation
            function validatePasswordMatch() {
                if (newPasswordInput.value !== confirmPasswordInput.value) {
                    confirmPasswordInput.setCustomValidity('Passwords do not match');
                } else {
                    confirmPasswordInput.setCustomValidity('');
                }
            }

            newPasswordInput.addEventListener('input', validatePasswordMatch);
            confirmPasswordInput.addEventListener('input', validatePasswordMatch);

            resetPasswordForm.addEventListener('submit', function (e) {
                if (!resetPasswordForm.checkValidity()) {
                    e.preventDefault();
                    resetPasswordForm.classList.add('was-validated');
                    return;
                }

                // Additional validation
                if (newPasswordInput.value !== confirmPasswordInput.value) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error!',
                        text: 'Passwords do not match.',
                        showConfirmButton: true
                    });
                    return;
                }
            });
        }
    });
</script>

<script>
  // Add this script to handle the close icon
  document.addEventListener('DOMContentLoaded', function () {
    var bannerClose = document.getElementById('bannerClose');
    if (bannerClose) {
      bannerClose.onclick = function () {
        document.querySelector('.header.banner').style.display = 'none';
      };
    }
  });
</script>

</body>
</html>