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

// Fetch settings from the database
$stmt = $pdo->prepare("SELECT website_name, email_address, school_logo, admin_name FROM general_setting WHERE id = 1");
$stmt->execute();
$row = $stmt->fetch();

$websiteName = $row['website_name'] ?? "";
$emailAddress = $row['email_address'] ?? "";
$schoolLogo = $row['school_logo'] ?? "";
$adminName = $row['admin_name'] ?? "";

// Fetch admin profile data from user_data table
$adminId = $_SESSION['user_id']; // Assuming admin_id is stored in the session
$stmt = $pdo->prepare("SELECT deped_id_no, last_name, first_name, middle_name, email FROM user_data WHERE id_no = ? AND role = 'admin'");
$stmt->execute([$adminId]);
$adminProfile = $stmt->fetch();

$depedIdNo = $adminProfile['deped_id_no'] ?? "";
$lastName = $adminProfile['last_name'] ?? "";
$firstName = $adminProfile['first_name'] ?? "";
$middleName = $adminProfile['middle_name'] ?? "";
$adminEmail = $adminProfile['email'] ?? "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['general_settings'])) {
        $websiteName = $_POST['website_name'];
        $emailAddress = $_POST['email_address'];
        $adminName = $_POST['admin_name'];

        // Handle file upload for the school logo
        if (!empty($_FILES['school_logo']['name'])) {
            $targetDir = "../uploads/";
            $schoolLogo = basename($_FILES['school_logo']['name']);
            $targetFilePath = $targetDir . $schoolLogo;
            move_uploaded_file($_FILES['school_logo']['tmp_name'], $targetFilePath);
        }

        // Update settings in the database
        $stmt = $pdo->prepare("UPDATE general_setting SET website_name = ?, email_address = ?, school_logo = ?, admin_name = ? WHERE id = 1");
        $stmt->execute([$websiteName, $emailAddress, $schoolLogo, $adminName]);
    } elseif (isset($_POST['admin_profile'])) {
        $depedIdNo = $_POST['deped_id_no'];
        $lastName = $_POST['last_name'];
        $firstName = $_POST['first_name'];
        $middleName = $_POST['middle_name'];
        $adminEmail = $_POST['email'];

        // Update admin profile in the user_data table
        $stmt = $pdo->prepare("UPDATE user_data SET deped_id_no = ?, last_name = ?, first_name = ?, middle_name = ?, email = ? WHERE id_no = ? AND role = 'admin'");
        $stmt->execute([$depedIdNo, $lastName, $firstName, $middleName, $adminEmail, $adminId]);
        
        // Handle admin photo upload if provided
        if (!empty($_FILES['admin_photo']['name'])) {
            require_once '../functions/upload_user_photo.php';
            $photoResult = uploadUserPhoto($adminId, $_FILES['admin_photo']);
            if (!$photoResult['success']) {
                // Log the error but don't stop the process
                error_log("Admin photo upload failed: " . $photoResult['message']);
            }
        }
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/top-navbar.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="col-md-9 main-content">
    <h1 class="text-center mb-4">Settings</h1>
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white text-center">
            <h3>General Settings</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="general_settings" value="1">
                <div class="mb-3">
                    <label for="website_name" class="form-label">Website Name</label>
                    <input type="text" name="website_name" id="website_name" class="form-control" value="<?php echo htmlspecialchars($websiteName); ?>">
                </div>
                <div class="mb-3">
                    <label for="email_address" class="form-label">Email Address</label>
                    <input type="email" name="email_address" id="email_address" class="form-control" value="<?php echo htmlspecialchars($emailAddress); ?>">
                </div>
                <div class="mb-3">
                    <label for="school_logo" class="form-label">School Logo</label>
                    <input type="file" name="school_logo" id="school_logo" class="form-control">
                    <?php if (!empty($schoolLogo)): ?>
                        <div class="mt-2">
                            <img src="../uploads/<?php echo htmlspecialchars($schoolLogo); ?>" alt="School Logo" class="img-fluid rounded" style="max-height: 100px;">
                        </div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="admin_name" class="form-label">Admin Name</label>
                    <input type="text" name="admin_name" id="admin_name" class="form-control" value="<?php echo htmlspecialchars($adminName); ?>">
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg">Save Settings</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white text-center">
            <h3>Admin Profile</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="admin_profile" value="1">
                <div class="mb-3">
                    <label for="deped_id_no" class="form-label">DepEd ID No</label>
                    <input type="text" name="deped_id_no" id="deped_id_no" class="form-control" value="<?php echo htmlspecialchars($depedIdNo); ?>">
                </div>
                <div class="mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" name="last_name" id="last_name" class="form-control" value="<?php echo htmlspecialchars($lastName); ?>">
                </div>
                <div class="mb-3">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" name="first_name" id="first_name" class="form-control" value="<?php echo htmlspecialchars($firstName); ?>">
                </div>
                <div class="mb-3">
                    <label for="middle_name" class="form-label">Middle Name</label>
                    <input type="text" name="middle_name" id="middle_name" class="form-control" value="<?php echo htmlspecialchars($middleName); ?>">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($adminEmail); ?>">
                </div>
                <div class="mb-3">
                    <label for="admin_photo" class="form-label">Profile Photo</label>
                    <div class="d-flex align-items-center">
                        <?php 
                        $adminPhoto = null;
                        if (isset($_SESSION['user_id'])) {
                            require_once '../functions/upload_user_photo.php';
                            $adminPhoto = getUserPhoto($_SESSION['user_id']);
                        }
                        ?>
                        <?php if ($adminPhoto): ?>
                            <img src="<?= htmlspecialchars($adminPhoto) ?>" alt="Current Photo" class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover; border: 2px solid #007bff;">
                        <?php else: ?>
                            <div class="rounded-circle me-3 d-flex align-items-center justify-content-center bg-light" style="width: 60px; height: 60px;">
                                <i class="fas fa-user" style="font-size: 30px; color: #ccc;"></i>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="admin_photo" name="admin_photo" accept="image/*">
                    </div>
                    <small class="text-muted">Upload JPG, PNG, or GIF (max 5MB)</small>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-secondary btn-lg">Update Profile</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Management Section - Admin Only -->
    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
    <div class="card shadow-sm mt-4 border-danger">
        <div class="card-header bg-danger text-white text-center">
            <h3><i class="fas fa-exclamation-triangle me-2"></i>Data Management</h3>
        </div>
        <div class="card-body">
            <div class="alert alert-warning" role="alert">
                <h5><i class="fas fa-exclamation-triangle me-2"></i>Warning!</h5>
                <p class="mb-0">This section contains powerful administrative functions that can permanently delete all data. Use with extreme caution!</p>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h5><i class="fas fa-database me-2"></i>Database Statistics</h5>
                        </div>
                        <div class="card-body">
                            <div id="dbStats">
                                <div class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2">Loading database statistics...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h5><i class="fas fa-trash-alt me-2"></i>Selective Data Deletion</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Select specific tables to delete data from:</p>
                            
                            <form id="selectiveDeleteForm">
                                <div class="mb-3">
                                    <h6 class="text-primary">File Tables (Main + Versions)</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input table-checkbox" type="checkbox" id="admin_files" name="tables[]" value="admin_files">
                                                <label class="form-check-label" for="admin_files">
                                                    Admin Files + Versions
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input table-checkbox" type="checkbox" id="aeld_files" name="tables[]" value="aeld_files">
                                                <label class="form-check-label" for="aeld_files">
                                                    AELD Files + Versions
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input table-checkbox" type="checkbox" id="cild_files" name="tables[]" value="cild_files">
                                                <label class="form-check-label" for="cild_files">
                                                    CILD Files + Versions
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input table-checkbox" type="checkbox" id="if_completed_files" name="tables[]" value="if_completed_files">
                                                <label class="form-check-label" for="if_completed_files">
                                                    IF Completed Files + Versions
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input table-checkbox" type="checkbox" id="if_proposals_files" name="tables[]" value="if_proposals_files">
                                                <label class="form-check-label" for="if_proposals_files">
                                                    IF Proposals Files + Versions
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input table-checkbox" type="checkbox" id="lulr_files" name="tables[]" value="lulr_files">
                                                <label class="form-check-label" for="lulr_files">
                                                    LULR Files + Versions
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input table-checkbox" type="checkbox" id="rp_completed_berf_files" name="tables[]" value="rp_completed_berf_files">
                                                <label class="form-check-label" for="rp_completed_berf_files">
                                                    RP Completed BERF Files + Versions
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input table-checkbox" type="checkbox" id="rp_completed_nonberf_files" name="tables[]" value="rp_completed_nonberf_files">
                                                <label class="form-check-label" for="rp_completed_nonberf_files">
                                                    RP Completed Non-BERF Files + Versions
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input table-checkbox" type="checkbox" id="rp_proposal_berf_files" name="tables[]" value="rp_proposal_berf_files">
                                                <label class="form-check-label" for="rp_proposal_berf_files">
                                                    RP Proposal BERF Files + Versions
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input table-checkbox" type="checkbox" id="rp_proposal_nonberf_files" name="tables[]" value="rp_proposal_nonberf_files">
                                                <label class="form-check-label" for="rp_proposal_nonberf_files">
                                                    RP Proposal Non-BERF Files + Versions
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input table-checkbox" type="checkbox" id="t_lr_files" name="tables[]" value="t_lr_files">
                                                <label class="form-check-label" for="t_lr_files">
                                                    T LR Files + Versions
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input table-checkbox" type="checkbox" id="t_pp_files" name="tables[]" value="t_pp_files">
                                                <label class="form-check-label" for="t_pp_files">
                                                    T PP Files + Versions
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input table-checkbox" type="checkbox" id="t_rs_files" name="tables[]" value="t_rs_files">
                                                <label class="form-check-label" for="t_rs_files">
                                                    T RS Files + Versions
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input table-checkbox" type="checkbox" id="approved_proposal" name="tables[]" value="approved_proposal">
                                                <label class="form-check-label" for="approved_proposal">
                                                    Approved Proposal + Versions
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <h6 class="text-warning">System Tables</h6>
                                    <div class="form-check">
                                        <input class="form-check-input table-checkbox" type="checkbox" id="pending_files" name="tables[]" value="pending_files">
                                        <label class="form-check-label" for="pending_files">
                                            Pending Files
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input table-checkbox" type="checkbox" id="master_files" name="tables[]" value="master_files">
                                        <label class="form-check-label" for="master_files">
                                            Master Files
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input table-checkbox" type="checkbox" id="file_preview_mapping" name="tables[]" value="file_preview_mapping">
                                        <label class="form-check-label" for="file_preview_mapping">
                                            File Preview Mapping
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="deletePhysicalFiles" name="delete_physical_files">
                                        <label class="form-check-label" for="deletePhysicalFiles">
                                            Also delete physical files from server
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="confirmationCode" class="form-label">Confirmation Code</label>
                                    <input type="text" class="form-control" id="confirmationCode" 
                                           placeholder="Type: DELETE_SELECTED_TABLES_CONFIRM" required>
                                    <div class="form-text">Type exactly: <code>DELETE_SELECTED_TABLES_CONFIRM</code></div>
                                </div>
                                
                                <button type="button" class="btn btn-danger w-100" id="deleteSelectedBtn" disabled>
                                    <i class="fas fa-trash-alt me-2"></i>Delete Selected Tables
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>

<!-- SweetAlert2 for confirmations -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load database statistics
    loadDatabaseStats();
    
    // Handle confirmation code input
    const confirmationInput = document.getElementById('confirmationCode');
    const deleteBtn = document.getElementById('deleteSelectedBtn');
    
    if (confirmationInput && deleteBtn) {
        confirmationInput.addEventListener('input', function() {
            updateDeleteSelectedButton();
        });
        
        // Handle production confirmation checkbox
        const productionConfirm = document.getElementById('deletePhysicalFiles');
        if (productionConfirm) {
            productionConfirm.addEventListener('change', function() {
                updateDeleteSelectedButton();
            });
        }
    }
    
    function updateDeleteSelectedButton() {
        const confirmationInput = document.getElementById('confirmationCode');
        const deleteBtn = document.getElementById('deleteSelectedBtn');
        const checkedTables = document.querySelectorAll('.table-checkbox:checked');
        
        if (confirmationInput && deleteBtn) {
            const codeCorrect = confirmationInput.value === 'DELETE_SELECTED_TABLES_CONFIRM';
            const hasSelectedTables = checkedTables.length > 0;
            deleteBtn.disabled = !(codeCorrect && hasSelectedTables);
            
            // Update button text with count
            if (hasSelectedTables) {
                deleteBtn.innerHTML = `<i class="fas fa-trash-alt me-2"></i>Delete Selected Tables (${checkedTables.length})`;
            } else {
                deleteBtn.innerHTML = `<i class="fas fa-trash-alt me-2"></i>Delete Selected Tables`;
            }
        }
    }
    
    // Handle table checkbox changes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('table-checkbox')) {
            updateDeleteSelectedButton();
        }
    });
        
        // Handle delete all button click
        deleteBtn.addEventListener('click', function() {
            deleteSelectedData();
        });
    }
});

function loadDatabaseStats() {
    fetch('../functions/get_database_stats.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                displayDatabaseStats(data.stats);
            } else {
                document.getElementById('dbStats').innerHTML = 
                    '<div class="alert alert-danger">Failed to load database statistics</div>';
            }
        })
        .catch(error => {
            console.error('Error loading stats:', error);
            document.getElementById('dbStats').innerHTML = 
                '<div class="alert alert-danger">Error loading database statistics</div>';
        });
}

function displayDatabaseStats(stats) {
    const statsHtml = `
        <div class="row text-center">
            <div class="col-6">
                <h4 class="text-primary">${stats.total_files}</h4>
                <small class="text-muted">Total Files</small>
            </div>
            <div class="col-6">
                <h4 class="text-success">${stats.total_versions}</h4>
                <small class="text-muted">File Versions</small>
            </div>
        </div>
        <hr>
        <div class="row text-center">
            <div class="col-6">
                <h4 class="text-warning">${stats.pending_files}</h4>
                <small class="text-muted">Pending Files</small>
            </div>
            <div class="col-6">
                <h4 class="text-info">${stats.total_size}</h4>
                <small class="text-muted">Total Size</small>
            </div>
        </div>
    `;
    document.getElementById('dbStats').innerHTML = statsHtml;
}

function deleteSelectedData() {
    const confirmationCode = document.getElementById('confirmationCode').value;
    const deletePhysicalFiles = document.getElementById('deletePhysicalFiles').checked;
    const selectedTables = Array.from(document.querySelectorAll('.table-checkbox:checked')).map(cb => cb.value);
    
    if (confirmationCode !== 'DELETE_SELECTED_TABLES_CONFIRM') {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Confirmation',
            text: 'Please type the confirmation code correctly.'
        });
        return;
    }
    
    if (selectedTables.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No Tables Selected',
            text: 'Please select at least one table to delete.'
        });
        return;
    }
    
    // Create table list for display
    const tableList = selectedTables.map(table => {
        if (table.includes('_files') || table === 'approved_proposal') {
            return `• ${table} + ${table}_versions`;
        }
        return `• ${table}`;
    }).join('<br>');
    
    // Final confirmation with SweetAlert
    Swal.fire({
        icon: 'warning',
        title: 'Are you absolutely sure?',
        html: `
            <div class="text-start">
                <p><strong>This action will permanently delete data from:</strong></p>
                <div class="text-start mb-3">
                    ${tableList}
                </div>
                <ul class="text-start">
                    <li>All records from selected tables</li>
                    <li>All file versions and revisions (for file tables)</li>
                    ${deletePhysicalFiles ? '<li><strong>ALL PHYSICAL FILES</strong></li>' : ''}
                </ul>
                <p class="text-danger"><strong>This action cannot be undone!</strong></p>
                <p class="text-success"><strong>Note:</strong> User accounts, system settings, and audit logs will be preserved.</p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: `Yes, delete ${selectedTables.length} table(s)!`,
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            performDeleteSelected(selectedTables);
        }
    });
}

function performDeleteSelected(selectedTables) {
    const confirmationCode = document.getElementById('confirmationCode').value;
    const deletePhysicalFiles = document.getElementById('deletePhysicalFiles').checked;
    
    // Show loading state
    Swal.fire({
        title: 'Deleting Selected Data...',
        html: 'This may take a few moments. Please do not close this window.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Prepare form data
    const formData = new FormData();
    formData.append('confirmation', confirmationCode);
    if (deletePhysicalFiles) {
        formData.append('delete_physical_files', '1');
    }
    formData.append('tables', JSON.stringify(selectedTables)); // Append selected tables as JSON
    
    // Send request
    fetch('../functions/delete_selected_files.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Data Deleted Successfully!',
                html: `
                    <div class="text-start">
                        <p><strong>Summary:</strong></p>
                        <ul class="text-start">
                            <li>Main records: ${data.summary.total_main_records}</li>
                            <li>Version records: ${data.summary.total_version_records}</li>
                            <li>Physical files: ${data.summary.total_physical_files}</li>
                        </ul>
                    </div>
                `,
                confirmButtonText: 'OK'
            }).then(() => {
                // Reload page to reflect changes
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred while deleting data. Please try again.'
        });
    });
}
</script>
