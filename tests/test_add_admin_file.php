<?php
// This script simulates uploading MISSION_VISION.pdf to admin_files using the current add_file.php process

$addFileUrl = 'http://localhost/west2es/functions/file_functions/add_file.php'; // Updated to full URL
$filePath = realpath(__DIR__ . '/../uploads/MISSION_VISION.pdf');

if (!file_exists($filePath)) {
    die("File not found: $filePath\n");
}

// Prepare POST fields
$postFields = [
    'fileName' => 'MISSION_VISION',
    'table1' => 'admin_files',
    'fileInput' => new CURLFile($filePath, 'application/pdf', 'MISSION_VISION.pdf'),
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $addFileUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// If authentication/session cookie is needed, set it here:
// curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID=your-session-id');

$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Curl error: ' . curl_error($ch) . "\n";
} else {
    echo "Response from add_file.php:\n";
    echo $response . "\n";
}
curl_close($ch);