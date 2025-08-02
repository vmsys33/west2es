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
                <th>Name</th>
                <th>Filename</th>
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


<!-- Include DataTable Scripts -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>


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
            { data: 'name' },
            { data: 'filename' },
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


