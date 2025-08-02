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

// Fetch mission and vision from the database
$stmt = $pdo->prepare("SELECT mission, vision FROM schoolmissionvision WHERE id = 1");
$stmt->execute();
$row = $stmt->fetch();

$mission = $row['mission'] ?? "";
$vision = $row['vision'] ?? "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mission = $_POST['mission'];
    $vision = $_POST['vision'];

    // Update mission and vision in the database
    $stmt = $pdo->prepare("UPDATE schoolmissionvision SET mission = ?, vision = ?, updated_at = NOW() WHERE id = 1");
    $stmt->execute([$mission, $vision]);
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/top-navbar.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="col-md-9 main-content">
    <h1 class="text-center">School Mission and Vision</h1>
    <form method="POST" action="">
        <div class="card mb-4">
            <div class="card-header text-center">
                <h2>SSES Mission</h2>
            </div>
            <div class="card-body">
                <textarea name="mission" class="form-control" rows="5" placeholder="Enter the mission statement here..."><?php echo htmlspecialchars($mission); ?></textarea>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header text-center">
                <h2>SSES Vision</h2>
            </div>
            <div class="card-body">
                <textarea name="vision" class="form-control" rows="5" placeholder="Enter the vision statement here..."><?php echo htmlspecialchars($vision); ?></textarea>
            </div>
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="../uploads/MISSION_VISION.pdf" target="_blank" class="btn btn-secondary">View PDF</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
