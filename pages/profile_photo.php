<?php
session_start();
require_once '../functions/db_connection.php';
require_once '../functions/upload_user_photo.php';

// Check if user is logged in
if (!isset($_SESSION['id_no'])) {
    header('Location: ../index.php');
    exit();
}

$userId = $_SESSION['id_no'];
$userPhoto = getUserPhoto($userId);
$message = '';
$messageType = '';

// Handle photo upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'upload' && isset($_FILES['photo'])) {
            $result = uploadUserPhoto($userId, $_FILES['photo']);
            if ($result['success']) {
                $message = $result['message'];
                $messageType = 'success';
                $userPhoto = getUserPhoto($userId); // Refresh photo path
            } else {
                $message = $result['message'];
                $messageType = 'error';
            }
        } elseif ($_POST['action'] === 'delete') {
            $result = deleteUserPhoto($userId);
            if ($result['success']) {
                $message = $result['message'];
                $messageType = 'success';
                $userPhoto = null;
            } else {
                $message = $result['message'];
                $messageType = 'error';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Photo - West 2 Elementary School</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .profile-photo-container {
            max-width: 300px;
            margin: 0 auto;
        }
        .profile-photo {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border: 3px solid #007bff;
        }
        .upload-area {
            border: 2px dashed #ccc;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }
        .upload-area:hover {
            border-color: #007bff;
            background-color: #e3f2fd;
        }
        .upload-area.dragover {
            border-color: #007bff;
            background-color: #e3f2fd;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-camera me-2"></i>
                            Profile Photo Management
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-<?= $messageType === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($message) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <div class="profile-photo-container text-center mb-4">
                            <?php if ($userPhoto): ?>
                                <img src="<?= htmlspecialchars($userPhoto) ?>" alt="Profile Photo" class="profile-photo rounded-circle mb-3">
                                <div class="mt-3">
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete your profile photo?')">
                                            <i class="fas fa-trash me-2"></i>Delete Photo
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <div class="profile-photo rounded-circle mb-3 d-flex align-items-center justify-content-center bg-light" style="width: 200px; height: 200px; margin: 0 auto;">
                                    <i class="fas fa-user" style="font-size: 80px; color: #ccc;"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="upload-area mb-4" id="uploadArea">
                            <form method="POST" enctype="multipart/form-data" id="uploadForm">
                                <input type="hidden" name="action" value="upload">
                                <div class="mb-3">
                                    <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: #007bff;"></i>
                                    <h5 class="mt-2">Upload New Photo</h5>
                                    <p class="text-muted">Drag and drop your photo here or click to browse</p>
                                </div>
                                <input type="file" name="photo" id="photoInput" accept="image/*" class="form-control" style="display: none;">
                                <button type="button" class="btn btn-primary" onclick="document.getElementById('photoInput').click()">
                                    <i class="fas fa-folder-open me-2"></i>Choose File
                                </button>
                            </form>
                        </div>

                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Photo Guidelines:</h6>
                            <ul class="mb-0">
                                <li>Supported formats: JPG, PNG, GIF</li>
                                <li>Maximum file size: 5MB</li>
                                <li>Recommended size: 200x200 pixels or larger</li>
                                <li>Photos will be automatically cropped to a circle</li>
                            </ul>
                        </div>

                        <div class="text-center">
                            <a href="dashboard-overview.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-submit form when file is selected
        document.getElementById('photoInput').addEventListener('change', function() {
            if (this.files.length > 0) {
                document.getElementById('uploadForm').submit();
            }
        });

        // Drag and drop functionality
        const uploadArea = document.getElementById('uploadArea');
        const photoInput = document.getElementById('photoInput');

        uploadArea.addEventListener('click', () => photoInput.click());

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                photoInput.files = files;
                document.getElementById('uploadForm').submit();
            }
        });
    </script>
</body>
</html> 