<?php
session_start();
echo "<h2>Session Debug Information</h2>";
echo "<pre>";
echo "Session school_logo: " . ($_SESSION['school_logo'] ?? 'NOT SET') . "\n";
echo "Session website_name: " . ($_SESSION['website_name'] ?? 'NOT SET') . "\n";
echo "Session email_address: " . ($_SESSION['email_address'] ?? 'NOT SET') . "\n";
echo "Session admin_name: " . ($_SESSION['admin_name'] ?? 'NOT SET') . "\n";
echo "All session data:\n";
print_r($_SESSION);
echo "</pre>";

// Check if the logo file exists
$schoolLogo = $_SESSION['school_logo'] ?? '';
$logoPath = !empty($schoolLogo) ? '../uploads/' . $schoolLogo : '../assets/images/logo1.png';
echo "<h3>Logo Path Check</h3>";
echo "Logo filename: " . $schoolLogo . "<br>";
echo "Full path: " . $logoPath . "<br>";
echo "File exists: " . (file_exists($logoPath) ? 'YES' : 'NO') . "<br>";

// Check uploads directory
echo "<h3>Uploads Directory Contents</h3>";
$uploadsDir = '../uploads/';
if (is_dir($uploadsDir)) {
    $files = scandir($uploadsDir);
    echo "<pre>";
    print_r($files);
    echo "</pre>";
} else {
    echo "Uploads directory does not exist";
}
?>
