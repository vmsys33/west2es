<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);


session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit;
}

// Database connection
require_once '../functions/db_connection.php';
require_once '../functions/pageTitle.php';
?>



<?php
// Get the current file name
$currentPage = basename($_SERVER['PHP_SELF']);

// Call the function to get the page title
$pageTitle = getPageTitle($currentPage);

$stmt = $pdo->prepare("
    SELECT 
        af.id, 
        af.filename, 
        af.user_id, 
        ud.first_name, 
        afv.version_no,
        ud.last_name, 
        MAX(afv.version_no) AS latest_version, 
        afv.datetime
    FROM 
        rp_proposal_berf_files af
    LEFT JOIN 
        rp_proposal_berf_files_versions afv 
        ON af.id = afv.file_id
    LEFT JOIN 
        user_data ud 
        ON af.user_id = ud.id_no
    WHERE 
        1=1 -- Show all approved files (status is handled in pending_files table)
    GROUP BY 
        af.id, af.filename, af.user_id, ud.first_name, ud.last_name
");

$stmt->execute();
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/top-navbar.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="col-md-9 main-content">


<?php
// Include the function to get the file category
require_once '../functions/file_operations.php';

// Get the current page name
$pageName = basename($_SERVER['PHP_SELF'], ".php");

// Get the file category based on the page name
$fileCategory = getFileCategory($pageName);
?>

<h3 class="mb-3">
    <?php echo $fileCategory; ?>
</h3>



    

    <!-- <h3 class="mb-3">Administrative Files</h3> -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addFileModal">Add File</button>

    <h4 class="mb-3">List of Files</h4>
    <div class="table-responsive">
   <table id="filesTable" class="table table-bordered">
    <colgroup>
        <col width="5%"> <!-- No. -->
        <col width="20%"> <!-- Filename -->
        <col width="20%"> <!-- Date & Time -->
        <col width="10%"> <!-- Uploader -->
        <col width="15%"> <!-- Actions -->
    </colgroup>
    <thead>
        <tr>
            <th>#</th> <!-- Number -->
            <th>Name</th>
            <!-- <th>Latest Version</th> -->
            <th>Date & Time</th>
            <th>Uploader</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php $counter = 1; ?>
        <?php foreach ($files as $file): ?>
            <tr>
                <td><?= $counter++ ?></td> <!-- Numbering flag -->
                <td><?= htmlspecialchars($file['filename']) ?></td>
                <td><?= htmlspecialchars(date('F j, Y, h:i A', strtotime($file['datetime']))) ?></td>
                <td><?= htmlspecialchars($file['first_name']) ?></td>
                <td>
                    <button class="btn btn-info btn-sm preview-file" data-id="<?= $file['id'] ?>" data-table="<?= 'rp_proposal_berf_files_versions'?>" data-bs-toggle="modal" data-bs-target="#previewFileModal">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-success btn-sm edit-file" data-id="<?= $file['id'] ?>" data-table="<?= 'rp_proposal_berf_files_versions'?>" data-version="<?= $file['version_no'] ?>" data-bs-toggle="modal" data-bs-target="#editFileModal">
                        <i class="fas fa-edit"></i>
                    </button>
                    <!-- Add button for file revision -->
                    <button class="btn btn-primary btn-sm add-revision" data-id="<?= $file['id'] ?>" data-name="<?= $file['filename'] ?>" data-bs-toggle="modal" data-bs-target="#addFileRevisionModal">
                        <i class="fas fa-plus"></i>
                    </button>
                    <?php if ($_SESSION['user_role'] !== 'faculty'): ?>
                    <button class="btn btn-danger btn-sm delete-file" data-id="<?= $file['id'] ?>" data-table1="<?= 'rp_proposal_berf_files' ?>" data-table2="<?= 'rp_proposal_berf_files_versions' ?>" data-version="<?= $file['version_no'] ?>" >
                            <i class="fas fa-trash"></i>
                        </button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

    </div>
</div>

<!-- Add File Modal -->
<!-- <button class="btn btn-primary btn-sm download-file" data-url="${sanitizedPath}">
                    <i class="fas fa-download"></i>
                </button> -->

<div class="modal fade" id="addFileModal" tabindex="-1" aria-labelledby="addFileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addFileModalLabel">
                    <i class="fas fa-file-upload me-2"></i> Add New File
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addFileForm" enctype="multipart/form-data" action="../functions/file_functions/add_file.php" method="POST">
                    <div class="mb-4">
                        <label for="fileName" class="form-label fw-bold">File Name</label>
                        <input type="text" class="form-control form-control-lg" id="fileName" name="fileName" placeholder="Enter a descriptive file name" required>
                        <input type="hidden" name="table1" value="rp_proposal_berf_files">
                        <input type="hidden" name="table2" value="rp_proposal_berf_files_versions">
                        <small class="text-muted">Provide a clear and descriptive name for the file.</small>
                    </div>
                    <div class="mb-4">
                        <label for="fileInput" class="form-label fw-bold">Upload File</label>
                        <input type="file" class="form-control form-control-lg" id="fileInput" name="fileInput" required>
                        <small class="text-muted">Supported formats: PDF, DOCX, XLSX, etc. Max size: 10MB.</small>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Save File
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Preview File Modal -->
<div class="modal fade" id="previewFileModal" tabindex="-1" aria-labelledby="previewFileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="previewFileModalLabel">
                    <i class="fas fa-eye me-2"></i> File Revisions
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Modal Body -->
            <div class="modal-body">
                <!-- Table Section -->
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Version</th>
                                <th>Filename</th>
                                <th>Date & Time</th>
                                <th>File Size</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="revisionTableBody">
                            <!-- Revisions will be dynamically loaded here -->
                        </tbody>
                    </table>
                </div>
                <!-- Message for Empty State -->
                <div id="noRevisionsMessage" class="text-center text-muted mt-4 d-none">
                    <i class="fas fa-info-circle me-2"></i>No revisions available for this file.
                </div>
            </div>
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Edit File Modal -->
<div class="modal fade" id="editFileModal" tabindex="-1" aria-labelledby="editFileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="editFileModalLabel">
                    <i class="fas fa-edit me-2"></i> Edit File Revisions
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Modal Body -->
            <div class="modal-body">
                <h5 class="mb-4 fw-bold text-muted">
                    <i class="fas fa-history me-2"></i> File Revision History
                </h5>
                <div class="table-responsive">
                    <table id="revisionsTable" class="table table-hover table-bordered align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Version</th>
                                <th>Filename</th>
                                <th>Date & Time</th>
                                <th>File Size</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Revision rows will be dynamically loaded -->
                        </tbody>
                    </table>
                </div>
                <!-- Empty State Message -->
                <div id="noRevisionsMessage" class="text-center text-muted mt-4 d-none">
                    <i class="fas fa-info-circle me-2"></i>No revisions available.
                </div>
            </div>
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Revision Modal -->
<div class="modal fade" id="addFileRevisionModal" tabindex="-1" aria-labelledby="addFileRevisionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addFileRevisionModalLabel">
                    <i class="fas fa-plus me-2"></i> Add New Revision
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Modal Body -->
            <div class="modal-body">
                <form id="addRevisionForm" enctype="multipart/form-data">
                    <!-- Hidden Field -->
                    <input type="hidden" id="addFileId" name="file_id">
                    <input type="hidden" id="addFileName" name="file_name">
                    <input type="hidden" name="table1" value="rp_proposal_berf_files">
                    <input type="hidden" name="table2" value="rp_proposal_berf_files_versions">
                    
                    <!-- Upload Revision File -->
                    <div class="mb-4">
                        <label for="addRevisionFile" class="form-label fw-bold">Upload New Revision</label>
                        <input type="file" class="form-control form-control-lg" id="addRevisionFile" name="file" required>
                        <small class="text-muted">Supported formats: PDF, DOCX, XLSX, etc. Max size: 10MB.</small>
                        <div class="invalid-feedback">Please select a file to upload.</div>
                    </div>
                    <!-- Footer Buttons -->
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" id="addRevisionBtn">
                            <i class="fas fa-save me-1"></i> Add Revision
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>




<!-- Edit Revision Modal -->
<div class="modal fade" id="editRevisionModal" tabindex="-1" aria-labelledby="editRevisionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="editRevisionModalLabel">
                    <i class="fas fa-edit me-2"></i> Edit Revision
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Modal Body -->
            <div class="modal-body">
                <form id="editRevisionForm" enctype="multipart/form-data">
                    <!-- Hidden Field -->
                    <input type="hidden" id="editRevisionId" name="revision_id">
                    <input type="hidden" name="table2" value="rp_proposal_berf_files_versions">
                    <input type="hidden" name="table1" value="rp_proposal_berf_files">
                    <input type="hidden" name="table1" value="rp_proposal_berf_files">
                    <input type="hidden" id="editRevisionVersion" name="version_no" >

                    <!-- Upload New File -->
                    <div class="mb-4">
                        <label for="editRevisionFile" class="form-label fw-bold">Upload New File</label>
                        <input type="file" class="form-control form-control-lg" id="editRevisionFile" name="file" required>
                        <small class="text-muted">Replace the existing revision with a new file.</small>
                        <div class="invalid-feedback">Please select a file to upload.</div>
                    </div>
                    <!-- Footer Buttons -->
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Preview Document Modal -->
<div class="modal fade" id="previewDocModal" tabindex="-1" aria-labelledby="previewDocModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="previewDocModalLabel">
                    <i class="fas fa-file-alt me-2"></i> Preview Document
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="docPreviewIframe" src="" width="100%" height="600px" style="border: none;"></iframe>
            </div>
        </div>
    </div>
</div>


  <?php include '../includes/footer.php'; ?>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

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
    // $(document).ready(function () {
    //     $('.filesTable').DataTable();

    $(document).ready(function () {
    // Check if the DataTable is already initialized
    if (!$.fn.dataTable.isDataTable('#filesTable')) {
        // Initialize DataTable instance with responsive option
        $('#filesTable').DataTable({
            responsive: true  // Enable responsiveness
        });
    } else {
        // Optionally, if the DataTable is already initialized, re-enable responsiveness
        $('#filesTable').DataTable().responsive.recalc();
    }
});




        // Preview file revisions
        $('.preview-file').on('click', function () {
            const fileId = $(this).data('id');
            const fileTable = $(this).data('table');

            // Fetch revisions via AJAX
            $.get('../functions/file_functions/fetch_revisions.php', { file_id: fileId, file_table:fileTable }, function (response) {
                if (response.status === 'success') {
                    let revisions = '';
                    response.data.forEach(revision => {
                        // Extract file extension from filename
                        const fileExtension = revision.filename.split('.').pop().toLowerCase();

                        let viewerButton = ''; // Initialize the viewer button HTML
                        if (fileExtension === 'pdf') {
                          viewerButton = `
                            <a href="../view_pdf.php?file_path=${revision.file_path}" class="btn btn-secondary btn-sm" target="_blank"> 
                                <i class="fas fa-eye"></i>   
                            </a>`;

                        } else if (fileExtension === 'doc' || fileExtension === 'docx') {
                            // Word file viewer
                            viewerButton = `
                                <a href="../view_word.php?file_path=${revision.file_path}" class="btn btn-secondary btn-sm" target="_blank"> 
                                    <i class="fas fa-eye"></i>   
                                </a>`;
                                                            
                         } else if (fileExtension === 'xls' || fileExtension === 'xlsx') {
                        // Excel file viewer
                        viewerButton = `
                            <a href="../view_excel.php?file_path=${revision.file_path}" class="btn btn-secondary btn-sm" target="_blank"> 
                                <i class="fas fa-eye"></i>   
                            </a>`;

                        } else {
                            // Unsupported file type message
                            viewerButton = `
                                <button class="btn btn-danger btn-sm" disabled>
                                    <i class="fas fa-eye-slash"></i> Unsupported
                                </button>`;
                        }

                        revisions += `
                            <tr>
                                <td>Version ${revision.version_no}</td>
                                <td>${revision.filename}</td>
                                <td>${formatDateTime(revision.datetime)}</td>
                                <td>${revision.file_size}</td>
                                <td>
 <a href="${revision.file_path}" class="btn btn-sm btn-primary" download-file2><i class="fas fa-download"></i>
                                    </a>                                    ${viewerButton} <!-- Dynamically generated viewer button -->
                                </td>
                            </tr>
                        `;
                    });
                    $('#revisionTableBody').html(revisions);
                } else {
                    alert('Error fetching revisions: ' + response.message);
                }
            }, 'json');
        });
</script>



<!-- Edit saving of files -->
<script>
  $(document).ready(function () {
    // Edit button click to load revisions for a file
    $('.edit-file').on('click', function () {
        const fileId = $(this).data('id'); // Get the file 
        const fileTable = $(this).data('table');

        // Fetch revisions via AJAX
        $.get('../functions/file_functions/fetch_file_revisions.php', { file_id: fileId, file_table:fileTable }, function (response) {
            if (response.status === 'success') {
                // Populate revisions table
                let revisionsHtml = '';

                let maxVersion = 0;

                // Find the maximum version_no that is greater than 1
                response.data.forEach(revision => {
                    if (revision.version_no > 1 && revision.version_no > maxVersion) {
                        maxVersion = revision.version_no;
                    }
                });

                
                response.data.forEach(revision => {
                    revisionsHtml += `
                        <tr>
                            <td>${revision.version_no}</td>
                            <td>${revision.filename}</td>
                            <td>${formatDateTime(revision.datetime)}</td>
                            <td>${revision.file_size}</td>
                            <td>
                                <button class="btn btn-warning btn-sm edit-revision" data-id="${revision.file_id}" data-table1="rp_proposal_berf_files" data-table2="rp_proposal_berf_files_versions"
                                data-version="${revision.version_no}">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                 ${revision.version_no === maxVersion && revision.version_no > 1 ? 
                                `<button class="btn btn-danger btn-sm delete-revision" 
                                    data-table1="rp_proposal_berf_files" data-table2="rp_proposal_berf_files_versions" 
                                    data-id="${revision.file_id}" data-version="${revision.version_no}">
                                    <i class="fas fa-trash"></i> Delete
                                </button>` : ''}
                               
                            </td>
                        </tr>
                    `;
                });
                $('#revisionsTable tbody').html(revisionsHtml);

                // Add event listeners to the Edit buttons
                addEditButtonListeners();

                // Show the modal
                $('#editFileModal').modal('show');
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        }, 'json');
    });

    // Add click event listeners for the Edit Revision buttons
    function addEditButtonListeners() {
        $('.edit-revision').on('click', function () {
            const revisionId = $(this).data('id'); // Get the revision ID
            const revisionTable1 = $(this).data('table1');
            const revisionTable2 = $(this).data('table2');
            const revisionVersion = $(this).data('version');

            // Populate the hidden input field with the revision ID
            $('#editRevisionId').val(revisionId);
            $('#editRevisionVersion').val(revisionVersion);

            // Show the Edit Revision Modal
            $('#editRevisionModal').modal('show');
        });
    }

    // Save changes to a revision
    $('#editRevisionForm').on('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this); // Create a FormData object for file upload

        // Update revision via AJAX
        $.ajax({
            url: '../functions/file_functions/update_revision_file.php', // Backend script to handle file upload and update
            method: 'POST',
            data: formData,
            processData: false, // Required for file uploads
            contentType: false, // Required for file uploads
            success: function (response) {
                if (response.status === 'success') {
                    Swal.fire('Success', response.message, 'success').then(() => {
                        $('#editRevisionModal').modal('hide'); // Close the Edit Revision Modal
                        $('#editFileModal').modal('hide'); // Close the Edit File Modal
                        location.reload(); // Reload the page to reflect changes
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function () {
                Swal.fire('Error', 'An error occurred while updating the revision.', 'error');
            }
        });
    });
});

</script>


<script>
    $(document).on('click', '.delete-revision', function() {
    const revisionId = $(this).data('id');
    const revisionTable1 = $(this).data('table1');
    const revisionTable2 = $(this).data('table2');
    const revisionVersion = $(this).data('version');


    // Confirm deletion
    if (confirm('Are you sure you want to delete this revision?')) {
        $.ajax({
            url: '../functions/file_functions/delete_revision.php', // PHP file that handles deletion
            type: 'POST',
            data: { file_id: revisionId, file_table1: revisionTable1, file_table2: revisionTable2, file_version: revisionVersion },
            success: function(response) {
                if (response.status === 'success') {
                    alert('Revision deleted successfully!');
                    // Optionally, remove the row from the table
                    $(`#revisionsTable tbody tr td button[data-id="${revisionId}"]`).closest('tr').remove();
                    location.reload(); // Reload the page to reflect changes
                } else {
                    alert('Error deleting revision. Please try again.');
                }
            },
            error: function() {
                alert('An error occurred while trying to delete the revision.');
            }
        });
    }
});

</script>


<!-- Adding of files begin -->

<script>
  document.getElementById("addFileForm").addEventListener("submit", function (e) {
    e.preventDefault();

    // Disable the submit button to prevent multiple clicks
    const submitButton = document.querySelector("#addFileForm button[type='submit']");
    submitButton.disabled = true;
    submitButton.innerText = "Saving..."; // Optionally change the button text to indicate saving

    const formData = new FormData(this);

    fetch("../functions/file_functions/add_file.php", {
        method: "POST",
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.status === "success") {
                Swal.fire("Success", data.message, "success").then(() => {
                    
                    document.querySelector("#addFileModal .btn-close").click();
                     // Reload the page to reflect changes
                    location.reload();
                });
            } else {
                Swal.fire("Error", data.message, "error");
            }
        })
        .catch((error) => {
            Swal.fire("Error", "An unexpected error occurred.", "error");
        })
        .finally(() => {
            // Re-enable the submit button after the action is complete
            submitButton.disabled = false;
            submitButton.innerText = "Save File"; // Reset the button text
        });
});

</script>



<script>
    // Handle the Add Revision Form submission
    $('#addRevisionForm').on('submit', function (e) {
        e.preventDefault();

        

        const formData = new FormData(this); // Create a FormData object for file upload

        // Disable the button to prevent double-click submissions
        $('#addRevisionBtn').prop('disabled', true);
        $('#addRevisionBtn').html('<i class="fas fa-spinner fa-spin me-1"></i> Processing...'); // Change button text to indicate loading

        // Send the revision via AJAX
        $.ajax({
            url: '../functions/file_functions/add_revision_file.php', // Backend script to handle file upload and version increment
            method: 'POST',
            data: formData,
            processData: false, // Required for file uploads
            contentType: false, // Required for file uploads
            success: function (response) {
                if (response.status === 'success') {
                    Swal.fire('Success', response.message, 'success').then(() => {
                        $('#addRevisionModal').modal('hide'); // Close the Add Revision Modal
                        location.reload(); // Reload the page to reflect changes
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function () {
                Swal.fire('Error', 'An error occurred while adding the revision.', 'error');
            },
            complete: function () {
                // Re-enable the button once the AJAX request is complete
                $('#addRevisionBtn').prop('disabled', false);
                $('#addRevisionBtn').html('<i class="fas fa-save me-1"></i> Add Revision'); // Reset button text
            }
        });
    });
</script>



<script>
    // When the "Add Revision" button is clicked
$('.add-revision').on('click', function () {

    const fileId = $(this).data('id'); // Retrieve the data-id from the clicked button
    const fileName = $(this).data('name'); // Retrieve the data-id from the clicked button
    console.log('Captured File ID:', fileId); // Debug to ensure the correct file_id is captured
    $('#addFileId').val(fileId); // Assign the file_id to the hidden input in the modal
    $('#addFileName').val(fileName); // Assign the file_id to the hidden input in the modal
});

</script>

<script>
    $('#filesTable').DataTable().on('draw', function () {
    console.log('Table redrawn');
    
    // Rebind any custom actions, if needed
});

</script>

<script>
    $(document).on('click', '.delete-file', function () {
    const fileId = $(this).data('id'); // Get the file ID
    const fileTable1 = $(this).data('table1'); 
    const fileTable2 = $(this).data('table2');
    const fileVersion = $(this).data('version');
    console.log('Deleting file ID:', fileId); // Debugging
    

    Swal.fire({
        title: 'Are you sure?',
        text: 'This will delete the file and all its revisions. This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Send AJAX request to delete the file
            $.ajax({
                url: '../functions/file_functions/delete_file.php',
                method: 'POST',
                data: { file_id: fileId, file_table1: fileTable1, file_table2: fileTable2, file_version: fileVersion },
                success: function (response) {
                    if (response.status === 'success') {
                        Swal.fire('Deleted!', response.message, 'success').then(() => {
                            // Reload the DataTable
                            location.reload(); // Reload the page to reflect changes
                        });
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error!', 'An unexpected error occurred while deleting the file.', 'error');
                }
            });
        }
    });
});

</script>



<script>

$(document).on('click', '.download-file', function () {
    const button = $(this); // Reference to the clicked button
    const fileUrl = button.data('url'); // Retrieve the file URL
    const fileName = fileUrl.split('/').pop(); // Extract file name

    Swal.fire({
        title: 'Download Confirmation',
        text: `Do you want to download ${fileName}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, download it!',
        cancelButtonText: 'No, cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loader
            button.find('.loader').show();
            button.prop('disabled', true); // Disable the button

            const downloadUrl = `../functions/file_functions/download_file.php?file=${encodeURIComponent(fileUrl)}`;
            
            // Simulate a delay for the loader (if needed for testing purposes)
            setTimeout(() => {
                window.location.href = downloadUrl; // Trigger download

                // Hide loader and enable button after download trigger
                button.find('.loader').hide();
                button.prop('disabled', false);
            }, 9000); // Optional delay
        }
    });
});

</script>