<?php
session_start();

// Website Configuration
define('SITE_NAME', 'HackVault Pro');
define('SITE_URL', 'http://localhost');
define('DATA_PATH', __DIR__ . '/data/');

// Database Helper Functions
class JsonDB {
    
    public static function read($file) {
        $path = DATA_PATH . $file;
        if (!file_exists($path)) {
            return [];
        }
        $content = file_get_contents($path);
        return json_decode($content, true) ?: [];
    }
    
    public static function write($file, $data) {
        $path = DATA_PATH . $file;
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));
    }
    
    public static function getNextId($file) {
        $data = self::read($file);
        if (empty($data)) {
            return 1;
        }
        $ids = array_column($data, 'id');
        return max($ids) + 1;
    }
}

// Helper Functions
function formatPrice($price) {
    return '$' . number_format($price, 2);
}

function generateOrderId() {
    return 'ORD' . date('Ymd') . rand(1000, 9999);
}

function isAdmin() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: login.php');
        exit;
    }
}

function redirect($url) {
    header("Location: $url");
    exit;
}

// Crypto wallet addresses
function getCryptoAddresses() {
    $admin = JsonDB::read('admin.json');
    return $admin['wallet_addresses'] ?? [
        'BTC' => 'bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh',
        'ETH' => '0x742d35Cc6634C0532925a3b8D404fddaF789C5C2',
        'LTC' => 'ltc1qw508d6qejxtdg4y5r3zarvary0c5xw7k5zpx'
    ];
}
?>