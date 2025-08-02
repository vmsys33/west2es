<div class="modal fade" id="facultyLoginModal" tabindex="-1" aria-labelledby="facultyLoginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <!-- Modal Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="facultyLoginModalLabel">Faculty Login</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Modal Body -->
            <div class="modal-body p-4">
                <form id="facultyLoginForm" action="functions/faculty_login_process.php" method="POST" novalidate>
                    <!-- DepEd ID No. -->
                    <div class="mb-3">
                        <label for="facultyIdNo" class="form-label fw-semibold">DepEd ID No.</label>
                        <input type="text" class="form-control" id="facultyIdNo" name="deped_id_no" placeholder="123456" required pattern="^\d{6}$">
                        <div class="invalid-feedback">
                            DepEd ID must be exactly 6 digits (e.g., 123456).
                        </div>
                    </div>
                    <!-- Email -->
                    <div class="mb-3">
                        <label for="facultyEmail" class="form-label fw-semibold">Email Address</label>
                        <input type="email" class="form-control" id="facultyEmail" name="email" placeholder="yourname@deped.gov.ph" required>
                        <div class="invalid-feedback">
                            Please enter a valid DepEd email address.
                        </div>
                    </div>
                    <!-- Password -->
                    <div class="mb-3">
                        <label for="facultyPassword" class="form-label fw-semibold">Password</label>
                        <input type="password" class="form-control" id="facultyPassword" name="password" placeholder="••••••••" required>
                        <div class="invalid-feedback">
                            Please enter your password.
                        </div>
                    </div>
                    <!-- Submit Button -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Log In</button>
                    </div>
                </form>
            </div>
            <!-- Modal Footer -->
            <div class="modal-footer border-top-0 justify-content-center">
                <small class="text-muted">
                    Forgot your password? 
                    <a href="#" class="text-primary" onclick="showResetForm()">Click here</a>
                </small>
            </div>
        </div>
    </div>
</div>
