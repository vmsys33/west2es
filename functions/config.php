<?php

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$currentUri = $_SERVER['REQUEST_URI'];

// Go up two levels in the directory structure
$baseUrl = $protocol . "://" . $host . dirname(dirname($currentUri)) . "/"; 

?>

