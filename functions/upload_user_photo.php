<?php
require_once 'db_connection.php';

function uploadUserPhoto($userId, $file) {
    global $pdo;
    
    // Check if file was uploaded
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'No file uploaded or upload error'];
    }
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.'];
    }
    
    // Validate file size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'message' => 'File size too large. Maximum size is 5MB.'];
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'user_' . $userId . '_' . time() . '.' . $extension;
    $uploadPath = '../uploads/files/user_photos/' . $filename;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return ['success' => false, 'message' => 'Failed to save file'];
    }
    
    // Update database
    try {
        $stmt = $pdo->prepare("UPDATE user_data SET photo = ? WHERE id_no = ?");
        $stmt->execute([$filename, $userId]);
        
        return ['success' => true, 'message' => 'Photo uploaded successfully', 'filename' => $filename];
    } catch (PDOException $e) {
        // Delete uploaded file if database update fails
        unlink($uploadPath);
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

function getUserPhoto($userId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT photo FROM user_data WHERE id_no = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        
        if ($result && $result['photo']) {
            return '../uploads/files/user_photos/' . $result['photo'];
        }
        
        return null; // No photo uploaded
    } catch (PDOException $e) {
        return null;
    }
}

function deleteUserPhoto($userId) {
    global $pdo;
    
    try {
        // Get current photo filename
        $stmt = $pdo->prepare("SELECT photo FROM user_data WHERE id_no = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        
        if ($result && $result['photo']) {
            $photoPath = '../uploads/files/user_photos/' . $result['photo'];
            if (file_exists($photoPath)) {
                unlink($photoPath);
            }
        }
        
        // Update database to remove photo reference
        $stmt = $pdo->prepare("UPDATE user_data SET photo = NULL WHERE id_no = ?");
        $stmt->execute([$userId]);
        
        return ['success' => true, 'message' => 'Photo deleted successfully'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}
?> 