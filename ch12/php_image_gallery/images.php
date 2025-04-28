<?php
/**
 * Image Data from MySQL Database
 * 
 * This file connects to a MySQL database using the DatabaseHelper class,
 * creates the images table if it doesn't exist,
 * populates it with sample data if empty,
 * and retrieves the image data.
 */

// Include the DatabaseHelper class
require_once 'DatabaseHelper.php';

// Database configuration
$db_host = 'localhost';
$db_name = getenv('DB_NAME') ?: 'image_gallery';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') ?: '';

// Initialize images array
$images = [];

try {
    // Create database helper instance
    $dbHelper = new DatabaseHelper($db_host, $db_name, $db_user, $db_pass);
    
    // Get PDO connection
    $pdo = $dbHelper->getPDO();
    
    // Create and populate images table if needed, then fetch all images
    $dbHelper->createAndPopulateImagesTable();
    $images = $dbHelper->getImages();
    
    echo "<!-- Database initialized with images -->\n";
    
} catch (PDOException $e) {
    // If database connection fails, use static array as fallback
    echo "<!-- Database error: " . htmlspecialchars($e->getMessage()) . " -->\n";
    
    // Fallback image data
    $images = [
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
        ]
    ];
}
?>
