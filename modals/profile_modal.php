<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit;
}

require_once '../functions/db_connection.php';

// Fetch admin profile data from user_data table
$adminId = $_SESSION['user_id']; // Assuming admin_id is stored in the session
$stmt = $pdo->prepare("SELECT deped_id_no, last_name, first_name, middle_name, email FROM user_data WHERE id_no = ? AND role = 'admin'");
$stmt->execute([$adminId]);
$adminProfile = $stmt->fetch();

$depedIdNo = $adminProfile['deped_id_no'] ?? "";
$lastName = $adminProfile['last_name'] ?? "";
$firstName = $adminProfile['first_name'] ?? "";
$middleName = $adminProfile['middle_name'] ?? "";
$adminEmail = $adminProfile['email'] ?? "";

// Handle form submission via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_profile'])) {
    $depedIdNo = $_POST['deped_id_no'];
    $lastName = $_POST['last_name'];
    $firstName = $_POST['first_name'];
    $middleName = $_POST['middle_name'];
    $adminEmail = $_POST['email'];

    // Update admin profile in the user_data table
    $stmt = $pdo->prepare("UPDATE user_data SET deped_id_no = ?, last_name = ?, first_name = ?, middle_name = ?, email = ? WHERE id_no = ? AND role = 'admin'");
    $result = $stmt->execute([$depedIdNo, $lastName, $firstName, $middleName, $adminEmail, $adminId]);
    
    // Handle admin photo upload if provided
    if (!empty($_FILES['admin_photo']['name'])) {
        require_once '../functions/upload_user_photo.php';
        $photoResult = uploadUserPhoto($adminId, $_FILES['admin_photo']);
        if (!$photoResult['success']) {
            // Log the error but don't stop the process
            error_log("Admin photo upload failed: " . $photoResult['message']);
        }
    }
    
    // Return JSON response for AJAX
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update profile.']);
    }
    exit;
}
?>

<!-- Profile Settings Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="profileModalLabel">
                    <i class="fas fa-user me-2"></i>Admin Profile Settings
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="profileAlert" class="alert" style="display: none;"></div>
                
                <form id="profileForm" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="admin_profile" value="1">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="deped_id_no" class="form-label">DepEd ID No</label>
                                <input type="text" name="deped_id_no" id="deped_id_no" class="form-control" value="<?php echo htmlspecialchars($depedIdNo); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($adminEmail); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" name="last_name" id="last_name" class="form-control" value="<?php echo htmlspecialchars($lastName); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" name="first_name" id="first_name" class="form-control" value="<?php echo htmlspecialchars($firstName); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="middle_name" class="form-label">Middle Name</label>
                                <input type="text" name="middle_name" id="middle_name" class="form-control" value="<?php echo htmlspecialchars($middleName); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="admin_photo" class="form-label">Profile Photo</label>
                        <div class="d-flex align-items-center">
                            <?php 
                            $adminPhoto = null;
                            if (isset($_SESSION['user_id'])) {
                                require_once '../functions/upload_user_photo.php';
                                $adminPhoto = getUserPhoto($_SESSION['user_id']);
                            }
                            ?>
                            <?php if ($adminPhoto): ?>
                                <img src="<?= htmlspecialchars($adminPhoto) ?>" alt="Current Photo" class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover; border: 2px solid #007bff;">
                            <?php else: ?>
                                <div class="rounded-circle me-3 d-flex align-items-center justify-content-center bg-light" style="width: 60px; height: 60px;">
                                    <i class="fas fa-user" style="font-size: 30px; color: #ccc;"></i>
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" id="admin_photo" name="admin_photo" accept="image/*">
                        </div>
                        <small class="text-muted">Upload JPG, PNG, or GIF (max 5MB)</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <button type="submit" form="profileForm" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update Profile
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const profileForm = document.getElementById('profileForm');
    const profileAlert = document.getElementById('profileAlert');
    
    profileForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
        submitBtn.disabled = true;
        
        fetch('modals/profile_modal.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                // Refresh the page after a short delay to update the navbar
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'An error occurred while updating the profile.');
        })
        .finally(() => {
            // Restore button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
    
    function showAlert(type, message) {
        profileAlert.className = `alert alert-${type}`;
        profileAlert.textContent = message;
        profileAlert.style.display = 'block';
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            profileAlert.style.display = 'none';
        }, 5000);
    }
});
</script>