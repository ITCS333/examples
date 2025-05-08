<?php
session_start();
header('Content-Type: application/json');

require_once 'config.php';
require_once 'DatabaseHelper.php';

// Create database helper
$db = new DatabaseHelper(
    $config['host'],
    $config['dbname'],
    $config['username'],
    $config['password'],
    $config['options']
);

// Handle different API endpoints
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'register':
        register($db);
        break;
    case 'login':
        login($db);
        break;
    case 'logout':
        logout();
        break;
    case 'upload':
        uploadFile($db);
        break;
    case 'files':
        getFiles($db);
        break;
    case 'download':
        downloadFile($db);
        break;
    case 'delete':
        deleteFile($db);
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}

// Register a new user
function register($db) {
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['username']) || !isset($data['password']) || !isset($data['email'])) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        return;
    }
    
    $username = $data['username'];
    $password = $data['password'];
    $email = $data['email'];
    
    // Validate input
    if (strlen($username) < 3) {
        echo json_encode(['status' => 'error', 'message' => 'Username must be at least 3 characters']);
        return;
    }
    
    if (strlen($password) < 6) {
        echo json_encode(['status' => 'error', 'message' => 'Password must be at least 6 characters']);
        return;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
        return;
    }
    
    try {
        // Register the user
        $result = $db->registerUser($username, $password, $email);
        
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Registration successful']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Registration failed']);
        }
    } catch (PDOException $e) {
        // Check for duplicate entry
        if ($e->getCode() == 23000) {
            echo json_encode(['status' => 'error', 'message' => 'Username or email already exists']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }
}

// Login a user
function login($db) {
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['username']) || !isset($data['password'])) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        return;
    }
    
    $username = $data['username'];
    $password = $data['password'];
    $rememberMe = isset($data['remember_me']) ? $data['remember_me'] : false;
    
    try {
        // Verify user credentials
        $user = $db->verifyUser($username, $password);
        
        if ($user) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // Set cookie if remember me is checked
            if ($rememberMe) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_token', $token, time() + 30 * 24 * 60 * 60, '/', '', false, true);
                
                // In a real app, you would store this token in the database
                // associated with the user for verification
            }
            
            echo json_encode([
                'status' => 'success', 
                'message' => 'Login successful',
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username']
                ]
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid username or password']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

// Logout a user
function logout() {
    // Clear session
    session_unset();
    session_destroy();
    
    // Clear cookies
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/');
    }
    
    echo json_encode(['status' => 'success', 'message' => 'Logout successful']);
}

// Upload a file
function uploadFile($db) {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
        return;
    }
    
    $userId = $_SESSION['user_id'];
    
    // Check if file was uploaded
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $error = isset($_FILES['file']) ? uploadErrorMessage($_FILES['file']['error']) : 'No file uploaded';
        echo json_encode(['status' => 'error', 'message' => $error]);
        return;
    }
    
    $file = $_FILES['file'];
    $filename = $file['name'];
    $mimeType = $file['type'];
    $size = $file['size'];
    $tmpPath = $file['tmp_name'];
    
    // Read file data
    $fileData = file_get_contents($tmpPath);
    
    try {
        // Save file to database
        $result = $db->saveFile($userId, $filename, $mimeType, $size, $fileData);
        
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'File uploaded successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to save file']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

// Get user's files
function getFiles($db) {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
        return;
    }
    
    $userId = $_SESSION['user_id'];
    
    try {
        // Get files for the user
        $files = $db->getUserFiles($userId);
        
        // Format file sizes
        foreach ($files as &$file) {
            $file['size_formatted'] = formatFileSize($file['size']);
        }
        
        echo json_encode(['status' => 'success', 'files' => $files]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

// Download a file
function downloadFile($db) {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
        return;
    }
    
    $userId = $_SESSION['user_id'];
    
    // Check if file ID is provided
    if (!isset($_GET['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'File ID not provided']);
        return;
    }
    
    $fileId = $_GET['id'];
    
    try {
        // Get file from database
        $file = $db->getFile($fileId, $userId);
        
        if (!$file) {
            echo json_encode(['status' => 'error', 'message' => 'File not found']);
            return;
        }
        
        // Set headers for file download
        header('Content-Type: ' . $file['mime_type']);
        header('Content-Disposition: attachment; filename="' . $file['filename'] . '"');
        header('Content-Length: ' . $file['size']);
        
        // Output file data
        echo $file['file_data'];
        exit;
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

// Delete a file
function deleteFile($db) {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
        return;
    }
    
    $userId = $_SESSION['user_id'];
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'File ID not provided']);
        return;
    }
    
    $fileId = $data['id'];
    
    try {
        // Delete file from database
        $result = $db->deleteFile($fileId, $userId);
        
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'File deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete file']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

// Helper function to format file size
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}

// Helper function to get upload error message
function uploadErrorMessage($error) {
    switch ($error) {
        case UPLOAD_ERR_INI_SIZE:
            return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
        case UPLOAD_ERR_FORM_SIZE:
            return 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form';
        case UPLOAD_ERR_PARTIAL:
            return 'The uploaded file was only partially uploaded';
        case UPLOAD_ERR_NO_FILE:
            return 'No file was uploaded';
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'Missing a temporary folder';
        case UPLOAD_ERR_CANT_WRITE:
            return 'Failed to write file to disk';
        case UPLOAD_ERR_EXTENSION:
            return 'A PHP extension stopped the file upload';
        default:
            return 'Unknown upload error';
    }
}
