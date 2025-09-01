<?php
/**
 * Direct Admin Fix Script
 * This script directly fixes the admin.json file
 */

echo "<h1>Direct Admin Fix</h1>";

// Generate a working password hash
$username = 'admin';
$password = 'SecureAdmin123!';
$new_hash = password_hash($password, PASSWORD_DEFAULT);

echo "<h2>New Credentials:</h2>";
echo "<p><strong>Username:</strong> $username</p>";
echo "<p><strong>Password:</strong> $password</p>";
echo "<p><strong>New Hash:</strong> <code>$new_hash</code></p>";

// Test the hash
if (password_verify($password, $new_hash)) {
    echo "<p style='color: green;'>✓ Hash verification test passed!</p>";
} else {
    echo "<p style='color: red;'>✗ Hash verification test failed!</p>";
    exit;
}

// Create new admin data
$admin_data = [
    'username' => $username,
    'password' => $new_hash,
    'wallet_addresses' => [
        'BTC' => 'bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh',
        'ETH' => '0x742d35Cc6634C0532925a3b8D404fddaF789C5C2',
        'LTC' => 'ltc1qw508d6qejxtdg4y5r3zarvary0c5xw7k5zpx'
    ]
];

// Write directly to admin.json
$json_content = json_encode($admin_data, JSON_PRETTY_PRINT);
$admin_file = 'data/admin.json';

echo "<h2>Writing to File:</h2>";
echo "<p><strong>File:</strong> $admin_file</p>";

$result = file_put_contents($admin_file, $json_content);

if ($result !== false) {
    echo "<p style='color: green;'>✓ File written successfully!</p>";
    
    // Verify the file
    if (file_exists($admin_file)) {
        $file_content = file_get_contents($admin_file);
        $decoded = json_decode($file_content, true);
        
        if ($decoded && password_verify($password, $decoded['password'])) {
            echo "<p style='color: green;'>✓ File verification passed!</p>";
            echo "<p style='color: green;'>✓ Admin login should now work!</p>";
            
            echo "<h2>Login Credentials:</h2>";
            echo "<ul>";
            echo "<li><strong>Username:</strong> admin</li>";
            echo "<li><strong>Password:</strong> SecureAdmin123!</li>";
            echo "</ul>";
            
            echo "<p><a href='admin/login.php' class='btn btn-primary btn-lg'>Go to Admin Login</a></p>";
            
            // Show the new file content
            echo "<h2>New admin.json Content:</h2>";
            echo "<pre>" . htmlspecialchars($json_content) . "</pre>";
            
        } else {
            echo "<p style='color: red;'>✗ File verification failed!</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ File was not created!</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Failed to write file!</p>";
    echo "<p>Check file permissions for data/admin.json</p>";
    
    // Try to create the data directory if it doesn't exist
    if (!is_dir('data')) {
        echo "<p>Attempting to create data directory...</p>";
        if (mkdir('data', 0755, true)) {
            echo "<p style='color: green;'>✓ Data directory created!</p>";
            
            // Try writing again
            $result = file_put_contents($admin_file, $json_content);
            if ($result !== false) {
                echo "<p style='color: green;'>✓ File written after creating directory!</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ Failed to create data directory!</p>";
        }
    }
}

echo "<hr>";
echo "<p><small>Fix completed!</small></p>";
?>