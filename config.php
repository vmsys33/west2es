<?php

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$currentUri = $_SERVER['REQUEST_URI'];

// Go up two levels in the directory structure
$baseUrl = $protocol . "://" . $host . dirname(dirname($currentUri)) . "/"; 

?>

<?php
require __DIR__ . '/vendor/autoload.php'; // ✅ Corrected vendor path

use Pusher\Pusher;


// Initialize Pusher
$pusher = new Pusher(
    '7e21bf94e1a661247fc0',  // Your Pusher APP KEY
    '117f49779d2d523c1833',  // Your Pusher APP SECRET
    '1732249',  // Your Pusher APP ID
    [
        'cluster' => 'ap1', // Your Pusher CLUSTER
        'useTLS' => true,
        'curl_options' => [
            CURLOPT_CAINFO => "C:/wamp3/bin/php/cacert.pem"    // Secure connection
    ]
    ]
);
?>
