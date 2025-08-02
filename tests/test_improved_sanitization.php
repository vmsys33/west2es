<?php
/**
 * Test Improved Filename Sanitization
 * Tests the improved filename sanitization that allows spaces
 */

echo "🔍 Test Improved Filename Sanitization\n";
echo "=====================================\n\n";

// Test various filename inputs
$testFilenames = [
    'test.pdf',
    'test file.pdf',
    'test-file.pdf',
    'test_file.pdf',
    'test..pdf',
    'test...pdf',
    'test.pdf.',
    'test.pdf..',
    'test..pdf.',
    'test...pdf...',
    'test with spaces.pdf',
    'test-with-dashes.pdf',
    'test_with_underscores.pdf',
    'test123.pdf',
    'test@#$%.pdf',
    'test(1).pdf',
    'test[1].pdf',
    'test{1}.pdf',
    'my document.pdf',
    'project file v2.pdf',
    'report 2024.pdf'
];

echo "🧪 Testing Improved Filename Sanitization...\n\n";

foreach ($testFilenames as $originalFilename) {
    echo "📄 Original: '{$originalFilename}'\n";
    
    // Apply the improved sanitization
    $sanitizedFilename = preg_replace('/[^a-zA-Z0-9._\s-]/', '', $originalFilename);
    $sanitizedFilename = preg_replace('/\.{2,}/', '.', $sanitizedFilename);
    $sanitizedFilename = rtrim($sanitizedFilename, '.');
    $sanitizedFilename = preg_replace('/\s+/', ' ', $sanitizedFilename);
    $sanitizedFilename = trim($sanitizedFilename);
    
    echo "   Sanitized: '{$sanitizedFilename}'\n";
    
    // Check for multiple consecutive periods
    if (preg_match('/\.{2,}/', $sanitizedFilename)) {
        echo "   ❌ WARNING: Multiple consecutive periods detected!\n";
    } else {
        echo "   ✅ No multiple consecutive periods\n";
    }
    
    // Check for trailing period
    if (substr($sanitizedFilename, -1) === '.') {
        echo "   ❌ WARNING: Trailing period detected!\n";
    } else {
        echo "   ✅ No trailing period\n";
    }
    
    // Check if it still has a valid extension
    if (!preg_match('/\.[a-zA-Z0-9]+$/', $sanitizedFilename)) {
        echo "   ❌ WARNING: No valid extension detected!\n";
    } else {
        echo "   ✅ Valid extension detected\n";
    }
    
    // Check if spaces are preserved
    if (strpos($originalFilename, ' ') !== false && strpos($sanitizedFilename, ' ') === false) {
        echo "   ❌ WARNING: Spaces were removed!\n";
    } else {
        echo "   ✅ Spaces preserved correctly\n";
    }
    
    echo "\n";
}

echo "🎉 The improved sanitization now:\n";
echo "1. ✅ Allows spaces in filenames\n";
echo "2. ✅ Removes multiple consecutive periods\n";
echo "3. ✅ Removes trailing periods\n";
echo "4. ✅ Maintains valid file extensions\n";
echo "5. ✅ Cleans up multiple spaces to single spaces\n";
echo "6. ✅ Trims leading and trailing spaces\n\n";

echo "🔧 The improved sanitization process:\n";
echo "1. Allow valid chars: preg_replace('/[^a-zA-Z0-9._\\s-]/', '', \$filename)\n";
echo "2. Clean periods: preg_replace('/\\.{2,}/', '.', \$filename)\n";
echo "3. Remove trailing: rtrim(\$filename, '.')\n";
echo "4. Clean spaces: preg_replace('/\\s+/', ' ', \$filename)\n";
echo "5. Trim spaces: trim(\$filename)\n";
?> 