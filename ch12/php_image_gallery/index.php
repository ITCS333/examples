<?php require 'images.php';

// Initialize variables for form handling
$searchTerm = '';
$filteredImages = $images;
$sortBy = 'default';
$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the search term from the form
    $searchTerm = isset($_POST['search']) ? trim($_POST['search']) : '';
    $sortBy = isset($_POST['sort']) ? $_POST['sort'] : 'default';
    
    // Filter images based on search term (case-insensitive)
    if (!empty($searchTerm)) {
        $filteredImages = array_filter($images, function($image) use ($searchTerm) {
            return (stripos($image['title'], $searchTerm) !== false || 
                   stripos($image['source'], $searchTerm) !== false);
        });
    }

    // Contact form handling
    if (isset($_POST['contact_submit'])) {
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
        
        // Simple validation
        if (empty($name) || empty($email) || empty($comment)) {
            $message = '<div class="alert alert-danger">Please fill in all fields</div>';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = '<div class="alert alert-danger">Please enter a valid email address</div>';
        } else {
            // In a real application, you would process the form data here (e.g., save to database, send email)
            $message = '<div class="alert alert-success">Thank you for your message, ' . htmlspecialchars($name) . '! We will get back to you soon.</div>';
        }
    }
    
    // Sort images
    if ($sortBy === 'title-asc') {
        usort($filteredImages, function($a, $b) {
            return strcmp($a['title'], $b['title']);
        });
    } elseif ($sortBy === 'title-desc') {
        usort($filteredImages, function($a, $b) {
            return strcmp($b['title'], $a['title']);
        });
    }
} else {
    // Default ordering
    shuffle($filteredImages);
}

// Count filtered images
$totalImages = count($filteredImages);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Image Gallery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light py-4">
    <div class="container">
        <header class="text-center mb-4">
            <h1 class="display-4">PHP Image Gallery</h1>
            <p class="text-muted">A responsive grid of beautiful images</p>
        </header>

        <!-- Search and Filter Form -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Search & Filter Images</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="row g-3">
                    <div class="col-md-6">
                        <label for="search" class="form-label">Search by title or source</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               placeholder="Enter keywords..." value="<?= htmlspecialchars($searchTerm); ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="sort" class="form-label">Sort by</label>
                        <select class="form-select" id="sort" name="sort">
                            <option value="default" <?= ($sortBy === 'default') ? 'selected' : ''; ?>>Random Order</option>
                            <option value="title-asc" <?= ($sortBy === 'title-asc') ? 'selected' : ''; ?>>Title (A-Z)</option>
                            <option value="title-desc" <?= ($sortBy === 'title-desc') ? 'selected' : ''; ?>>Title (Z-A)</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Apply</button>
                    </div>
                </form>
            </div>
            <div class="card-footer">
                <p class="mb-0">Showing <?= $totalImages; ?> image<?= ($totalImages !== 1) ? 's' : ''; ?>
                <?php if (!empty($searchTerm)): ?>
                    for search: "<strong><?= htmlspecialchars($searchTerm); ?></strong>"
                <?php endif; ?>
                </p>
            </div>
        </div>

        <!-- Display results or no results message -->
        <?php if (empty($filteredImages)): ?>
            <div class="alert alert-info">
                <h4>No images found</h4>
                <p>Sorry, no images match your search criteria. Try a different search term.</p>
                <a href="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="btn btn-outline-primary">Reset Filters</a>
            </div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($filteredImages as $image) : ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <img src="<?= $image['url']; ?>"
                            class="card-img-top" style="height: 200px; object-fit: cover;"
                            alt="<?= $image['title']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= $image['title']; ?></h5>
                            <p class="card-text text-muted small">Source: <?= $image['source']; ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Contact Form -->
        <div class="card mt-5 shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Contact Us</h5>
            </div>
            <div class="card-body">
                <?= $message; ?>
                <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="mb-3">
                        <label for="name" class="form-label">Your Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label">Your Message</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-success" name="contact_submit" value="1">Send Message</button>
                </form>
            </div>
        </div>

        <footer class="text-center mt-5 py-3 border-top text-muted">
            <p class="mb-0">&copy; <?= date('Y'); ?> PHP Image Gallery Demo</p>
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
