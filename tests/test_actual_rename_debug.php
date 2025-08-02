<?php
/**
 * Test Actual Rename Process
 * Simulates the exact rename process to debug issues
 */

echo "🔍 Testing Actual Rename Process\n";
echo "===============================\n\n";

// Simulate the exact sanitization process from edit_filename.php
function testFilenameSanitization($originalFilename) {
    echo "📄 Testing: '{$originalFilename}'\n";
    
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
    
    return $step6;
}

// Test the extension handling logic
function testExtensionHandling($newFilename, $oldFilename) {
    echo "\n🔧 Testing Extension Handling:\n";
    echo "   New filename: '{$newFilename}'\n";
    echo "   Old filename: '{$oldFilename}'\n";
    
    // Check if the new filename already has an extension
    $newFilenameExtension = pathinfo($newFilename, PATHINFO_EXTENSION);
    echo "   New filename extension: '{$newFilenameExtension}'\n";
    
    if (empty($newFilenameExtension)) {
        // If no extension provided, use the original file's extension
        $fileExtension = pathinfo($oldFilename, PATHINFO_EXTENSION);
        $newFilenameWithExtension = $newFilename . '.' . $fileExtension;
        echo "   No extension provided, using original: '{$fileExtension}'\n";
        echo "   Final filename: '{$newFilenameWithExtension}'\n";
    } else {
        // If extension is provided, use the new filename as is
        $newFilenameWithExtension = $newFilename;
        echo "   Extension provided, using as is\n";
        echo "   Final filename: '{$newFilenameWithExtension}'\n";
    }
    
    return $newFilenameWithExtension;
}

// Test cases
$testCases = [
    ['input' => 'my file.pdf', 'old' => 'document.pdf'],
    ['input' => 'my file', 'old' => 'document.pdf'],
    ['input' => 'test..pdf', 'old' => 'document.pdf'],
    ['input' => 'test.pdf.', 'old' => 'document.pdf'],
    ['input' => 'my document.pdf', 'old' => 'report.pdf'],
    ['input' => 'project file v2.pdf', 'old' => 'project.pdf']
];

echo "🧪 Testing Filename Sanitization:\n";
echo "================================\n";

foreach ($testCases as $testCase) {
    echo "\n--- Test Case ---\n";
    $sanitized = testFilenameSanitization($testCase['input']);
    $final = testExtensionHandling($sanitized, $testCase['old']);
    
    // Check for issues
    if (substr($final, -1) === '.') {
        echo "   ❌ WARNING: Final filename has trailing period!\n";
    }
    
    if (preg_match('/\.{2,}/', $final)) {
        echo "   ❌ WARNING: Final filename has multiple periods!\n";
    }
    
    if (!preg_match('/\.[a-zA-Z0-9]+$/', $final)) {
        echo "   ❌ WARNING: No valid extension!\n";
    } else {
        echo "   ✅ Valid extension detected\n";
    }
    
    echo "   📝 RESULT: '{$final}'\n";
}

echo "\n🎯 Summary:\n";
echo "The sanitization process should:\n";
echo "1. Allow spaces in filenames\n";
echo "2. Clean multiple periods to single periods\n";
echo "3. Remove trailing periods\n";
echo "4. Preserve valid extensions\n";
echo "5. Handle cases where user includes extension vs doesn't\n";
?> 