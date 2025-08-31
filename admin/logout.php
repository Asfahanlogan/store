<?php
require_once '../config.php';

// Destroy session
$_SESSION = array();
session_destroy();

// Redirect to login page
redirect('login.php');
?>