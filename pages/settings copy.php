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
$stmt = $pdo->prepare("SELECT website_name, email_address, school_logo, admin_name FROM general_setting WHERE id = 1");
$stmt->execute();
$row = $stmt->fetch();

$websiteName = $row['website_name'] ?? "";
$emailAddress = $row['email_address'] ?? "";
$schoolLogo = $row['school_logo'] ?? "";
$adminName = $row['admin_name'] ?? "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/top-navbar.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="col-md-9 main-content">
    <h1 class="text-center mb-4">Settings</h1>
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white text-center">
            <h3>General Settings</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="" enctype="multipart/form-data">
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
</div>

<?php include '../includes/footer.php'; ?>
