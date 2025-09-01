<?php
/**
 * Quick Admin Fix Script
 * This script manually fixes the admin.json file
 */

echo "<h1>Quick Admin Fix</h1>";

// Generate the correct password hash
$username = 'admin';
$password = 'SecureAdmin123!';
$new_hash = password_hash($password, PASSWORD_DEFAULT);

echo "<p>Username: <strong>$username</strong></p>";
echo "<p>Password: <strong>$password</strong></p>";
echo "<p>New hash: <code>$new_hash</code></p>";

// Test the hash
if (password_verify($password, $new_hash)) {
    echo "<p style='color: green;'>✓ Hash verification test passed!</p>";
} else {
    echo "<p style='color: red;'>✗ Hash verification test failed!</p>";
    exit;
}

// Create the new admin data
$admin_data = [
    'username' => $username,
    'password' => $new_hash,
    'wallet_addresses' => [
        'BTC' => 'bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh',
        'ETH' => '0x742d35Cc6634C0532925a3b8D404fddaF789C5C2',
        'LTC' => 'ltc1qw508d6qejxtdg4y5r3zarvary0c5xw7k5zpx'
    ]
];

// Write directly to the file
$json_content = json_encode($admin_data, JSON_PRETTY_PRINT);
$result = file_put_contents('data/admin.json', $json_content);

if ($result !== false) {
    echo "<p style='color: green;'>✓ admin.json updated successfully!</p>";
    
    // Verify the file was written
    $file_content = file_get_contents('data/admin.json');
    $decoded = json_decode($file_content, true);
    
    if ($decoded && password_verify($password, $decoded['password'])) {
        echo "<p style='color: green;'>✓ File verification passed!</p>";
        echo "<p>You can now login with:</p>";
        echo "<ul>";
        echo "<li><strong>Username:</strong> admin</li>";
        echo "<li><strong>Password:</strong> SecureAdmin123!</li>";
        echo "</ul>";
        echo "<p><a href='admin/login.php' class='btn btn-primary'>Go to Admin Login</a></p>";
    } else {
        echo "<p style='color: red;'>✗ File verification failed!</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Failed to write admin.json</p>";
    echo "<p>Check file permissions for data/admin.json</p>";
}

echo "<hr>";
echo "<p><small>Fix completed!</small></p>";
?>