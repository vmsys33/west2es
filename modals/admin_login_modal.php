<?php include_once __DIR__ . '/../includes/csrf.php'; ?>
<div class="modal fade" id="adminLoginModal" tabindex="-1" aria-labelledby="adminLoginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
                        <!-- Modal Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="facultyLoginModalLabel">Admin Login</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <form id="adminLoginForm" action="functions/admin_login_process.php" method="POST" novalidate autocomplete="off">
                    <!-- DepEd ID No. -->
                    <div class="mb-3">
                        <label for="idNo" class="form-label fw-semibold">DepEd ID No.</label>
                        <input type="text" class="form-control" id="idNo" name="deped_id_no" placeholder="1234567" required pattern="^\d{7}$" minlength="7" maxlength="7" autocomplete="off">
                        <div class="invalid-feedback">
                            DepEd ID must be exactly 7 digits (e.g., 1234567).
                        </div>
                    </div>
                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="yourname@deped.gov.ph" required autocomplete="off">
                        <div class="invalid-feedback">
                            Please enter a valid DepEd email address (must end with @deped.gov.ph).
                        </div>
                    </div>
                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required minlength="8" autocomplete="off">
                        <div class="invalid-feedback">
                            Please enter your password (minimum 8 characters).
                        </div>
                    </div>
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generate_csrf_token()); ?>">
                    <!-- Submit Button -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Log In</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top-0 justify-content-center">
                <small class="text-muted">Forgot your password? 
                    <a href="#" class="text-primary" onclick="showResetForm('admin')">Click here</a>
                </small>
            </div>
        </div>
    </div>
</div>
