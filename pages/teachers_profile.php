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
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data will be dynamically populated here -->
        </tbody>
    </table>
</div>

<div class="mt-3">
    <a href="../export_faculty_excel.php" class="btn btn-success">Export to Excel</a>
</div>

</div>

<!-- Modal for Viewing Faculty Details -->
<div class="modal fade" id="facultyModal" tabindex="-1" aria-labelledby="facultyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="facultyModalLabel">Faculty Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="facultyDetails">
          <!-- Faculty details will be dynamically loaded here -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
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
                            user.email,
                            `<button class="btn btn-sm btn-primary view-btn" data-id="${user.id_no}" data-bs-toggle="modal" data-bs-target="#facultyModal">View</button>`
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

    // Fetch and display faculty details in the modal
    document.querySelector('#facultyTable').addEventListener('click', function (e) {
        if (e.target.classList.contains('view-btn')) {
            const facultyId = e.target.getAttribute('data-id');
            fetch(`../functions/get_faculty_details.php?id=${facultyId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const details = data.details;
                        const facultyDetails = `
                            <p><strong>DepEd ID No:</strong> ${details.deped_id_no}</p>
                            <p><strong>Last Name:</strong> ${details.last_name}</p>
                            <p><strong>First Name:</strong> ${details.first_name}</p>
                            <p><strong>Middle Name:</strong> ${details.middle_name || '-'}</p>
                            <p><strong>Status:</strong> ${details.status}</p>
                            <p><strong>Email:</strong> ${details.email}</p>
                            <p><strong>Position:</strong> ${details.position || '-'}</p>
                            <p><strong>Contact No:</strong> ${details.contact_no || '-'}</p>
                            <p><strong>Bachelor's Degree:</strong> ${details.bachelors_degree || '-'}</p>
                            <p><strong>Post Graduate:</strong> ${details.post_graduate || '-'}</p>
                            <p><strong>Major:</strong> ${details.major || '-'}</p>
                            <p><strong>Suffix:</strong> ${details.suffix || '-'}</p>
                            <p><strong>Date of Birth:</strong> ${details.date_of_birth || '-'}</p>
                            <p><strong>Birthplace:</strong> ${details.birthplace || '-'}</p>
                            <p><strong>Sex:</strong> ${details.sex || '-'}</p>
                            <p><strong>Employee No:</strong> ${details.employee_no || '-'}</p>
                            <p><strong>Plantilla No:</strong> ${details.plantilla_no || '-'}</p>
                            <p><strong>PhilHealth No:</strong> ${details.philhealth_no || '-'}</p>
                            <p><strong>BP No:</strong> ${details.bp_no || '-'}</p>
                            <p><strong>PAGIBIG No:</strong> ${details.pagibig_no || '-'}</p>
                            <p><strong>TIN No:</strong> ${details.tin_no || '-'}</p>
                            <p><strong>PRC No:</strong> ${details.prc_no || '-'}</p>
                            <p><strong>PRC Validity Date:</strong> ${details.prc_validity_date || '-'}</p>
                            <p><strong>PhilSys ID No:</strong> ${details.phlisys_id_no || '-'}</p>
                            <p><strong>Salary Grade:</strong> ${details.salary_grade || '-'}</p>
                            <p><strong>Current Step Based on Payslip:</strong> ${details.current_step_based_on_payslip || '-'}</p>
                            <p><strong>Date of First Appointment:</strong> ${details.date_of_first_appointment || '-'}</p>
                            <p><strong>Date of Latest Promotion:</strong> ${details.date_of_latest_promotion || '-'}</p>
                            <p><strong>First Day of Service:</strong> ${details.first_day_of_service || '-'}</p>
                            <p><strong>Retirement Day:</strong> ${details.retirement_day || '-'}</p>
                        `;
                        document.querySelector('#facultyDetails').innerHTML = facultyDetails;
                    } else {
                        document.querySelector('#facultyDetails').innerHTML = '<p>Error loading details.</p>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching faculty details:', error);
                    document.querySelector('#facultyDetails').innerHTML = '<p>Error loading details.</p>';
                });
        }
    });
});

</script>
