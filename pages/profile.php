<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit;
}

require_once '../functions/db_connection.php';


// Get the current file name
$currentPage = basename($_SERVER['PHP_SELF']);

require_once '../functions/pageTitle.php';
$pageTitle = getPageTitle($currentPage);


// Fetch user data from the database
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM user_data WHERE id_no = :id_no");
$stmt->bindParam(':id_no', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch user_data_details from the database
$stmt = $pdo->prepare("SELECT * FROM user_data_details WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user_details = $stmt->fetch(PDO::FETCH_ASSOC) ?: []; // Default to an empty array if no data is found
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/top-navbar.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="col-md-9 main-content">
    <form id="userDetailsForm" class="row g-3" enctype="multipart/form-data">
        <!-- Hidden Input for user_id -->
        <input type="hidden" name="user_id" id="user_id" value="<?php echo $user['id_no'] ?? ''; ?>">

        <!-- Fields from user_data Table -->
        <div class="col-md-6">
            <label for="deped_id_no" class="form-label">DepEd ID No</label>
            <input type="text" class="form-control" id="deped_id_no" name="deped_id_no" value="<?php echo $user['deped_id_no'] ?? ''; ?>">
        </div>

        <div class="col-md-6">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $user['last_name'] ?? ''; ?>">
        </div>

        <div class="col-md-6">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $user['first_name'] ?? ''; ?>">
        </div>

        <div class="col-md-6">
            <label for="middle_name" class="form-label">Middle Name</label>
            <input type="text" class="form-control" id="middle_name" name="middle_name" value="<?php echo $user['middle_name'] ?? ''; ?>">
        </div>

        <div class="col-md-6">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email'] ?? ''; ?>">
        </div>

        <!-- Photo Upload Field -->
        <div class="col-md-6">
            <label for="photo" class="form-label">Profile Photo</label>
            <div class="d-flex align-items-center">
                <?php 
                $userPhoto = null;
                if (isset($_SESSION['id_no'])) {
                    require_once '../functions/upload_user_photo.php';
                    $userPhoto = getUserPhoto($_SESSION['id_no']);
                }
                ?>
                <?php if ($userPhoto): ?>
                    <img src="<?= htmlspecialchars($userPhoto) ?>" alt="Current Photo" class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover; border: 2px solid #007bff;">
                <?php else: ?>
                    <div class="rounded-circle me-3 d-flex align-items-center justify-content-center bg-light" style="width: 60px; height: 60px;">
                        <i class="fas fa-user" style="font-size: 30px; color: #ccc;"></i>
                    </div>
                <?php endif; ?>
                <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
            </div>
            <small class="text-muted">Upload JPG, PNG, or GIF (max 5MB)</small>
        </div>

        <div class="col-md-6">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status">
                <option value="active" <?php if (($user['status'] ?? '') === 'active') echo 'selected'; ?>>Active</option>
                <option value="inactive" <?php if (($user['status'] ?? '') === 'inactive') echo 'selected'; ?>>Inactive</option>
                <option value="pending" <?php if (($user['status'] ?? '') === 'pending') echo 'selected'; ?>>Pending</option>
            </select>
        </div>

        <div class="col-md-6">
            <label for="role" class="form-label">Role</label>
            <select class="form-select" id="role" name="role">
                <option value="">Please select User Role</option>
                <option value="faculty" <?php if (($user['role'] ?? '') === 'faculty') echo 'selected'; ?>>Faculty</option>
                <option value="admin" <?php if (($user['role'] ?? '') === 'admin') echo 'selected'; ?>>Admin</option>
            </select>
        </div>

        <!-- Password Change -->
        <div class="col-md-6">
            <label for="password" class="form-label">New Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password">
        </div>

        <div class="col-md-6">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm new password">
        </div>

        <!-- Fields from user_data_details Table -->
        <div class="col-md-6">
            <label for="suffix" class="form-label">Suffix</label>
            <input type="text" class="form-control" id="suffix" name="suffix" value="<?php echo $user_details['suffix'] ?? ''; ?>">
        </div>

        <div class="col-md-6">
            <label for="date_of_birth" class="form-label">Date of Birth</label>
            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?php echo $user_details['date_of_birth'] ?? ''; ?>">
        </div>

        <div class="col-md-6">
            <label for="birthplace" class="form-label">Birthplace</label>
            <input type="text" class="form-control" id="birthplace" name="birthplace" value="<?php echo $user_details['birthplace'] ?? ''; ?>">
        </div>

        <div class="col-md-6">
            <label for="sex" class="form-label">Sex</label>
            <select class="form-select" id="sex" name="sex">
                <option value="Male" <?php if (($user_details['sex'] ?? '') === 'Male') echo 'selected'; ?>>Male</option>
                <option value="Female" <?php if (($user_details['sex'] ?? '') === 'Female') echo 'selected'; ?>>Female</option>
                <option value="Other" <?php if (($user_details['sex'] ?? '') === 'Other') echo 'selected'; ?>>Other</option>
            </select>
        </div>

        <div class="col-md-6">
            <label for="position" class="form-label">Position</label>
            <input type="text" class="form-control" id="position" name="position" value="<?php echo $user_details['position'] ?? ''; ?>">
        </div>

        <div class="col-md-6">
            <label for="contact_no" class="form-label">Contact No</label>
            <input type="text" class="form-control" id="contact_no" name="contact_no" value="<?php echo $user_details['contact_no'] ?? ''; ?>">
        </div>

        <div class="col-md-6">
            <label for="personal_gmail_account" class="form-label">Personal Gmail Account</label>
            <input type="email" class="form-control" id="personal_gmail_account" name="personal_gmail_account" value="<?php echo $user_details['personal_gmail_account'] ?? ''; ?>">
        </div>

        <div class="col-md-6">
            <label for="bachelors_degree" class="form-label">Bachelor's Degree</label>
            <input type="text" class="form-control" id="bachelors_degree" name="bachelors_degree" value="<?php echo $user_details['bachelors_degree'] ?? ''; ?>">
        </div>

        <div class="col-md-6">
            <label for="post_graduate" class="form-label">Post Graduate</label>
            <input type="text" class="form-control" id="post_graduate" name="post_graduate" value="<?php echo $user_details['post_graduate'] ?? ''; ?>">
        </div>

        <div class="col-md-6">
            <label for="major" class="form-label">Major</label>
            <input type="text" class="form-control" id="major" name="major" value="<?php echo $user_details['major'] ?? ''; ?>">
        </div>

        <div class="col-md-6">
            <label for="employee_no" class="form-label">Employee No</label>
            <input type="text" class="form-control" id="employee_no" name="employee_no" value="<?php echo $user_details['employee_no'] ?? ''; ?>">
        </div>

        <div class="col-md-6">
            <label for="plantilla_no" class="form-label">Plantilla No</label>
            <input type="text" class="form-control" id="plantilla_no" name="plantilla_no" value="<?php echo $user_details['plantilla_no'] ?? ''; ?>">
        </div>

        <div class="col-md-6">
            <label for="philhealth_no" class="form-label">Philhealth No</label>
            <input type="text" class="form-control" id="philhealth_no" name="philhealth_no" value="<?php echo $user_details['philhealth_no'] ?? ''; ?>">
        </div>

        <div class="col-md-6">
            <label for="bp_no" class="form-label">BP No</label>
            <input type="text" class="form-control" id="bp_no" name="bp_no" value="<?php echo $user_details['bp_no'] ?? ''; ?>">
        </div>

        <div class="col-md-6">
            <label for="pagibig_no" class="form-label">Pagibig No</label>
            <input type="text" class="form-control" id="pagibig_no" name="pagibig_no" value="<?php echo $user_details['pagibig_no'] ?? ''; ?>">
        </div>

        <div class="col-md-6">
            <label for="tin_no" class="form-label">TIN No</label>
            <input type="text" class="form-control" id="tin_no" name="tin_no" value="<?php echo $user_details['tin_no'] ?? ''; ?>">
        </div>

        <div class="col-md-6">
            <label for="prc_no" class="form-label">PRC No</label>
            <input type="text" class="form-control" id="prc_no" name="prc_no" value="<?php echo $user_details['prc_no'] ?? ''; ?>">
        </div>

        <div class="col-md-6">
            <label for="prc_validity_date" class="form-label">PRC Validity Date</label>
            <input type="date" class="form-control" id="prc_validity_date" name="prc_validity_date" value="<?php echo $user_details['prc_validity_date'] ?? ''; ?>">
        </div>

        <div class="col-md-6">
            <label for="phlisys_id_no" class="form-label">Phlisys ID No</label>
            <input type="text" class="form-control" id="phlisys_id_no" name="phlisys_id_no" value="<?php echo $user_details['phlisys_id_no'] ?? ''; ?>">
        </div>

        <div class="col-md-6">
            <label for="salary_grade" class="form-label">Salary Grade</label>
            <input type="text" class="form-control" id="salary_grade" name="salary_grade" value="<?php echo $user_details['salary_grade'] ?? ''; ?>">
        </div>

        <div class="col-md-6">
            <label for="current_step_based_on_payslip" class="form-label">Current Step Based on Payslip</label>
            <input type="text" class="form-control" id="current_step_based_on_payslip" name="current_step_based_on_payslip" value="<?php echo $user_details['current_step_based_on_payslip'] ?? ''; ?>">
        </div>

        <div class="col-md-6">
            <label for="date_of_first_appointment" class="form-label">Date of First Appointment</label>
            <input type="date" class="form-control" id="date_of_first_appointment" name="date_of_first_appointment" value="<?php echo $user_details['date_of_first_appointment'] ?? ''; ?>">
        </div>

        <div class="col-md-6">
            <label for="date_of_latest_promotion" class="form-label">Date of Latest Promotion</label>
            <input type="date" class="form-control" id="date_of_latest_promotion" name="date_of_latest_promotion" value="<?php echo $user_details['date_of_latest_promotion'] ?? ''; ?>">
        </div>

        <div class="col-md-6">
            <label for="first_day_of_service" class="form-label">First Day of Service</label>
            <input type="date" class="form-control" id="first_day_of_service" name="first_day_of_service" value="<?php echo $user_details['first_day_of_service'] ?? ''; ?>">
        </div>

        <div class="col-md-6">
            <label for="retirement_day" class="form-label">Retirement Day</label>
            <input type="date" class="form-control" id="retirement_day" name="retirement_day" value="<?php echo $user_details['retirement_day'] ?? ''; ?>">
        </div>

        <!-- Submit Button -->
        <div class="col-12">
            <button type="button" class="btn btn-primary" id="submitForm">Submit</button>
        </div>
    </form>

    <div class="col-12 text-center mt-4">
    
    <a href="../functions/generate_excel.php?user_id=<?php echo $user['id_no'] ?? ''; ?>" class="btn btn-secondary">
    Download Excel
</a>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.getElementById('submitForm').addEventListener('click', function () {
        const form = document.getElementById('userDetailsForm');
        const formData = new FormData(form);

        // Validate password match
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        if (password && password !== confirmPassword) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Passwords do not match!',
            });
            return;
        }

        fetch('../functions/process_user_details.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.message,
                }).then(() => {
                    location.reload(); // Reload the page after successful save
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message,
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'An error occurred',
                text: 'An error occurred while saving the data. Please try again.',
            });
        });
    });
</script>



<?php include '../includes/footer.php'; ?>
