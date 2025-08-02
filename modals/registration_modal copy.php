<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="registerModalLabel">Register Account</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="registrationForm" action="functions/register_process.php" method="POST" novalidate>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="lastName" class="form-label">Last Name</label>
                            <!-- <input type="text" class="form-control" id="lastName" name="last_name" required pattern="^[a-zA-Z]+$"> -->
                            <input type="text" class="form-control" id="lastName" name="last_name" required pattern="^[a-zA-Z\s-]+$">
                            <div class="invalid-feedback">Please enter a valid last name using only letters.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstName" name="first_name" required pattern="^[a-zA-Z\s-]+$">
                            <div class="invalid-feedback">Please enter a valid first name using only letters.</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="middleName" class="form-label">Middle Name</label>
                        <input type="text" class="form-control" id="middleName" name="middle_name" required pattern="^[a-zA-Z\s-]+$">
                        <div class="invalid-feedback">Please enter a valid middle name or leave it blank.</div>
                    </div>
                      <!-- <div class="mb-3">
                        <label for="depedIdNo" class="form-label">DepEd ID No.</label>
                        <input type="text" class="form-control" id="depedIdNo" name="deped_id_no" required pattern="^\d{2}-\d{8}$">
                        <div class="invalid-feedback">Please enter a valid DepEd ID number in the format 11-22334455.</div>
                    </div> -->
                    <div class="mb-3">
                        <label for="depedIdNo" class="form-label">Please enter a valid 6-digit DepEd ID No.</label>
                        <input type="text" class="form-control" id="depedIdNo" name="deped_id_no" required pattern="^\d{6}$">
                        <div class="invalid-feedback">Please enter a valid DepEd ID number with exactly 6 digits.</div>
                    </div>

    
                    <div class="mb-3">
                        <label for="registerEmail" class="form-label">DepEd Email Account</label>
                        <div class="d-flex align-items-center">
                            <input type="text" class="form-control" id="registerEmail" name="email_prefix" autocomplete="off" required
                                pattern="^[^@]+$" placeholder="Enter email (e.g., firstname.lastname)">
                            <span class="ms-2">@deped.gov.ph</span>
                        </div>
                        <small class="text-muted">Do not include @deped.gov.ph or any other email extension.</small>
                        <div class="invalid-feedback">Email must not contain "@" or any domain extensions.</div>
                    </div>

    
                    
                    <div class="mb-3">
                        <label for="registerPassword" class="form-label">Password</label>
                        <input type="password" class="form-control" id="registerPassword" name="password" required minlength="8">
                        <div class="invalid-feedback">Password must be at least 8 characters long.</div>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                        <div class="invalid-feedback">Please confirm your password.</div>
                    </div>
                  
                    <button type="submit" id="AddRegistersubmit" class="btn btn-primary">Create</button>
                </form>
            </div>
        </div>
    </div>
</div>

