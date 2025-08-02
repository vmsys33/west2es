<?php
/**
 * Test Filename Sanitization Fixed
 * Tests the improved filename sanitization
 */

echo "🔍 Test Filename Sanitization Fixed\n";
echo "===================================\n\n";

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

echo "🧪 Testing Improved Filename Sanitization...\n\n";

foreach ($testFilenames as $originalFilename) {
    echo "📄 Original: '{$originalFilename}'\n";
    
    // Apply the improved sanitization
    $sanitizedFilename = preg_replace('/[^a-zA-Z0-9._-]/', '', $originalFilename);
    $sanitizedFilename = preg_replace('/\.{2,}/', '.', $sanitizedFilename);
    $sanitizedFilename = rtrim($sanitizedFilename, '.');
    
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
    
    echo "\n";
}

echo "🎉 The fix should now:\n";
echo "1. ✅ Remove multiple consecutive periods\n";
echo "2. ✅ Remove trailing periods\n";
echo "3. ✅ Maintain valid file extensions\n";
echo "4. ✅ Prevent 'unsupported' button issues\n\n";

echo "🔧 The improved sanitization:\n";
echo "1. Remove invalid characters: preg_replace('/[^a-zA-Z0-9._-]/', '', \$filename)\n";
echo "2. Clean multiple periods: preg_replace('/\\.{2,}/', '.', \$filename)\n";
echo "3. Remove trailing periods: rtrim(\$filename, '.')\n";
echo "4. Validate extension: preg_match('/\\.[a-zA-Z0-9]+\$/', \$filename)\n";
?> 