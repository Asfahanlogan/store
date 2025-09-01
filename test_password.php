<?php
/**
 * Password Test Script
 * This script tests the password hash functionality
 */

echo "<h1>Password Hash Test</h1>";

// Test the password
$username = 'admin';
$password = 'SecureAdmin123!';

echo "<h2>Test Credentials:</h2>";
echo "<p><strong>Username:</strong> $username</p>";
echo "<p><strong>Password:</strong> $password</p>";

// Generate a new hash
$new_hash = password_hash($password, PASSWORD_DEFAULT);
echo "<h2>Generated Hash:</h2>";
echo "<p><code>$new_hash</code></p>";

// Test verification
$verify_result = password_verify($password, $new_hash);
echo "<h2>Verification Test:</h2>";
echo "<p>Password verification result: <strong>" . ($verify_result ? 'PASSED' : 'FAILED') . "</strong></p>";

// Test with current admin.json
echo "<h2>Current Admin.json Test:</h2>";
if (file_exists('data/admin.json')) {
    $admin_data = json_decode(file_get_contents('data/admin.json'), true);
    
    if ($admin_data && isset($admin_data['password'])) {
        echo "<p>Current hash in admin.json: <code>" . $admin_data['password'] . "</code></p>";
        
        $current_verify = password_verify($password, $admin_data['password']);
        echo "<p>Current hash verification: <strong>" . ($current_verify ? 'PASSED' : 'FAILED') . "</strong></p>";
        
        if (!$current_verify) {
            echo "<p style='color: red;'>The current hash in admin.json is not working!</p>";
            echo "<p>Let's fix it now...</p>";
            
            // Fix the admin.json file
            $admin_data['password'] = $new_hash;
            unset($admin_data['password_plain']); // Remove plain text password
            
            $result = file_put_contents('data/admin.json', json_encode($admin_data, JSON_PRETTY_PRINT));
            
            if ($result !== false) {
                echo "<p style='color: green;'>✓ admin.json updated with new hash!</p>";
                
                // Test again
                $updated_data = json_decode(file_get_contents('data/admin.json'), true);
                $final_verify = password_verify($password, $updated_data['password']);
                echo "<p>Final verification: <strong>" . ($final_verify ? 'PASSED' : 'FAILED') . "</strong></p>";
                
                if ($final_verify) {
                    echo "<p style='color: green;'>✓ Password fixed! You can now login with:</p>";
                    echo "<ul>";
                    echo "<li><strong>Username:</strong> admin</li>";
                    echo "<li><strong>Password:</strong> SecureAdmin123!</li>";
                    echo "</ul>";
                    echo "<p><a href='admin/login.php' class='btn btn-primary'>Go to Admin Login</a></p>";
                }
            } else {
                echo "<p style='color: red;'>✗ Failed to update admin.json</p>";
            }
        } else {
            echo "<p style='color: green;'>✓ Current hash is working correctly!</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Could not read admin.json or missing password field</p>";
    }
} else {
    echo "<p style='color: red;'>✗ admin.json file not found!</p>";
}

echo "<hr>";
echo "<p><small>Test completed!</small></p>";
?>