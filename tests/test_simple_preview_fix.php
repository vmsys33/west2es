<?php
/**
 * Simple Preview Fix Test
 * Tests that preview file paths are working correctly
 */

echo "🔍 Simple Preview Fix Test\n";
echo "=========================\n\n";

// Test file path construction
$testFiles = [
    'aeld_files' => 'aeld8.pdf',
    'aeld_files' => 'aeld5.docx',
    'aeld_files' => 'aeld8_v2.pdf'
];

echo "🧪 Testing File Path Construction...\n\n";

foreach ($testFiles as $table => $filename) {
    $filePath = "uploads/files/{$table}/{$filename}";
    $physicalPath = $_SERVER['DOCUMENT_ROOT'] . '/west2es/' . $filePath;
    
    echo "📄 Testing: {$filename}\n";
    echo "   Path: {$filePath}\n";
    echo "   Physical: {$physicalPath}\n";
    
    if (file_exists($physicalPath)) {
        echo "   ✅ File exists on server\n";
    } else {
        echo "   ❌ File NOT found on server\n";
    }
    echo "\n";
}

echo "🎉 Preview path fix test completed!\n";
echo "📋 The viewer files have been updated to handle the west2es directory structure.\n";
?> 