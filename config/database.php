<?php

/**
 * Database Configuration
 * Sistem Manajemen Produk Multi-Kategori
 */

// Load environment variables from .env file (manual implementation)
function loadEnvFile($path)
{
    if (!file_exists($path)) {
        return false;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);

        // Skip comments and empty lines
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }

        // Parse key=value pairs
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            // Remove quotes if present
            if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                (substr($value, 0, 1) === "'" && substr($value, -1) === "'")
            ) {
                $value = substr($value, 1, -1);
            }

            if (!empty($name)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
    return true;
}

// Load .env file
$envLoaded = loadEnvFile(__DIR__ . '/../.env');

// Try to use vlucas/phpdotenv if available (Composer dependency)
if (!$envLoaded && class_exists('Dotenv\Dotenv')) {
    try {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->load();
    } catch (Exception $e) {
        // Silently fail if .env loading fails
        error_log("Warning: Could not load .env file: " . $e->getMessage());
    }
}

class Database
{
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;

    public function __construct()
    {
        // Load database configuration from environment variables (NO HARDCODED FALLBACKS)
        $this->host = $_ENV['DB_HOST'] ?? getenv('DB_HOST');
        $this->db_name = $_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE');
        $this->username = $_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME');
        $this->password = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD');

        // Validate required environment variables - NO FALLBACK CREDENTIALS ALLOWED
        if (empty($this->host) || empty($this->db_name) || empty($this->username)) {
            throw new Exception(
                "Missing required database environment variables. " .
                    "Please ensure your .env file exists and contains: DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD"
            );
        }
    }

    /**
     * Get database connection
     */
    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);

            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }

            $this->conn->set_charset("utf8mb4");
        } catch (Exception $e) {
            echo "<div style='color: red; padding: 20px; background: #ffe6e6; border: 1px solid red; margin: 20px; border-radius: 5px;'>";
            echo "<strong>Database Connection Error:</strong><br>";
            echo $e->getMessage() . "<br><br>";
            echo "<strong>Troubleshooting:</strong><br>";
            echo "1. Make sure MySQL server is running<br>";
            echo "2. Verify database credentials in config/database.php<br>";
            echo "3. Import database.sql file first<br>";
            echo "</div>";
            die();
        }

        return $this->conn;
    }

    /**
     * Close database connection
     */
    public function closeConnection()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
