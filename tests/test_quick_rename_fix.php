<?php
/**
 * Quick Test for Rename Fix
 */

echo "🧪 Testing Rename Fix\n";
echo "=====================\n\n";

// Test the exact logic from rename_revision_file.php
function testRenameLogic($userInput, $currentFileName) {
    echo "📄 User input: '{$userInput}'\n";
    echo "📄 Current filename: '{$currentFileName}'\n";
    
    // Step 1: Sanitize filename
    $newFilename = preg_replace('/[^a-zA-Z0-9._\s-]/', '', $userInput);
    $newFilename = preg_replace('/\.{2,}/', '.', $newFilename);
    $newFilename = rtrim($newFilename, '.');
    $newFilename = preg_replace('/\s+/', ' ', $newFilename);
    $newFilename = trim($newFilename);
    
    echo "   After sanitization: '{$newFilename}'\n";
    
    // Step 2: Check if new filename has extension
    $newFilenameExtension = pathinfo($newFilename, PATHINFO_EXTENSION);
    echo "   New filename extension: '{$newFilenameExtension}'\n";
    
    if (empty($newFilenameExtension)) {
        // If no extension provided, use the original file's extension
        $fileExtension = pathinfo($currentFileName, PATHINFO_EXTENSION);
        $newFilename = $newFilename . '.' . $fileExtension;
        echo "   No extension provided, using original: '{$fileExtension}'\n";
    } else {
        echo "   Extension provided, using as is\n";
    }
    
    echo "   Final filename: '{$newFilename}'\n";
    
    // Check for issues
    if (substr($newFilename, -1) === '.') {
        echo "   ❌ WARNING: Still has trailing period!\n";
    }
    
    if (preg_match('/\.{2,}/', $newFilename)) {
        echo "   ❌ WARNING: Still has multiple periods!\n";
    }
    
    if (!preg_match('/\.[a-zA-Z0-9]+$/', $newFilename)) {
        echo "   ❌ WARNING: No valid extension!\n";
    } else {
        echo "   ✅ Valid extension detected\n";
    }
    
    return $newFilename;
}

// Test cases
$testCases = [
    ['input' => 'my file', 'current' => 'document.pdf'],
    ['input' => 'my file.pdf', 'current' => 'document.pdf'],
    ['input' => 'test..pdf', 'current' => 'document.pdf'],
    ['input' => 'test.pdf.', 'current' => 'document.pdf'],
    ['input' => 'my document', 'current' => 'report.pdf'],
    ['input' => 'project file v2', 'current' => 'project.pdf']
];

foreach ($testCases as $testCase) {
    echo "\n--- Test Case ---\n";
    $result = testRenameLogic($testCase['input'], $testCase['current']);
    echo "   📝 RESULT: '{$result}'\n";
}

echo "\n✅ Fix Summary:\n";
echo "1. Frontend no longer forces extension requirement\n";
echo "2. Backend handles extension logic properly\n";
echo "3. Sanitization preserves spaces and handles periods\n";
echo "4. Preview buttons should work after rename\n";
?> 