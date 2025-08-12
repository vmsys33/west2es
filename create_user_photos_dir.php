<?php
echo "<h2>Creating User Photos Directory</h2>";

$uploadDir = 'uploads/files/user_photos/';

// Create the directory structure if it doesn't exist
if (!is_dir($uploadDir)) {
    echo "<p>❌ User photos directory does not exist. Creating it...</p>";
    
    // Create directories recursively
    if (mkdir($uploadDir, 0755, true)) {
        echo "<p>✅ User photos directory created successfully at: $uploadDir</p>";
    } else {
        echo "<p>❌ Failed to create user photos directory.</p>";
    }
} else {
    echo "<p>✅ User photos directory already exists at: $uploadDir</p>";
}

// Check if directory is writable
if (is_writable($uploadDir)) {
    echo "<p>✅ Directory is writable.</p>";
} else {
    echo "<p>❌ Directory is not writable. Please check permissions.</p>";
}

// List contents of uploads directory
echo "<h3>Uploads Directory Contents:</h3>";
$uploadsDir = 'uploads/';
if (is_dir($uploadsDir)) {
    $files = scandir($uploadsDir);
    echo "<pre>";
    print_r($files);
    echo "</pre>";
} else {
    echo "<p>Uploads directory does not exist.</p>";
}
?>
