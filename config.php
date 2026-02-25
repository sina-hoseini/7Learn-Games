<?php
/**
 * Environment Configuration
 * 7Learn Games Platform
 */

// Detect environment
$env = getenv('APP_ENV') ?: 'development';
$isProduction = $env === 'production';

// Database Configuration
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$database = getenv('DB_NAME') ?: 'games_db';
$port = getenv('DB_PORT') ?: 3306;

// Parse DATABASE_URL if provided (Railway/Render)
$dbUrl = getenv('DATABASE_URL');
if ($dbUrl) {
    $dsn = parse_url($dbUrl);
    $host = $dsn['host'] ?? $host;
    $user = $dsn['user'] ?? $user;
    $password = $dsn['pass'] ?? $password;
    $database = ltrim($dsn['path'] ?? '', '/') ?: $database;
    $port = $dsn['port'] ?? $port;
}

// App Configuration
define('APP_ENV', $env);
define('IS_PRODUCTION', $isProduction);
define('DEBUG_MODE', !$isProduction);

// Database Configuration
define('DB_HOST', $host);
define('DB_USER', $user);
define('DB_PASSWORD', $password);
define('DB_NAME', $database);
define('DB_PORT', $port);

// Security
define('SESSION_TIMEOUT', (int)(getenv('SESSION_TIMEOUT') ?: 3600));
define('SECURE_COOKIES', $isProduction);

// App URLs
define('APP_URL', getenv('APP_URL') ?: 'http://localhost/games');
define('API_URL', APP_URL . '/api');

// Return configuration array
return [
    'environment' => APP_ENV,
    'debug' => DEBUG_MODE,
    'database' => [
        'host' => DB_HOST,
        'user' => DB_USER,
        'password' => DB_PASSWORD,
        'name' => DB_NAME,
        'port' => DB_PORT,
    ],
    'session' => [
        'timeout' => SESSION_TIMEOUT,
        'secure' => SECURE_COOKIES,
    ],
    'app' => [
        'url' => APP_URL,
        'api_url' => API_URL,
    ],
];
?>
