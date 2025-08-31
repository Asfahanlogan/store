<?php
require_once '../config.php';

// Redirect to dashboard if logged in, otherwise to login page
if (isAdmin()) {
    redirect('dashboard.php');
} else {
    redirect('login.php');
}
?>