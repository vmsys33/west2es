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
    <table id="facultyTable" class="table table-bordered table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th class="text-center">Photo</th>
                <th>DepEd ID No</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Status</th>
                <th>Email</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data will be dynamically populated here -->
        </tbody>
    </table> 
</div>

<style>
.faculty-photo {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #dee2e6;
}

.faculty-photo-placeholder {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 14px;
}

/* DataTable responsive styles - only affects mobile/tablet */
@media (max-width: 768px) {
    .dtr-details {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 1rem;
        margin-top: 0.5rem;
    }

    .dtr-details li {
        border-bottom: 1px solid #dee2e6;
        padding: 0.5rem 0;
    }

    .dtr-details li:last-child {
        border-bottom: none;
    }

    .dtr-title {
        font-weight: 600;
        color: #495057;
        min-width: 120px;
        display: inline-block;
    }

    /* Custom styling for DataTable's responsive controls */
    .dtr-control {
        cursor: pointer;
        text-align: center;
        color: #007bff;
    }

    .dtr-control:before {
        content: "⊕";
        font-size: 1.2em;
        font-weight: bold;
    }

    .dtr-control.dtr-expanded:before {
        content: "⊖";
    }
}

/* Hide responsive controls on desktop */
@media (min-width: 769px) {
    .dtr-control {
        display: none !important;
    }
}

/* Improve mobile experience */
@media (max-width: 768px) {
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        text-align: center;
        margin-bottom: 1rem;
    }
    
    .dataTables_wrapper .dataTables_paginate {
        text-align: center;
    }
    
    .dataTables_wrapper .dataTables_info {
        text-align: center;
        margin-top: 0.5rem;
    }
}

@media (max-width: 576px) {
    .faculty-photo,
    .faculty-photo-placeholder {
        width: 30px;
        height: 30px;
        font-size: 12px;
    }
    
    .badge {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
    }
    
    .btn-sm {
        padding: 0.25rem 0.4rem;
        font-size: 0.75rem;
    }
}
</style>

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
    // Initialize DataTable for faculty members with DataTable's built-in responsive features
    let facultyTableInstance = $('#facultyTable').DataTable({
        responsive: {
            details: {
                type: 'column',
                target: 'tr'
            },
            breakpoints: [
                { name: 'bigdesktop', width: Infinity },
                { name: 'meddesktop', width: 1480 },
                { name: 'desktop', width: 1024 },
                { name: 'tablet', width: 768 },
                { name: 'phone', width: 480 }
            ]
        },
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        order: [[2, 'asc']], // Sort by last name
        columnDefs: [
            { 
                orderable: false, 
                targets: [0, 7] // Disable sorting for Photo and Action columns
            },
            { 
                searchable: false, 
                targets: [0, 7] // Disable search for Photo and Action columns
            },
            { 
                responsivePriority: 1, 
                targets: [0, 1, 2, 3, 7] // Always show Photo, DepEd ID, Names, Actions on desktop
            },
            { 
                responsivePriority: 2, 
                targets: [5] // Then Status
            },
            { 
                responsivePriority: 3, 
                targets: [4, 6] // Finally Middle Name, Email (collapse only on very small screens)
            },
            { 
                className: "text-center", 
                targets: [0, 7] // Center align Photo and Action columns
            }
        ],
        language: {
            search: "Search faculty:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ faculty members",
            infoEmpty: "No faculty members found",
            infoFiltered: "(filtered from _MAX_ total entries)",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });

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
                        // Create photo HTML
                        let photoHtml = '';
                        if (user.photo && user.photo.trim() !== '') {
                            photoHtml = `<img src="../uploads/user_photos/${user.photo}" alt="Photo" class="faculty-photo" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="faculty-photo-placeholder" style="display:none;">${user.first_name.charAt(0)}</div>`;
                        } else {
                            photoHtml = `<div class="faculty-photo-placeholder">${user.first_name.charAt(0)}</div>`;
                        }

                        facultyTableInstance.row.add([
                            `<div class="text-center">${photoHtml}</div>`,
                            user.deped_id_no,
                            user.last_name,
                            user.first_name,
                            user.middle_name || '-', // Handle null values
                            `<span class="badge ${user.status === 'active' ? 'bg-success' : user.status === 'pending' ? 'bg-warning' : 'bg-secondary'}">${user.status}</span>`,
                            user.email,
                            `<div class="text-center">
                                <button class="btn btn-sm btn-primary view-btn me-1" data-id="${user.id_no}" data-bs-toggle="modal" data-bs-target="#facultyModal">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="${user.id_no}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>`
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
        if (e.target.classList.contains('delete-btn')) {
            const facultyId = e.target.getAttribute('data-id');
            if (confirm('Are you sure you want to delete this faculty member?')) {
                fetch(`../functions/delete_faculty.php?id=${facultyId}`, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('Deleted!', 'Faculty member deleted successfully.', 'success');
                        loadFacultyData(); // Refresh the table
                    } else {
                        Swal.fire('Error!', 'Error deleting faculty member: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'Error deleting faculty member.', 'error');
                    console.error(error);
                });
            }
        }
    });
});

</script>

<!-- Add SweetAlert2 script (latest stable version) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
