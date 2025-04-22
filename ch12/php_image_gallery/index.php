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
        <header class="text-center mb-5">
            <h1 class="display-4">PHP Image Gallery</h1>
            <p class="text-muted">A responsive grid of beautiful images</p>
        </header>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <img src="https://picsum.photos/id/1043/600/400"
                        class="card-img-top" style="height: 200px; object-fit: cover;"
                        alt="Mountain Landscape">
                    <div class="card-body">
                        <h5 class="card-title">Mountain Landscape</h5>
                        <p class="card-text text-muted small">Source: Picsum Photos</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <img src="https://picsum.photos/id/1039/600/400"
                        class="card-img-top" style="height: 200px; object-fit: cover;"
                        alt="Calm Lake View">
                    <div class="card-body">
                        <h5 class="card-title">Calm Lake View</h5>
                        <p class="card-text text-muted small">Source: Picsum Photos</p>
                    </div>
                </div>
            </div>
        </div>

        <footer class="text-center mt-5 py-3 border-top text-muted">
            <p class="mb-0">&copy; 2025 PHP Image Gallery Demo</p>
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
