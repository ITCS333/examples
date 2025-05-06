<?php
require_once 'config.php';

class DatabaseHelper {
    private $host;
    private $dbName;
    private $username;
    private $password;
    private $options;
    private $pdo;
    
    /**
     * Constructor
     */
    public function __construct($host, $dbName, $username, $password, $options = []) {
        $this->host = $host;
        $this->dbName = $dbName;
        $this->username = $username;
        $this->password = $password;
        $this->options = $options;
    }
    
    /**
     * Get PDO connection
     */
    public function getPDO() {
        if (!$this->pdo) {
            try {
                // Create connection to MySQL server
                $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset=utf8mb4";
                $this->pdo = new PDO($dsn, $this->username, $this->password, $this->options);
            } catch (PDOException $e) {
                // If database doesn't exist, create it
                if ($e->getCode() == 1049) {
                    $this->createDatabase();
                    // Try connection again with the new database
                    $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset=utf8mb4";
                    $this->pdo = new PDO($dsn, $this->username, $this->password, $this->options);
                } else {
                    throw $e;
                }
            }
        }
        return $this->pdo;
    }
    
    /**
     * Create database if it doesn't exist
     */
    private function createDatabase() {
        try {
            $pdo = new PDO("mysql:host={$this->host}", $this->username, $this->password, $this->options);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$this->dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        } catch (PDOException $e) {
            die("Error creating database: " . $e->getMessage());
        }
    }
    
    /**
     * Execute a query
     */
    public function query($sql) {
        return $this->getPDO()->query($sql);
    }
    
    /**
     * Prepare a statement
     */
    public function prepare($sql) {
        return $this->getPDO()->prepare($sql);
    }
    
    /**
     * Execute SQL
     */
    public function exec($sql) {
        return $this->getPDO()->exec($sql);
    }
    
    /**
     * Create users table
     */
    public function createUsersTable() {
        $sql = "CREATE TABLE IF NOT EXISTS `users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `username` VARCHAR(50) NOT NULL UNIQUE,
            `password` VARCHAR(255) NOT NULL,
            `email` VARCHAR(100) NOT NULL UNIQUE,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        $this->exec($sql);
    }
    
    /**
     * Create files table
     */
    public function createFilesTable() {
        $sql = "CREATE TABLE IF NOT EXISTS `files` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `filename` VARCHAR(255) NOT NULL,
            `mime_type` VARCHAR(100) NOT NULL,
            `size` INT NOT NULL,
            `file_data` LONGBLOB NOT NULL,
            `uploaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        $this->exec($sql);
    }
    
    /**
     * Register a new user
     */
    public function registerUser($username, $password, $email) {
        // Make sure the users table exists
        $this->createUsersTable();
        
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Prepare and execute the insert statement
        $stmt = $this->prepare("INSERT INTO `users` (`username`, `password`, `email`) VALUES (?, ?, ?)");
        return $stmt->execute([$username, $hashedPassword, $email]);
    }
    
    /**
     * Verify user login
     */
    public function verifyUser($username, $password) {
        $stmt = $this->prepare("SELECT * FROM `users` WHERE `username` = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * Save a file to the database
     */
    public function saveFile($userId, $filename, $mimeType, $size, $fileData) {
        // Make sure the files table exists
        $this->createFilesTable();
        
        // Prepare and execute the insert statement
        $stmt = $this->prepare("INSERT INTO `files` (`user_id`, `filename`, `mime_type`, `size`, `file_data`) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$userId, $filename, $mimeType, $size, $fileData]);
    }
    
    /**
     * Get files for a user
     */
    public function getUserFiles($userId) {
        $stmt = $this->prepare("SELECT `id`, `filename`, `mime_type`, `size`, `uploaded_at` FROM `files` WHERE `user_id` = ? ORDER BY `uploaded_at` DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get a file by ID
     */
    public function getFile($fileId, $userId) {
        $stmt = $this->prepare("SELECT * FROM `files` WHERE `id` = ? AND `user_id` = ?");
        $stmt->execute([$fileId, $userId]);
        return $stmt->fetch();
    }
    
    /**
     * Delete a file
     */
    public function deleteFile($fileId, $userId) {
        $stmt = $this->prepare("DELETE FROM `files` WHERE `id` = ? AND `user_id` = ?");
        return $stmt->execute([$fileId, $userId]);
    }
}
