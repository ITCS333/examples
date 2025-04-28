<?php
/**
 * REST API for Image Gallery
 * 
 * This file provides REST API endpoints for the image gallery application.
 * It handles requests for images and contact form submissions.
 */

// Set headers for JSON API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include the DatabaseHelper class
require_once 'DatabaseHelper.php';

// Database configuration
$db_host = 'localhost';
$db_name = getenv('db_name') ?? 'image_gallery';
$db_user = getenv('db_user') ?? 'root';
$db_pass = getenv('db_pass') ?? '';

// Create database helper instance
$dbHelper = new DatabaseHelper($db_host, $db_name, $db_user, $db_pass);

// Route the request based on the endpoint
try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Get images endpoint
        $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
        $sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'default';
        
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
        }
        
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
        $stmt = $dbHelper->prepare($sql);
        $stmt->execute($params);
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Return the images as JSON
        echo json_encode([
            'status' => 'success',
            'count' => count($images),
            'data' => $images
        ]);
    } 
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the request body
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        // Check if this is a contact form submission
        if (isset($data['type']) && $data['type'] === 'contact') {
            // Validate the data
            $name = isset($data['name']) ? trim($data['name']) : '';
            $email = isset($data['email']) ? trim($data['email']) : '';
            $message = isset($data['message']) ? trim($data['message']) : '';
            
            if (empty($name) || empty($email) || empty($message)) {
                throw new Exception('Please fill in all fields');
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Please enter a valid email address');
            }
            
            // Insert the contact message
            if ($dbHelper->insertContact($name, $email, $message)) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Thank you for your message! We will get back to you soon.'
                ]);
            } else {
                throw new Exception('Failed to save your message');
            }
        } else {
            throw new Exception('Invalid request type');
        }
    } 
    else {
        // Handle invalid endpoint or method
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'message' => 'Endpoint not found'
        ]);
    }
} catch (Exception $e) {
    // Handle errors
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
