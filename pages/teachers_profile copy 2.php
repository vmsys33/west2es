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

<h4 class="mb-3">List of Faculty Members</h4>
<div class="table-responsive">
    <table id="facultyTable" class="table table-bordered">
        <thead>
            <tr>
                <th>DepEd ID No</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Status</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data will be dynamically populated here -->
        </tbody>
    </table>
</div>
</div>




  <?php include '../includes/footer.php'; ?>


<script>
    document.addEventListener('DOMContentLoaded', function () {
    // Initialize DataTable for faculty members
    let facultyTableInstance = $('#facultyTable').DataTable();

    // Load Faculty Data
    const loadFacultyData = () => {
        fetch('../functions/fetch_faculty.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Clear existing rows in DataTable
                    facultyTableInstance.clear();

                    // Add rows dynamically
                    data.data.forEach(user => {
                        facultyTableInstance.row.add([
                            user.deped_id_no,
                            user.last_name,
                            user.first_name,
                            user.middle_name || '-', // Handle null values
                            user.status,
                            user.email
                        ]);
                    });

                    // Redraw DataTable to reflect changes
                    facultyTableInstance.draw();
                } else {
                    console.error('Error:', data.message);
                }
            })
            .catch(error => console.error('Error fetching faculty data:', error));
    };

    // Load faculty data when the page is loaded
    loadFacultyData();
});

</script>