<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadiz West 2 Elementary School</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

  <style>
    body {
      margin: 0;
      height: 100vh;
      background: url('../assets/images/west2.png') no-repeat center center fixed;
      background-size: cover;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .form-container {
      background: rgba(255, 255, 255, 0.9);
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      max-width: 400px;
      width: 100%;
      text-align: center;
    }

    .form-container img {
      max-width: 100px;
      margin-bottom: 20px;
    }

    .form-container h2 {
      font-size: 22px;
      font-weight: bold;
      margin-bottom: 20px;
      color: #333;
    }

    .form-label {
      font-weight: bold;
      color: #333;
    }

    .form-control {
      border-radius: 5px;
      border: 1px solid #ced4da;
      padding: 10px;
      font-size: 14px;
    }

    .btn-primary {
      background-color: #0d6efd;
      border-color: #0d6efd;
      font-size: 16px;
      font-weight: bold;
      padding: 10px 20px;
      border-radius: 5px;
      margin-top: 10px;
    }

    .btn-primary:hover {
      background-color: #0b5ed7;
      border-color: #0a58ca;
    }

    .btn-primary:disabled {
      background-color: #6c757d;
      border-color: #6c757d;
    }

    .password-requirements {
      font-size: 12px;
      color: #6c757d;
      margin-top: 5px;
      text-align: left;
    }

    .requirement {
      margin: 2px 0;
    }

    .requirement.met {
      color: #198754;
    }

    .requirement.not-met {
      color: #dc3545;
    }

    .strength-meter {
      height: 4px;
      background-color: #e9ecef;
      border-radius: 2px;
      margin-top: 5px;
      overflow: hidden;
    }

    .strength-fill {
      height: 100%;
      transition: width 0.3s ease, background-color 0.3s ease;
      width: 0%;
    }

    .strength-weak { background-color: #dc3545; }
    .strength-fair { background-color: #ffc107; }
    .strength-good { background-color: #198754; }
    .strength-strong { background-color: #0d6efd; }
  </style>
</head>

<body>
  <div class="form-container">
    <h2>Reset Password</h2>
    <form id="resetPasswordForm" action="../functions/reset_password.php" method="POST" novalidate>
      <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">
      <input type="hidden" name="user_type" value="<?php echo htmlspecialchars($_GET['user_type'] ?? ''); ?>">
      
      <div class="mb-3">
        <label for="new_password" class="form-label">New Password</label>
        <input type="password" class="form-control" id="new_password" name="new_password" 
               placeholder="Enter your new password" required minlength="8">
        <div class="strength-meter">
          <div class="strength-fill" id="strengthFill"></div>
        </div>
        <div class="password-requirements" id="passwordRequirements">
          <div class="requirement" id="lengthReq">At least 8 characters</div>
          <div class="requirement" id="uppercaseReq">One uppercase letter</div>
          <div class="requirement" id="lowercaseReq">One lowercase letter</div>
          <div class="requirement" id="numberReq">One number</div>
        </div>
        <div class="invalid-feedback">
          Please enter a strong password that meets all requirements.
        </div>
      </div>

      <div class="mb-3">
        <label for="confirm_password" class="form-label">Confirm Password</label>
        <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
               placeholder="Confirm your new password" required>
        <div class="invalid-feedback">
          Passwords do not match.
        </div>
      </div>

      <div class="d-grid">
        <button type="submit" class="btn btn-primary" name="reset_password" id="submitBtn" disabled>
          Reset Password
        </button>
      </div>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

        // Password validation
        const passwordInput = document.getElementById('new_password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const submitBtn = document.getElementById('submitBtn');
        const strengthFill = document.getElementById('strengthFill');

        function validatePassword(password) {
            const requirements = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /\d/.test(password)
            };

            // Update requirement indicators
            document.getElementById('lengthReq').className = `requirement ${requirements.length ? 'met' : 'not-met'}`;
            document.getElementById('uppercaseReq').className = `requirement ${requirements.uppercase ? 'met' : 'not-met'}`;
            document.getElementById('lowercaseReq').className = `requirement ${requirements.lowercase ? 'met' : 'not-met'}`;
            document.getElementById('numberReq').className = `requirement ${requirements.number ? 'met' : 'not-met'}`;

            // Calculate strength
            const metCount = Object.values(requirements).filter(Boolean).length;
            let strength = 'weak';
            let strengthClass = 'strength-weak';

            if (metCount === 4) {
                strength = 'strong';
                strengthClass = 'strength-strong';
            } else if (metCount === 3) {
                strength = 'good';
                strengthClass = 'strength-good';
            } else if (metCount === 2) {
                strength = 'fair';
                strengthClass = 'strength-fair';
            }

            // Update strength meter
            strengthFill.style.width = (metCount / 4 * 100) + '%';
            strengthFill.className = `strength-fill ${strengthClass}`;

            return metCount === 4;
        }

        function validateForm() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            const isPasswordValid = validatePassword(password);
            const passwordsMatch = password === confirmPassword;

            // Update confirm password validation
            if (confirmPassword && !passwordsMatch) {
                confirmPasswordInput.setCustomValidity('Passwords do not match');
            } else {
                confirmPasswordInput.setCustomValidity('');
            }

            // Enable/disable submit button
            submitBtn.disabled = !(isPasswordValid && passwordsMatch && password.length > 0);
        }

        passwordInput.addEventListener('input', validateForm);
        confirmPasswordInput.addEventListener('input', validateForm);

        // Form submission
        document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            if (!validatePassword(password)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Weak Password',
                    text: 'Please ensure your password meets all requirements.',
                    showConfirmButton: true
                });
                return false;
            }

            if (password !== confirmPassword) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Passwords Do Not Match',
                    text: 'Please make sure both passwords are identical.',
                    showConfirmButton: true
                });
                return false;
            }

            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Resetting...';
        });
    });
  </script>
</body>

</html>
