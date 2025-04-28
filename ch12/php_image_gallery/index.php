<?php
/**
 * Image Gallery Frontend
 * 
 * This file provides the HTML structure for the image gallery application.
 * The actual data is loaded via JavaScript from the API.
 */
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
                <form id="search-form" class="row g-3">
                    <div class="col-md-6">
                        <label for="search" class="form-label">Search by title or source</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               placeholder="Enter keywords...">
                    </div>
                    <div class="col-md-4">
                        <label for="sort" class="form-label">Sort by</label>
                        <select class="form-select" id="sort" name="sort">
                            <option value="default">Random Order</option>
                            <option value="title-asc">Title (A-Z)</option>
                            <option value="title-desc">Title (Z-A)</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Apply</button>
                    </div>
                </form>
            </div>
            <div class="card-footer">
                <p class="mb-0">Showing <span id="total-images">0</span> image(s)<span id="search-term-display"></span></p>
            </div>
        </div>

        <!-- No results message (hidden by default) -->
        <div id="no-results-container" class="alert alert-info d-none">
            <h4>No images found</h4>
            <p>Sorry, no images match your search criteria. Try a different search term.</p>
            <a href="index.php" class="btn btn-outline-primary">Reset Filters</a>
        </div>

        <!-- Image Gallery Container -->
        <div id="gallery-container" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <!-- Images will be loaded here by JavaScript -->
        </div>

        <!-- Contact Form -->
        <div class="card mt-5 shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Contact Us</h5>
            </div>
            <div class="card-body">
                <div id="message-container"></div>
                <form id="contact-form">
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
                    <button type="submit" class="btn btn-success">Send Message</button>
                </form>
            </div>
        </div>

        <footer class="text-center mt-5 py-3 border-top text-muted">
            <p class="mb-0">&copy; <?= date('Y'); ?> PHP Image Gallery Demo</p>
        </footer>
    </div>
    
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/gallery.js"></script>
</body>

</html>
