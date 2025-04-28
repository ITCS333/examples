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
    
    /**
     * Create images table if it doesn't exist and populate with sample data if empty
     * 
     * @return array The sample images that were inserted (if any)
     * @throws PDOException If creation or insertion fails
     */
    public function createAndPopulateImagesTable() {
        // Create images table if it doesn't exist
        $this->exec("CREATE TABLE IF NOT EXISTS `images` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `url` VARCHAR(255) NOT NULL,
            `title` VARCHAR(100) NOT NULL,
            `source` VARCHAR(100) NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        
        // Check if the table is empty
        $stmt = $this->query("SELECT COUNT(*) FROM `images`");
        $count = $stmt->fetchColumn();
        
        // If table is empty, insert sample data
        if ($count == 0) {
            // Sample image data
            $sampleImages = [
                [
                    'url' => 'https://picsum.photos/id/1015/600/400',
                    'title' => 'Scenic Mountain Lake',
                    'source' => 'Picsum Photos'
                ],
                [
                    'url' => 'https://picsum.photos/id/1018/600/400',
                    'title' => 'Foggy Mountains',
                    'source' => 'Picsum Photos'
                ],
                [
                    'url' => 'https://picsum.photos/id/1019/600/400',
                    'title' => 'Forest Waterfall',
                    'source' => 'Picsum Photos'
                ],
                [
                    'url' => 'https://picsum.photos/id/1022/600/400',
                    'title' => 'Northern Lights',
                    'source' => 'Picsum Photos'
                ],
                [
                    'url' => 'https://picsum.photos/id/1035/600/400',
                    'title' => 'Snowy Mountain Peaks',
                    'source' => 'Picsum Photos'
                ],
                [
                    'url' => 'https://picsum.photos/id/1039/600/400',
                    'title' => 'Calm Lake View',
                    'source' => 'Picsum Photos'
                ],
                [
                    'url' => 'https://picsum.photos/id/1043/600/400',
                    'title' => 'Mountain Landscape',
                    'source' => 'Picsum Photos'
                ],
                [
                    'url' => 'https://picsum.photos/id/1044/600/400',
                    'title' => 'Rocky Coastline',
                    'source' => 'Picsum Photos'
                ],
                [
                    'url' => 'https://picsum.photos/id/1047/600/400',
                    'title' => 'Town by the Sea',
                    'source' => 'Picsum Photos'
                ],
                [
                    'url' => 'https://picsum.photos/id/1050/600/400',
                    'title' => 'Mountain Road',
                    'source' => 'Picsum Photos'
                ],
                [
                    'url' => 'https://picsum.photos/id/1051/600/400',
                    'title' => 'Autumn Forest',
                    'source' => 'Picsum Photos'
                ],
                [
                    'url' => 'https://picsum.photos/id/1059/600/400',
                    'title' => 'Desert Road',
                    'source' => 'Picsum Photos'
                ]
            ];
            
            // Prepare insert statement
            $stmt = $this->prepare("INSERT INTO `images` (`url`, `title`, `source`) VALUES (?, ?, ?)");
            
            // Insert each image
            foreach ($sampleImages as $image) {
                $stmt->execute([$image['url'], $image['title'], $image['source']]);
            }
            
            return $sampleImages;
        }
        
        return [];
    }
    
    /**
     * Get images with optional search and sort
     * 
     * @param string $searchTerm Optional search term to filter images
     * @param string $sortBy Optional sort order (default, title-asc, title-desc)
     * @return array Array of image objects
     * @throws PDOException If query fails
     */
    public function getImages($searchTerm = '', $sortBy = 'default') {
        // Make sure the images table exists and has data
        $this->createAndPopulateImagesTable();
        
        // Build the SQL query based on search term
        $sql = "SELECT `url`, `title`, `source` FROM `images`";
        $params = [];
        
        if (!empty($searchTerm)) {
            $sql .= " WHERE `title` LIKE ? OR `source` LIKE ?";
            $params[] = "%$searchTerm%";
            $params[] = "%$searchTerm%";
        }
        
        // Add ordering based on sort parameter
        if ($sortBy === 'title-asc') {
            $sql .= " ORDER BY `title` ASC";
        } elseif ($sortBy === 'title-desc') {
            $sql .= " ORDER BY `title` DESC";
        } else {
            $sql .= " ORDER BY `id` ASC";
        }
        
        // Execute the query
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
