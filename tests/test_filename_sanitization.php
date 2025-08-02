<?php
/**
 * Test Filename Sanitization
 * Tests if the filename sanitization is adding extra periods
 */

echo "🔍 Test Filename Sanitization\n";
echo "=============================\n\n";

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
    'test{1}.pdf'
];

echo "🧪 Testing Filename Sanitization...\n\n";

foreach ($testFilenames as $originalFilename) {
    echo "📄 Original: '{$originalFilename}'\n";
    
    // Apply the same sanitization as in rename_revision_file.php
    $sanitizedFilename = preg_replace('/[^a-zA-Z0-9._-]/', '', $originalFilename);
    
    echo "   Sanitized: '{$sanitizedFilename}'\n";
    
    // Check for multiple consecutive periods
    if (preg_match('/\.{2,}/', $sanitizedFilename)) {
        echo "   ❌ WARNING: Multiple consecutive periods detected!\n";
    }
    
    // Check for trailing period
    if (substr($sanitizedFilename, -1) === '.') {
        echo "   ❌ WARNING: Trailing period detected!\n";
    }
    
    // Check if it still has a valid extension
    if (!preg_match('/\.[a-zA-Z0-9]+$/', $sanitizedFilename)) {
        echo "   ❌ WARNING: No valid extension detected!\n";
    } else {
        echo "   ✅ Valid extension detected\n";
    }
    
    echo "\n";
}

echo "🎯 The issue might be:\n";
echo "1. Multiple consecutive periods being created\n";
echo "2. Trailing periods being added\n";
echo "3. Invalid extensions being created\n";
echo "4. Frontend sending malformed filenames\n\n";

echo "🔧 Potential fixes:\n";
echo "1. Add period cleanup: preg_replace('/\.{2,}/', '.', $filename)\n";
echo "2. Remove trailing periods: rtrim($filename, '.')\n";
echo "3. Ensure proper extension validation\n";
echo "4. Check frontend input validation\n";
?> 