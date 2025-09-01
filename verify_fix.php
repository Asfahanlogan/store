<?php
/**
 * Verify Admin Fix
 * This script verifies that the admin login fix is working
 */

echo "<h1>Admin Login Fix Verification</h1>";

// Test the password
$username = 'admin';
$password = 'SecureAdmin123!';

echo "<h2>Test Credentials:</h2>";
echo "<p><strong>Username:</strong> $username</p>";
echo "<p><strong>Password:</strong> $password</p>";

// Read the current admin.json
if (file_exists('data/admin.json')) {
    $admin_data = json_decode(file_get_contents('data/admin.json'), true);
    
    if ($admin_data && isset($admin_data['password'])) {
        echo "<h2>Current Admin.json:</h2>";
        echo "<p><strong>Username:</strong> " . ($admin_data['username'] ?? 'NOT SET') . "</p>";
        echo "<p><strong>Password hash:</strong> " . (isset($admin_data['password']) ? 'SET' : 'NOT SET') . "</p>";
        echo "<p><strong>Hash length:</strong> " . (isset($admin_data['password']) ? strlen($admin_data['password']) : 'N/A') . "</p>";
        
        // Test password verification
        $verify_result = password_verify($password, $admin_data['password']);
        echo "<h2>Password Verification Test:</h2>";
        echo "<p>Result: <strong style='color: " . ($verify_result ? 'green' : 'red') . ";'>" . ($verify_result ? 'PASSED' : 'FAILED') . "</strong></p>";
        
        if ($verify_result) {
            echo "<p style='color: green;'>✓ Password verification is working!</p>";
            echo "<p style='color: green;'>✓ Admin login should now work!</p>";
            
            echo "<h2>Login Instructions:</h2>";
            echo "<p>You can now login to the admin panel with:</p>";
            echo "<ul>";
            echo "<li><strong>Username:</strong> admin</li>";
            echo "<li><strong>Password:</strong> SecureAdmin123!</li>";
            echo "</ul>";
            
            echo "<p><a href='admin/login.php' class='btn btn-primary btn-lg'>Go to Admin Login</a></p>";
            
        } else {
            echo "<p style='color: red;'>✗ Password verification is still failing!</p>";
            echo "<p>Let me generate a new working hash...</p>";
            
            // Generate a new hash
            $new_hash = password_hash($password, PASSWORD_DEFAULT);
            echo "<p><strong>New hash:</strong> <code>$new_hash</code></p>";
            
            // Test the new hash
            if (password_verify($password, $new_hash)) {
                echo "<p style='color: green;'>✓ New hash verification passed!</p>";
                
                // Update the file
                $admin_data['password'] = $new_hash;
                $result = file_put_contents('data/admin.json', json_encode($admin_data, JSON_PRETTY_PRINT));
                
                if ($result !== false) {
                    echo "<p style='color: green;'>✓ admin.json updated with new hash!</p>";
                    echo "<p style='color: green;'>✓ Admin login should now work!</p>";
                } else {
                    echo "<p style='color: red;'>✗ Failed to update admin.json</p>";
                }
            }
        }
        
    } else {
        echo "<p style='color: red;'>✗ Could not read admin.json or missing password field</p>";
    }
} else {
    echo "<p style='color: red;'>✗ admin.json file not found!</p>";
}

echo "<hr>";
echo "<p><small>Verification completed!</small></p>";
?>