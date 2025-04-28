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
        
        // Get images using the DatabaseHelper method
        $images = $dbHelper->getImages($searchTerm, $sortBy);
        
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
