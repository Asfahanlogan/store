<?php
/**
 * Admin Login Fix Solution
 * This script fixes the password hash issue in admin.json
 */

echo "<h1>Admin Login Fix Solution</h1>";

// Check if we can access the config
if (!file_exists('config.php')) {
    echo "<p style='color: red;'>Error: config.php not found!</p>";
    exit;
}

// Include the config to get access to JsonDB class
require_once 'config.php';

echo "<h2>Step 1: Checking current admin.json</h2>";
$current_admin = JsonDB::read('admin.json');
if (empty($current_admin)) {
    echo "<p style='color: red;'>Error: Could not read admin.json</p>";
    exit;
}

echo "<p>Current admin data:</p>";
echo "<pre>" . json_encode($current_admin, JSON_PRETTY_PRINT) . "</pre>";

echo "<h2>Step 2: Generating new password hash</h2>";
$username = 'admin';
$password = 'SecureAdmin123!';
$new_hash = password_hash($password, PASSWORD_DEFAULT);

echo "<p>Username: <strong>$username</strong></p>";
echo "<p>Password: <strong>$password</strong></p>";
echo "<p>New hash: <code>$new_hash</code></p>";

echo "<h2>Step 3: Updating admin.json</h2>";

// Create new admin data
$new_admin_data = [
    'username' => $username,
    'password' => $new_hash,
    'wallet_addresses' => [
        'BTC' => 'bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh',
        'ETH' => '0x742d35Cc6634C0532925a3b8D404fddaF789C5C2',
        'LTC' => 'ltc1qw508d6qejxtdg4y5r3zarvary0c5xw7k5zpx'
    ]
];

// Write the new data
$result = JsonDB::write('admin.json', $new_admin_data);

if ($result !== false) {
    echo "<p style='color: green;'>✓ admin.json updated successfully!</p>";
    
    // Verify the update
    $updated_admin = JsonDB::read('admin.json');
    echo "<p>Updated admin data:</p>";
    echo "<pre>" . json_encode($updated_admin, JSON_PRETTY_PRINT) . "</pre>";
    
    // Test password verification
    if (password_verify($password, $updated_admin['password'])) {
        echo "<p style='color: green;'>✓ Password verification test passed!</p>";
    } else {
        echo "<p style='color: red;'>✗ Password verification test failed!</p>";
    }
    
} else {
    echo "<p style='color: red;'>✗ Failed to update admin.json</p>";
    exit;
}

echo "<h2>Step 4: Testing login</h2>";
echo "<p>You can now try logging in at <a href='admin/login.php'>/admin/login.php</a> with:</p>";
echo "<ul>";
echo "<li><strong>Username:</strong> admin</li>";
echo "<li><strong>Password:</strong> SecureAdmin123!</li>";
echo "</ul>";

echo "<h2>Step 5: Cleanup</h2>";
echo "<p>For security, you should delete this solution.php file after successful login.</p>";
echo "<p><a href='admin/login.php' class='btn btn-primary'>Go to Admin Login</a></p>";

echo "<hr>";
echo "<p><small>Solution completed successfully!</small></p>";
?>