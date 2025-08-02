<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit;
}
?>


<?php
$user_role = $_SESSION['user_role'];
echo "<body data-user-role='$user_role'>";

// Get the current file name
$currentPage = basename($_SERVER['PHP_SELF']);

require_once '../functions/pageTitle.php';
$pageTitle = getPageTitle($currentPage);
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/top-navbar.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="col-md-9 main-content">
    <!-- Tabs for BERF and NonBERF -->
    <ul class="nav nav-tabs" id="researchTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="berf-tab" data-bs-toggle="tab" href="#berf" role="tab" aria-controls="berf" aria-selected="true">BERF</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="nonberf-tab" data-bs-toggle="tab" href="#nonberf" role="tab" aria-controls="nonberf" aria-selected="false">NonBERF</a>
        </li>
    </ul>

    <div class="tab-content" id="researchTabsContent">
        <!-- BERF Tab Content -->
        <div class="tab-pane fade show active" id="berf" role="tabpanel" aria-labelledby="berf-tab">
            <h3>Research Paper/Proposal - BERF</h3>
            <!-- Include BERF PHP Page -->
            <?php include '../includes/rp_proposal_berf_files.php'; ?>
        </div>

        <!-- NonBERF Tab Content -->
        <div class="tab-pane fade" id="nonberf" role="tabpanel" aria-labelledby="nonberf-tab">
            <h3>Research Paper/Proposal - NonBERF</h3>
            <!-- Include NonBERF PHP Page -->
            <?php include 'rp_proposal_nonberf_files.php'; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
