<?php
// Test if ZIP extension is available
if (class_exists('ZipArchive')) {
    echo "✅ ZIP extension is available!";
} else {
    echo "❌ ZIP extension is NOT available";
}

// Show loaded extensions
echo "<br><br>Loaded extensions:<br>";
$extensions = get_loaded_extensions();
foreach ($extensions as $ext) {
    if (strpos($ext, 'zip') !== false) {
        echo "✅ $ext<br>";
    }
}
?>
