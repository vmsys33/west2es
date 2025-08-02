<?php
/**
 * AJAX Endpoints Test
 * Tests all AJAX endpoints used in content.php
 */

require_once __DIR__ . '/../functions/db_connection.php';

echo "🔗 Testing AJAX Endpoints\n";
echo "========================\n\n";

// Test data
$testData = [
    'file_id' => 1,
    'file_table' => 'admin_files_versions',
    'table1' => 'admin_files',
    'table2' => 'admin_files_versions',
    'file_version' => 1
];

// Function to test AJAX endpoint
function testAjaxEndpoint($endpoint, $method = 'GET', $data = []) {
    $url = "http://localhost/west2es/{$endpoint}";
    
    // Create context for file_get_contents
    $context = stream_context_create([
        'http' => [
            'method' => $method,
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query($data)
        ]
    ]);
    
    try {
        $response = file_get_contents($url, false, $context);
        if ($response !== false) {
            $jsonResponse = json_decode($response, true);
            if ($jsonResponse) {
                return [
                    'status' => 'success',
                    'response' => $jsonResponse,
                    'raw' => $response
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Invalid JSON response',
                    'raw' => $response
                ];
            }
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to connect to endpoint'
            ];
        }
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}

// Test endpoints
$endpoints = [
    [
        'name' => 'fetch_revisions.php',
        'path' => 'functions/file_functions/fetch_revisions.php',
        'method' => 'GET',
        'data' => ['file_id' => 1, 'file_table' => 'admin_files_versions']
    ],
    [
        'name' => 'fetch_file_revisions.php',
        'path' => 'functions/file_functions/fetch_file_revisions.php',
        'method' => 'GET',
        'data' => ['file_id' => 1, 'file_table' => 'admin_files_versions']
    ]
];

echo "Testing AJAX Endpoints:\n";
echo "======================\n\n";

foreach ($endpoints as $endpoint) {
    echo "🔍 Testing: {$endpoint['name']}\n";
    echo "URL: {$endpoint['path']}\n";
    
    $result = testAjaxEndpoint($endpoint['path'], $endpoint['method'], $endpoint['data']);
    
    if ($result['status'] === 'success') {
        echo "✅ Status: Success\n";
        if (isset($result['response']['status'])) {
            echo "   Response Status: {$result['response']['status']}\n";
        }
        if (isset($result['response']['message'])) {
            echo "   Message: {$result['response']['message']}\n";
        }
    } else {
        echo "❌ Status: Failed\n";
        echo "   Error: {$result['message']}\n";
    }
    echo "\n";
}

// Test database connectivity for each file category
echo "🗄️  Testing Database Connectivity for File Categories\n";
echo "==================================================\n\n";

$fileCategories = [
    'admin_files' => 'admin_files_versions',
    'aeld_files' => 'aeld_files_versions',
    'cild_files' => 'cild_files_versions',
    'if_completed_files' => 'if_completed_files_versions',
    'if_proposals_files' => 'if_proposals_files_versions',
    'lulr_files' => 'lulr_files_versions',
    'rp_completed_berf_files' => 'rp_completed_berf_files_versions',
    'rp_completed_nonberf_files' => 'rp_completed_nonberf_files_versions',
    'rp_proposal_berf_files' => 'rp_proposal_berf_files_versions',
    'rp_proposal_nonberf_files' => 'rp_proposal_nonberf_files_versions',
    't_lr_files' => 't_lr_files_versions',
    't_pp_files' => 't_pp_files_versions',
    't_rs_files' => 't_rs_files_versions'
];

foreach ($fileCategories as $table1 => $table2) {
    echo "📁 Testing: {$table1}\n";
    
    // Test main table
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM {$table1}");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "   ✅ {$table1}: {$result['count']} records\n";
    } catch (Exception $e) {
        echo "   ❌ {$table1}: Error - {$e->getMessage()}\n";
    }
    
    // Test versions table
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM {$table2}");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "   ✅ {$table2}: {$result['count']} records\n";
    } catch (Exception $e) {
        echo "   ❌ {$table2}: Error - {$e->getMessage()}\n";
    }
    
    echo "\n";
}

// Test file operations function
echo "📂 Testing File Operations Function\n";
echo "==================================\n\n";

require_once __DIR__ . '/../functions/file_operations.php';

foreach ($fileCategories as $table1 => $table2) {
    echo "🔍 Testing setFileTableVariables for: {$table1}\n";
    
    try {
        $testFile = [
            'name' => 'test_file.pdf',
            'tmp_name' => '/tmp/test_file.pdf',
            'size' => 1024,
            'type' => 'application/pdf',
            'error' => 0
        ];
        
        $result = setFileTableVariables($table1, $testFile);
        
        if (isset($result['uploadDir']) && isset($result['table2'])) {
            echo "   ✅ uploadDir: {$result['uploadDir']}\n";
            echo "   ✅ table2: {$result['table2']}\n";
            echo "   ✅ file_path: {$result['file_path']}\n";
        } else {
            echo "   ❌ Missing required keys in result\n";
        }
    } catch (Exception $e) {
        echo "   ❌ Error: {$e->getMessage()}\n";
    }
    
    echo "\n";
}

echo "✅ AJAX Endpoints Test Complete!\n";
?> 