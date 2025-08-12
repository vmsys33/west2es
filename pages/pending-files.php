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

include '../includes/header.php'; 
include '../includes/top-navbar.php'; 
include '../includes/sidebar.php'; 
?>

<div class="col-md-9 main-content">
    <h1>Pending Files</h1>
    <table id="adminFilesTable" class="display" style="width:100%">
    <colgroup>
        <col width="5%"> <!-- No. -->
        <col width="15%"> <!-- Filename -->
        <col width="15%"> <!-- Date & Time -->
        <col width="5%"> <!-- Uploader -->
        <col width="10%"> <!-- Actions -->
        <col width="10%"> <!-- Uploader -->
        <col width="15%"> <!-- Actions -->
    </colgroup>

        <thead>
            <tr>
                <th>#</th>
                <th>User Label</th>
                <th>Actual Filename</th>
                <th>Version</th>
                <th>Date/Time</th>
                <th>File Size</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>




<?php include '../includes/footer.php'; ?>

<style>
/* Mobile responsive styles for DataTable */
@media (max-width: 768px) {
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        text-align: left;
        margin-bottom: 10px;
    }
    
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        text-align: center;
        margin-top: 10px;
    }
    
    /* Make buttons smaller on mobile */
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    /* Ensure table doesn't overflow */
    .dataTables_wrapper {
        overflow-x: auto;
    }
    
    /* Hide responsive controls on desktop */
    @media (min-width: 769px) {
        .dtr-control {
            display: none !important;
        }
    }
}

/* Improve modal display on mobile */
.dtr-modal {
    max-width: 95vw;
    margin: 10px auto;
}

.dtr-modal .dtr-modal-content {
    padding: 15px;
}

.dtr-modal .dtr-modal-close {
    top: 10px;
    right: 15px;
}
</style>


<!-- Include DataTable Scripts -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>


<script>
  function formatDateTime(datetime) {
    const date = new Date(datetime); // Convert string to a Date object

    // Format the date to "Month Day, Year Hour:Minute AM/PM"
    const options = { 
      month: 'long', 
      day: 'numeric', 
      year: 'numeric', 
      hour: 'numeric', 
      minute: 'numeric', 
      hour12: true // Use 12-hour clock
    };
    
    return date.toLocaleString('en-US', options);
  }
</script>


<script>
    $(document).ready(function () {
    // Ensure the element exists
    if ($('#adminFilesTable').length === 0) {
        console.error('Table #adminFilesTable not found');
        return;
    }

    const table = $('#adminFilesTable').DataTable({
        responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.modal({
                    header: function (row) {
                        var data = row.data();
                        return 'Details for ' + data.name;
                    }
                }),
                renderer: $.fn.dataTable.Responsive.renderer.tableAll()
            },
            breakpoints: [
                { name: 'desktop', width: Infinity },
                { name: 'tablet', width: 1024 },
                { name: 'phone', width: 480 }
            ]
        },
        ajax: {
            url: '../functions/fetchPendingFiles.php',
            dataSrc: '',
            error: function (xhr, error, thrown) {
                console.error('Error loading data:', error);
                console.log('Response:', xhr.responseText);
            }
        },
        columns: [
            {
            data: null,
            render: function (data, type, row, meta) {
                return meta.row + 1; // Incremental counter
            }
        },
            { 
                data: 'filename',
                render: function (data, type, row) {
                    // Show the actual filename with extension
                    return data || 'N/A';
                }
            },
            { 
                data: 'name',
                render: function (data, type, row) {
                    // Show the user-friendly label
                    return data || 'N/A';
                }
            },
            { data: 'version_no' },
             { 
                data: 'datetime',
                render: function (data) {
                    return formatDateTime(data); // Pass the actual datetime value here
                }
            },
            { data: 'file_size' },
            {
                data: null,
                render: function (data, type, row) {


                    // Extract file extension from filename
                        const fileExtension = row.filename.split('.').pop().toLowerCase();

                        let viewerButton = ''; // Initialize the viewer button HTML
                        if (fileExtension === 'pdf') {
                          viewerButton = `
                            <a href="../view_pdf.php?file_path=${row.file_path}" class="btn btn-secondary btn-sm" target="_blank"> 
                                <i class="fas fa-eye"></i>   
                            </a>`;

                        } else if (fileExtension === 'doc' || fileExtension === 'docx') {
                            // Word file viewer
                            viewerButton = `
                                <a href="../view_word.php?file_path=${row.file_path}" class="btn btn-secondary btn-sm" target="_blank"> 
                                    <i class="fas fa-eye"></i>   
                                </a>`;
                                                            
                         } else if (fileExtension === 'xls' || fileExtension === 'xlsx') {
                        // Excel file viewer
                        viewerButton = `
                            <a href="../view_excel.php?file_path=${row.file_path}" class="btn btn-secondary btn-sm" target="_blank"> 
                                <i class="fas fa-eye"></i>   
                            </a>`;

                        } else {
                            // Unsupported file type message
                            viewerButton = `
                                <button class="btn btn-danger btn-sm" disabled>
                                    <i class="fas fa-eye-slash"></i> Unsupported
                                </button>`;
                        }

                    return `
                        <button class="btn-approve" data-id="${row.id}" data-status="${row.status}">
                            <i class="fa fa-check"></i>
                        </button>
                        ${viewerButton}
                        <button class="btn-delete" data-id="${row.id}">
                            <i class="fa fa-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        columnDefs: [
            { orderable: false, targets: [0, 6] }, // Counter, Action
            { searchable: false, targets: [0, 6] },
            { responsivePriority: 1, targets: [1, 2, 6] }, // Actual Filename, User Label, Actions
            { responsivePriority: 2, targets: [4] }, // Date/Time
            { responsivePriority: 3, targets: [3, 5] }, // Version, File Size
            { className: "text-center", targets: [0, 6] }
        ]
    });


$('#adminFilesTable').on('click', '.btn-approve', function () {
    const button = $(this); // Reference the button clicked
    const id = button.data('id');
    const currentStatus = button.data('status'); // Get the current status
    const newStatus = currentStatus === 'pending' ? 'approve' : 'pending';

    // SweetAlert confirmation
    Swal.fire({
        title: 'Approval Confirmation',
        text: `Are you sure you want to ${newStatus} this file?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, approve it',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            // Disable the button to prevent multiple clicks
            button.prop('disabled', true);

            $.ajax({
                url: '../functions/toggleStatus.php',
                method: 'POST',
                data: { id, status: newStatus },
                success: function (response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        Swal.fire('Success!', 'The file has been approved successfully.', 'success');
                        table.ajax.reload(); // Reload 
                        location.reload();
                    } else {
                        Swal.fire('Error!', result.message || 'Failed to approve the file.', 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error!', 'An error occurred while approving the file.', 'error');
                },
                complete: function () {
                    // Re-enable the button after the request completes
                    button.prop('disabled', false);
                }
            });
        }
    });
});



 // Delete Button with SweetAlert
    $('#adminFilesTable').on('click', '.btn-delete', function () {
        const button = $(this); // Reference the button clicked
        const id = button.data('id');

        // SweetAlert confirmation for deletion
        Swal.fire({
            title: 'Are you sure?',
            text: 'This action cannot be undone. Do you want to delete this file?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                // Disable the button to prevent multiple clicks
                button.prop('disabled', true);

                $.ajax({
                    url: '../functions/deletePendingFile.php',
                    method: 'POST',
                    data: { id },
                    success: function () {
                        Swal.fire('Deleted!', 'The file has been deleted successfully.', 'success');
                        table.ajax.reload(); // Reload the table
                        location.reload();
                    },
                    error: function () {
                        Swal.fire('Error!', 'An error occurred while deleting the file.', 'error');
                    },
                    complete: function () {
                        // Re-enable the button after the request completes
                        button.prop('disabled', false);
                    }
                });
            }
        });
    });

 });  

</script>


