<?php
/**
 * Debug Rename Process
 * Tests to see exactly what's happening during rename
 */

echo "🔍 Debug Rename Process\n";
echo "======================\n\n";

// Test the exact sanitization process
$testFilenames = [
    'test file.pdf',
    'test-file.pdf',
    'test_file.pdf',
    'test..pdf',
    'test.pdf.',
    'test with spaces.pdf',
    'my document.pdf'
];

echo "🧪 Testing Exact Sanitization Process...\n\n";

foreach ($testFilenames as $originalFilename) {
    echo "📄 Original: '{$originalFilename}'\n";
    
    // Step 1: Trim
    $step1 = trim($originalFilename);
    echo "   Step 1 (trim): '{$step1}'\n";
    
    // Step 2: Remove invalid characters (but allow spaces)
    $step2 = preg_replace('/[^a-zA-Z0-9._\s-]/', '', $step1);
    echo "   Step 2 (preg_replace): '{$step2}'\n";
    
    // Step 3: Clean multiple periods
    $step3 = preg_replace('/\.{2,}/', '.', $step2);
    echo "   Step 3 (period cleanup): '{$step3}'\n";
    
    // Step 4: Remove trailing periods
    $step4 = rtrim($step3, '.');
    echo "   Step 4 (rtrim): '{$step4}'\n";
    
    // Step 5: Clean multiple spaces
    $step5 = preg_replace('/\s+/', ' ', $step4);
    echo "   Step 5 (space cleanup): '{$step5}'\n";
    
    // Step 6: Final trim
    $step6 = trim($step5);
    echo "   Step 6 (final trim): '{$step6}'\n";
    
    // Check for issues
    if (substr($step6, -1) === '.') {
        echo "   ❌ WARNING: Still has trailing period!\n";
    }
    
    if (preg_match('/\.{2,}/', $step6)) {
        echo "   ❌ WARNING: Still has multiple periods!\n";
    }
    
    if (!preg_match('/\.[a-zA-Z0-9]+$/', $step6)) {
        echo "   ❌ WARNING: No valid extension!\n";
    } else {
        echo "   ✅ Valid extension detected\n";
    }
    
    echo "\n";
}

echo "🎯 The issue might be:\n";
echo "1. The sanitization is still adding periods somewhere\n";
echo "2. The frontend is sending malformed data\n";
echo "3. There's additional processing happening\n";
echo "4. The file extension detection is failing\n\n";

echo "🔧 Let's check the actual rename function...\n";
?> 