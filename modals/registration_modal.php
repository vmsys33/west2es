<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <!-- Increased width for better spacing -->
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="registerModalLabel">Create an Account</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="registrationForm" action="functions/register_process.php" method="POST" enctype="multipart/form-data" novalidate>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="lastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastName" name="last_name" required pattern="^[a-zA-Z\s-]+$">
                            <div class="invalid-feedback">Enter a valid last name.</div>
                        </div>
                        <div class="col-md-4">
                            <label for="firstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstName" name="first_name" required pattern="^[a-zA-Z\s-]+$">
                            <div class="invalid-feedback">Enter a valid first name.</div>
                        </div>
                        <div class="col-md-4">
                            <label for="middleName" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="middleName" name="middle_name" pattern="^[a-zA-Z\s-]+$">
                            <div class="invalid-feedback">Enter a valid middle name.</div>
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label for="depedIdNo" class="form-label">DepEd ID No. (7 digits)</label>
                            <input type="text" class="form-control" id="depedIdNo" name="deped_id_no" required pattern="^\d{7}$" minlength="7" maxlength="7">
                            <div class="invalid-feedback">Enter exactly 7 digits.</div>
                        </div>
                       
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label for="registerEmail" class="form-label">DepEd Email</label>
                            <div class="input-group">
                                <input type="email" class="form-control" id="registerEmail" name="email_prefix" autocomplete="off" required
                                    pattern="^[^@]+$" placeholder="e.g., firstname.lastname">
                                <span class="input-group-text">@deped.gov.ph</span>
                            </div>
                            <small class="text-muted">Do not include "@deped.gov.ph" manually.</small>
                            <div class="invalid-feedback">Invalid email format.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="contactNo" class="form-label">Contact No.</label>
                            <input type="text" class="form-control" id="contactNo" name="contact_no" pattern="^\d{10,15}$" required>
                            <div class="invalid-feedback">Enter a valid contact number (10-15 digits).</div>
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label for="registerPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="registerPassword" name="password" required minlength="8">
                            <div class="invalid-feedback">Must be at least 8 characters.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="confirmPassword" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                            <div class="invalid-feedback">Passwords must match.</div>
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-12">
                            <label for="profilePhoto" class="form-label">Profile Photo (Optional)</label>
                            <input type="file" class="form-control" id="profilePhoto" name="photo" accept="image/*">
                            <small class="text-muted">Upload JPG, PNG, or GIF (max 5MB)</small>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <button type="submit" id="AddRegistersubmit" class="btn btn-primary w-100">Create Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
