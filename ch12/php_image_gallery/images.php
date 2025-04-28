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
    
    // Create images table if it doesn't exist
    $dbHelper->exec("CREATE TABLE IF NOT EXISTS `images` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `url` VARCHAR(255) NOT NULL,
        `title` VARCHAR(100) NOT NULL,
        `source` VARCHAR(100) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    // Check if the table is empty
    $stmt = $dbHelper->query("SELECT COUNT(*) FROM `images`");
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
        $stmt = $dbHelper->prepare("INSERT INTO `images` (`url`, `title`, `source`) VALUES (?, ?, ?)");
        
        // Insert each image
        foreach ($sampleImages as $image) {
            $stmt->execute([$image['url'], $image['title'], $image['source']]);
        }
        
        echo "<!-- Database initialized with sample images -->\n";
    }
    
    // Fetch all images from database
    $stmt = $dbHelper->query("SELECT `url`, `title`, `source` FROM `images` ORDER BY `id`");
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
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
