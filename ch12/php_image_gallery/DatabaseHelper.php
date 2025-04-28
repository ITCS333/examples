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
}
?>
