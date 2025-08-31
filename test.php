<?php
// Test script to verify all components work correctly

echo "<h1>CyberTools Pro - Functionality Test</h1>";

// Test 1: Check if config file loads correctly
echo "<h2>Test 1: Configuration File</h2>";
if (file_exists('config.php')) {
    echo "<p style='color: green;'>✓ config.php found</p>";
    
    // Try to include the config file
    try {
        include 'config.php';
        echo "<p style='color: green;'>✓ config.php loaded successfully</p>";
        
        // Test JsonDB class
        if (class_exists('JsonDB')) {
            echo "<p style='color: green;'>✓ JsonDB class exists</p>";
        } else {
            echo "<p style='color: red;'>✗ JsonDB class not found</p>";
        }
        
        // Test data directory
        if (defined('DATA_PATH') && is_dir(DATA_PATH)) {
            echo "<p style='color: green;'>✓ Data directory exists: " . DATA_PATH . "</p>";
        } else {
            echo "<p style='color: red;'>✗ Data directory not found</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Error loading config.php: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>✗ config.php not found</p>";
}

// Test 2: Check data files
echo "<h2>Test 2: Data Files</h2>";

$data_files = ['tools.json', 'payments.json', 'admin.json'];
foreach ($data_files as $file) {
    $path = 'data/' . $file;
    if (file_exists($path)) {
        echo "<p style='color: green;'>✓ $file found</p>";
        
        // Try to read the file
        try {
            $content = file_get_contents($path);
            $data = json_decode($content, true);
            if ($data !== null) {
                echo "<p style='color: green;'>✓ $file parsed successfully (" . count($data) . " records)</p>";
            } else {
                echo "<p style='color: red;'>✗ Error parsing $file: " . json_last_error_msg() . "</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>✗ Error reading $file: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ $file not found</p>";
    }
}

// Test 3: Check required directories
echo "<h2>Test 3: Required Directories</h2>";

$directories = ['includes', 'admin', 'admin/includes', 'assets/css'];
foreach ($directories as $dir) {
    if (is_dir($dir)) {
        echo "<p style='color: green;'>✓ $dir directory exists</p>";
    } else {
        echo "<p style='color: red;'>✗ $dir directory not found</p>";
    }
}

// Test 4: Check required files
echo "<h2>Test 4: Required Files</h2>";

$files = [
    'index.php', 'products.php', 'checkout.php', 'payment-status.php', 'contact.php',
    'includes/header.php', 'includes/footer.php',
    'admin/index.php', 'admin/login.php', 'admin/dashboard.php', 'admin/tools.php', 'admin/payments.php', 'admin/logout.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✓ $file exists</p>";
    } else {
        echo "<p style='color: red;'>✗ $file not found</p>";
    }
}

// Test 5: Check functions
echo "<h2>Test 5: Function Tests</h2>";

// Test formatPrice function
if (function_exists('formatPrice')) {
    $test_price = 29.99;
    $formatted = formatPrice($test_price);
    if ($formatted === '$29.99') {
        echo "<p style='color: green;'>✓ formatPrice function works correctly: $formatted</p>";
    } else {
        echo "<p style='color: red;'>✗ formatPrice function returned unexpected result: $formatted</p>";
    }
} else {
    echo "<p style='color: red;'>✗ formatPrice function not found</p>";
}

// Test isAdmin function
if (function_exists('isAdmin')) {
    echo "<p style='color: green;'>✓ isAdmin function exists</p>";
} else {
    echo "<p style='color: red;'>✗ isAdmin function not found</p>";
}

echo "<h2>Test Summary</h2>";
echo "<p>All core components have been tested. If all tests show green checkmarks, the website should function correctly when deployed on a PHP-enabled web server.</p>";
echo "<p>To run the website:</p>";
echo "<ol>";
echo "<li>Place all files in a directory accessible by your web server</li>";
echo "<li>Ensure PHP 7.0 or higher is installed and configured</li>";
echo "<li>Access index.php through your web browser</li>";
echo "<li>For admin access, navigate to /admin/ and use credentials from data/admin.json</li>";
echo "</ol>";
?>