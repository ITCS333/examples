<?php
/**
 * Database Helper Class
 * 
 * This class provides methods for database operations
 * using PDO for MySQL connections.
 */
class DatabaseHelper {
    private $host;
    private $dbName;
    private $username;
    private $password;
    private $pdo;
    
    /**
     * Constructor
     * 
     * @param string $host Database host
     * @param string $dbName Database name
     * @param string $username Database username
     * @param string $password Database password
     */
    public function __construct($host, $dbName, $username, $password) {
        $this->host = $host;
        $this->dbName = $dbName;
        $this->username = $username;
        $this->password = $password;
    }
    
    /**
     * Get PDO connection
     * 
     * @return PDO The PDO connection object
     * @throws PDOException If connection fails
     */
    public function getPDO() {
        if (!$this->pdo) {
            // Create initial connection to MySQL server
            $this->pdo = new PDO("mysql:host={$this->host};charset=utf8mb4", 
                                $this->username, 
                                $this->password);
            
            // Set error mode to exceptions
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create database if it doesn't exist
            $this->pdo->exec("CREATE DATABASE IF NOT EXISTS `{$this->dbName}`");
            $this->pdo->exec("USE `{$this->dbName}`");
        }
        
        return $this->pdo;
    }
    
    /**
     * Execute a query
     * 
     * @param string $sql SQL query to execute
     * @return PDOStatement The result of the query
     * @throws PDOException If query fails
     */
    public function query($sql) {
        return $this->getPDO()->query($sql);
    }
    
    /**
     * Prepare a statement
     * 
     * @param string $sql SQL statement to prepare
     * @return PDOStatement The prepared statement
     * @throws PDOException If preparation fails
     */
    public function prepare($sql) {
        return $this->getPDO()->prepare($sql);
    }
    
    /**
     * Execute a SQL statement directly
     * 
     * @param string $sql SQL statement to execute
     * @return int Number of affected rows
     * @throws PDOException If execution fails
     */
    public function exec($sql) {
        return $this->getPDO()->exec($sql);
    }
    
    /**
     * Create contacts table if it doesn't exist
     * 
     * @return bool True if successful
     * @throws PDOException If creation fails
     */
    public function createContactsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS `contacts` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL,
            `email` VARCHAR(100) NOT NULL,
            `message` TEXT NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        $this->exec($sql);
        return true;
    }
    
    /**
     * Insert a new contact message
     * 
     * @param string $name The name of the contact
     * @param string $email The email of the contact
     * @param string $message The message content
     * @return bool True if successful
     * @throws PDOException If insertion fails
     */
    public function insertContact($name, $email, $message) {
        // Make sure the contacts table exists
        $this->createContactsTable();
        
        // Prepare and execute the insert statement
        $stmt = $this->prepare("INSERT INTO `contacts` (`name`, `email`, `message`) VALUES (?, ?, ?)");
        return $stmt->execute([$name, $email, $message]);
    }
}
?>
