<?php
/**
 * Debug Paths Script
 * This script checks file paths and permissions
 */

echo "<h1>Debug Paths and Permissions</h1>";

// Check current directory
echo "<h2>Current Directory:</h2>";
echo "<p><strong>Current:</strong> " . __DIR__ . "</p>";
echo "<p><strong>Working:</strong> " . getcwd() . "</p>";

// Check config constants
echo "<h2>Config Constants:</h2>";
if (file_exists('config.php')) {
    require_once 'config.php';
    echo "<p><strong>DATA_PATH:</strong> " . DATA_PATH . "</p>";
    echo "<p><strong>SITE_NAME:</strong> " . SITE_NAME . "</p>";
    
    // Test JsonDB
    echo "<h2>JsonDB Test:</h2>";
    if (class_exists('JsonDB')) {
        echo "<p style='color: green;'>✓ JsonDB class exists</p>";
        
        // Test reading admin.json
        $admin_data = JsonDB::read('admin.json');
        echo "<p><strong>Admin data loaded:</strong> " . (empty($admin_data) ? 'EMPTY' : 'LOADED') . "</p>";
        
        if (!empty($admin_data)) {
            echo "<p><strong>Username:</strong> " . ($admin_data['username'] ?? 'NOT SET') . "</p>";
            echo "<p><strong>Password hash:</strong> " . (isset($admin_data['password']) ? 'SET' : 'NOT SET') . "</p>";
            echo "<p><strong>Password hash length:</strong> " . (isset($admin_data['password']) ? strlen($admin_data['password']) : 'N/A') . "</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ JsonDB class not found</p>";
    }
} else {
    echo "<p style='color: red;'>✗ config.php not found</p>";
}

// Check data directory
echo "<h2>Data Directory Check:</h2>";
$data_dir = __DIR__ . '/data/';
echo "<p><strong>Data directory:</strong> $data_dir</p>";
echo "<p><strong>Exists:</strong> " . (is_dir($data_dir) ? 'YES' : 'NO') . "</p>";
echo "<p><strong>Readable:</strong> " . (is_readable($data_dir) ? 'YES' : 'NO') . "</p>";

// Check admin.json file
echo "<h2>Admin.json File Check:</h2>";
$admin_file = $data_dir . 'admin.json';
echo "<p><strong>Admin file path:</strong> $admin_file</p>";
echo "<p><strong>Exists:</strong> " . (file_exists($admin_file) ? 'YES' : 'NO') . "</p>";
echo "<p><strong>Readable:</strong> " . (is_readable($admin_file) ? 'YES' : 'NO') . "</p>";
echo "<p><strong>Writable:</strong> " . (is_writable($admin_file) ? 'YES' : 'NO') . "</p>";

if (file_exists($admin_file)) {
    $file_size = filesize($admin_file);
    echo "<p><strong>File size:</strong> $file_size bytes</p>";
    
    $content = file_get_contents($admin_file);
    if ($content !== false) {
        $json_data = json_decode($content, true);
        if ($json_data !== null) {
            echo "<p style='color: green;'>✓ JSON is valid</p>";
            echo "<p><strong>Keys:</strong> " . implode(', ', array_keys($json_data)) . "</p>";
        } else {
            echo "<p style='color: red;'>✗ JSON is invalid: " . json_last_error_msg() . "</p>";
        }
    } else {
        echo "<p style='color: red;">✗ Could not read file content</p>";
    }
}

// Test from admin directory perspective
echo "<h2>Admin Directory Perspective:</h2>";
$admin_data_dir = __DIR__ . '/admin/../data/';
echo "<p><strong>Admin data dir:</strong> $admin_data_dir</p>";
echo "<p><strong>Exists:</strong> " . (is_dir($admin_data_dir) ? 'YES' : 'NO') . "</p>";

$admin_admin_file = $admin_data_dir . 'admin.json';
echo "<p><strong>Admin admin file:</strong> $admin_admin_file</p>";
echo "<p><strong>Exists:</strong> " . (file_exists($admin_admin_file) ? 'YES' : 'NO') . "</p>";

echo "<hr>";
echo "<p><small>Debug completed!</small></p>";
?>