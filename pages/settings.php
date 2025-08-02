<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit;
}

require_once '../functions/pageTitle.php';
require_once '../functions/db_connection.php';

$currentPage = basename($_SERVER['PHP_SELF']);
$pageTitle = getPageTitle($currentPage);

// Fetch settings from the database
$stmt = $pdo->prepare("SELECT website_name, email_address, school_logo FROM general_setting");
$stmt->execute();
$row = $stmt->fetch();

$websiteName = $row['website_name'] ?? "";
$emailAddress = $row['email_address'] ?? "";
$schoolLogo = $row['school_logo'] ?? "";
$adminName = $row['admin_name'] ?? "";

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['general_settings'])) {
        $websiteName = $_POST['website_name'];
        $emailAddress = $_POST['email_address'];
        $adminName = $_POST['admin_name'];

        // Handle file upload for the school logo
        if (!empty($_FILES['school_logo']['name'])) {
            $targetDir = "../uploads/";
            $schoolLogo = basename($_FILES['school_logo']['name']);
            $targetFilePath = $targetDir . $schoolLogo;
            move_uploaded_file($_FILES['school_logo']['tmp_name'], $targetFilePath);
        }

        // Update settings in the database
        $stmt = $pdo->prepare("UPDATE general_setting SET website_name = ?, email_address = ?, school_logo = ?, admin_name = ? WHERE id = 1");
        $stmt->execute([$websiteName, $emailAddress, $schoolLogo, $adminName]);
    } elseif (isset($_POST['admin_profile'])) {
        $depedIdNo = $_POST['deped_id_no'];
        $lastName = $_POST['last_name'];
        $firstName = $_POST['first_name'];
        $middleName = $_POST['middle_name'];
        $adminEmail = $_POST['email'];

        // Update admin profile in the user_data table
        $stmt = $pdo->prepare("UPDATE user_data SET deped_id_no = ?, last_name = ?, first_name = ?, middle_name = ?, email = ? WHERE id_no = ? AND role = 'admin'");
        $stmt->execute([$depedIdNo, $lastName, $firstName, $middleName, $adminEmail, $adminId]);
        
        // Handle admin photo upload if provided
        if (!empty($_FILES['admin_photo']['name'])) {
            require_once '../functions/upload_user_photo.php';
            $photoResult = uploadUserPhoto($adminId, $_FILES['admin_photo']);
            if (!$photoResult['success']) {
                // Log the error but don't stop the process
                error_log("Admin photo upload failed: " . $photoResult['message']);
            }
        }
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/top-navbar.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="col-md-9 main-content">
    <h1 class="text-center mb-4">Settings</h1>
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white text-center">
            <h3>General Settings</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="general_settings" value="1">
                <div class="mb-3">
                    <label for="website_name" class="form-label">Website Name</label>
                    <input type="text" name="website_name" id="website_name" class="form-control" value="<?php echo htmlspecialchars($websiteName); ?>">
                </div>
                <div class="mb-3">
                    <label for="email_address" class="form-label">Email Address</label>
                    <input type="email" name="email_address" id="email_address" class="form-control" value="<?php echo htmlspecialchars($emailAddress); ?>">
                </div>
                <div class="mb-3">
                    <label for="school_logo" class="form-label">School Logo</label>
                    <input type="file" name="school_logo" id="school_logo" class="form-control">
                    <?php if (!empty($schoolLogo)): ?>
                        <div class="mt-2">
                            <img src="../uploads/<?php echo htmlspecialchars($schoolLogo); ?>" alt="School Logo" class="img-fluid rounded" style="max-height: 100px;">
                        </div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="admin_name" class="form-label">Admin Name</label>
                    <input type="text" name="admin_name" id="admin_name" class="form-control" value="<?php echo htmlspecialchars($adminName); ?>">
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg">Save Settings</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white text-center">
            <h3>Admin Profile</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="admin_profile" value="1">
                <div class="mb-3">
                    <label for="deped_id_no" class="form-label">DepEd ID No</label>
                    <input type="text" name="deped_id_no" id="deped_id_no" class="form-control" value="<?php echo htmlspecialchars($depedIdNo); ?>">
                </div>
                <div class="mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" name="last_name" id="last_name" class="form-control" value="<?php echo htmlspecialchars($lastName); ?>">
                </div>
                <div class="mb-3">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" name="first_name" id="first_name" class="form-control" value="<?php echo htmlspecialchars($firstName); ?>">
                </div>
                <div class="mb-3">
                    <label for="middle_name" class="form-label">Middle Name</label>
                    <input type="text" name="middle_name" id="middle_name" class="form-control" value="<?php echo htmlspecialchars($middleName); ?>">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($adminEmail); ?>">
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
                <div class="text-center">
                    <button type="submit" class="btn btn-secondary btn-lg">Update Profile</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
