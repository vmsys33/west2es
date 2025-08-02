<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit;
}
?>

<?php
// Get the current file name
$currentPage = basename($_SERVER['PHP_SELF']);

require_once '../functions/pageTitle.php';
$pageTitle = getPageTitle($currentPage);
?>


<?php include '../includes/header.php'; ?>
<?php include '../includes/top-navbar.php'; ?>
<?php include '../includes/sidebar.php'; ?>


   <div class="col-md-9 main-content">
    <h3 class="mb-3">Pending Users</h3>
    <div class="table-responsive">
        <table class="table table-bordered" id="pendingUsersTable">
            <thead>
                <tr>
                    <th>DEPED No</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be dynamically loaded here -->
            </tbody>
        </table>
    </div>
</div>


    

  <?php include '../includes/footer.php'; ?>