<?php
// File path to the Word or Excel document
$filePath = 'C:\wamp3\www\west2es\uploads\files\admin_files\teachers_incentives_2025.docx'; // Use double backslashes for Windows paths

// Check if the file exists
if (file_exists($filePath)) {
    // Command to open the file using the default application
    $command = 'start "" "' . $filePath . '"';

    // Execute the command
    shell_exec($command);
    echo "File opened successfully!";
} else {
    echo "File not found: $filePath";
}
?>
