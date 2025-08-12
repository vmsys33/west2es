<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit;
}

// Get the current file name
$currentPage = basename($_SERVER['PHP_SELF']);

require_once '../functions/pageTitle.php';
$pageTitle = getPageTitle($currentPage);

// Database connection
require_once '../functions/db_connection.php';

// Fetch counts for pending users, pending files, and notifications
$stmt = $pdo->prepare("SELECT COUNT(*) AS pending_count FROM user_data WHERE status = 'pending'");
$stmt->execute();
$pendingUsersCount = $stmt->fetch(PDO::FETCH_ASSOC)['pending_count'];

$stmt = $pdo->prepare("SELECT COUNT(*) AS pending_count FROM pending_files WHERE status = 'pending'");
$stmt->execute();
$pendingFilesCount = $stmt->fetch(PDO::FETCH_ASSOC)['pending_count'];

$stmt = $pdo->prepare("SELECT COUNT(*) AS unseen_count FROM notifications WHERE seen = 0");
$stmt->execute();
$notificationCount = $stmt->fetch(PDO::FETCH_ASSOC)['unseen_count'];

// Fetch total users
$stmt = $pdo->prepare("SELECT COUNT(*) AS total_users FROM user_data");
$stmt->execute();
$totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];

// Fetch total files from tables ending with 'files_versions'
$tables = [
    'admin_files_versions', 'aeld_files_versions', 'cild_files_versions', 
    'if_completed_files_versions', 'rp_completed_berf_files_versions', 
    'rp_completed_nonberf_files_versions', 'rp_proposal_berf_files_versions', 
    'rp_proposal_nonberf_files_versions', 'lulr_files_versions', 
    't_lr_files_versions', 't_pp_files_versions', 't_rs_files_versions'
];
$totalFilesVersions = 0;
foreach ($tables as $table) {
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM $table");
    $stmt->execute();
    $totalFilesVersions += $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

// Fetch total files across all tables
$totalFiles = 0;
$query = $pdo->query("SHOW TABLES");
$tables = $query->fetchAll(PDO::FETCH_COLUMN);

foreach ($tables as $table) {
    if (strpos($table, 'files') !== false) {  // Check if the table name contains 'files'
        $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM $table");
        $stmt->execute();
        $totalFiles += $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}

// Fetch total events
$stmt = $pdo->prepare("SELECT COUNT(*) AS total_events FROM events");
$stmt->execute();
$totalEvents = $stmt->fetch(PDO::FETCH_ASSOC)['total_events'];
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/top-navbar.php'; ?>
<?php include '../includes/sidebar.php'; ?>




<div class="col-md-9 main-content">
    <div class="row g-3">
        <!-- Each box is now col-md-6 (2 in a row) with custom styling to ensure equal size -->
        <div class="col-md-6">
            <div class="info-box">
                <i class="fas fa-calendar-alt"></i>
                <span class="info-label">TOTAL</span>
                <span class="info-value"><?php echo $totalFiles; ?></span>
            </div>
        </div>
        <div class="col-md-6">
            <div class="info-box">
                <i class="fas fa-chart-line"></i>
                <span class="info-label">TOTAL EVENTS</span>
                <span class="info-value"><?php echo $totalEvents; ?></span>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="info-box">
                <i class="fas fa-user"></i>
                <span class="info-label">PENDING<br>USERS</span>
                <span class="info-value">
                    <a href="pending-users.php" class="text-dark notification-link" data-auto-mark="true" style="text-decoration: none;">
                        <span class="pending-users-count"><?php echo $pendingUsersCount; ?></span>
                    </a>
                </span>
            </div>
        </div>
        <div class="col-md-6">
            <div class="info-box">
                <i class="fas fa-file-alt"></i>
                <span class="info-label">PENDING<br>FILES</span>
                <span class="info-value">
                    <a href="pending-files.php" class="text-dark notification-link" data-auto-mark="true" style="text-decoration: none;">
                        <span class="pending-files-count"><?php echo $pendingFilesCount; ?></span>
                    </a>
                </span>
            </div>
        </div>
        <div class="col-md-6">
            <div class="info-box">
                <i class="fas fa-folder"></i>
                <span class="info-label">TOTAL FILES</span>
                <span class="info-value"><?php echo $totalFilesVersions; ?></span>
            </div>
        </div>
        <div class="col-md-6">
            <div class="info-box">
                <i class="fas fa-bell"></i>
                <span class="info-label">NOTIFICATIONS</span>
                <span class="info-value">
                    <a href="notification.php" class="text-dark notification-link" data-auto-mark="true" style="text-decoration: none;">
                        <span class="notification-count"><?php echo $notificationCount; ?></span>
                    </a>
                </span>
            </div>
        </div>
        <div class="col-md-6 offset-md-3">
            <div class="info-box">
                <i class="fas fa-users"></i>
                <span class="info-label">TOTAL<br>USERS</span>
                <span class="info-value"><?php echo $totalUsers; ?></span>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
