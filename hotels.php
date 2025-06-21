<?php
require_once 'config.php';
require_once 'auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$auth = new Auth();
$isLoggedIn = $auth->isLoggedIn();
$currentUser = $auth->getCurrentUser();

// Get filter parameters
$location = $_GET['location'] ?? '';
$minPrice = $_GET['min_price'] ?? '';
$maxPrice = $_GET['max_price'] ?? '';
$guests = $_GET['guests'] ?? '';
$rooms = $_GET['rooms'] ?? '';

// Build the query with filters
$query = "SELECT h.*, MIN(r.cost_night) as min_price, MAX(r.cost_night) as max_price
          FROM hotels h
          LEFT JOIN rooms r ON h.Hotel_ID = r.H_Hotel_ID
          WHERE 1=1";
$params = [];

if ($location) {
    $query .= " AND h.Location LIKE ?";
    $params[] = "%$location%";
}
if ($minPrice !== '') {
    $query .= " AND r.cost_night >= ?";
    $params[] = $minPrice;
}
if ($maxPrice !== '') {
    $query .= " AND r.cost_night <= ?";
    $params[] = $maxPrice;
}
$query .= " GROUP BY h.Hotel_ID ORDER BY h.Stars DESC, h.Name";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$hotels = $stmt->fetchAll();

// Get unique locations for filter dropdown
$locations = $pdo->query("SELECT DISTINCT Location FROM hotels ORDER BY Location")->fetchAll(PDO::FETCH_COLUMN);
// Get min/max price for all rooms
$priceRange = $pdo->query("SELECT MIN(cost_night) as min_price, MAX(cost_night) as max_price FROM rooms WHERE cost_night IS NOT NULL")->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotels - Tourza</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="./styles.css" rel="stylesheet">
    <style>
        .hotel-card {
            transition: transform 0.2s;
            height: 100%;
        }
        .hotel-card:hover {
            transform: translateY(-5px);
        }
        .hotel-price {
            font-size: 1.2rem;
            font-weight: bold;
            color: #0d6efd;
        }
        .hotel-features {
            list-style: none;
            padding-left: 0;
        }
        .hotel-features li {
            margin-bottom: 0.5rem;
        }
        .hotel-features i {
            color: #0d6efd;
            margin-right: 0.5rem;
        }
        .filter-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        .price-range {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        .price-range input {
            width: 120px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php"><img src="./images/logo.svg" alt="tourza brand icon"></a>
            <ul class="navbar-nav flex-row ms-3 justify-content-center" style="gap: 1.5rem; font-weight: 500; font-size: 1.1rem;">
                <li class="nav-item"><a class="nav-link px-2" href="index.php#flight-search">Flights</a></li>
                <li class="nav-item"><a class="nav-link px-2 active" href="hotels.php">Hotels</a></li>
                <li class="nav-item"><a class="nav-link px-2" href="tours.php">Tours</a></li>
            </ul>
            <div class="d-flex">
                <?php if ($isLoggedIn): ?>
                    <div class="dropdown">
                        <button class="btn btn-link dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?php echo $currentUser['profile_picture'] ?? './images/default-avatar.png'; ?>" alt="Profile" class="rounded-circle me-2" style="width: 24px; height: 24px;">
                            <?php echo $currentUser['first_name']; ?>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                            <li><a class="dropdown-item" href="bookings.php">My Bookings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" onclick="logout()">Sign Out</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#authModal">
                        Sign In
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <h1 class="mb-4">Hotels</h1>
        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="location" class="form-label">Location</label>
                    <select class="form-select" id="location" name="location">
                        <option value="">All Locations</option>
                        <?php foreach ($locations as $loc): ?>
                            <option value="<?php echo htmlspecialchars($loc); ?>" <?php echo $location === $loc ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($loc); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Price Range (per night)</label>
                    <div class="price-range">
                        <input type="number" class="form-control" name="min_price" placeholder="Min Price" value="<?php echo $minPrice; ?>" min="<?php echo $priceRange['min_price']; ?>">
                        <span>to</span>
                        <input type="number" class="form-control" name="max_price" placeholder="Max Price" value="<?php echo $maxPrice; ?>" max="<?php echo $priceRange['max_price']; ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Guests & Rooms</label>
                    <div class="d-flex gap-2">
                        <input type="number" class="form-control" name="guests" placeholder="Guests" value="<?php echo htmlspecialchars($guests); ?>" min="1">
                        <input type="number" class="form-control" name="rooms" placeholder="Rooms" value="<?php echo htmlspecialchars($rooms); ?>" min="1">
                    </div>
                </div>
                <div class="col-md-12 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Apply Filters</button>
                    <a href="hotels.php" class="btn btn-outline-secondary">Clear Filters</a>
                </div>
            </form>
        </div>

        <div class="row g-4">
            <?php if (empty($hotels)): ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        No hotels found matching your criteria. Please try different filters.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($hotels as $hotel): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card hotel-card shadow-sm h-100">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title mb-1"><?php echo htmlspecialchars($hotel['Name']); ?></h5>
                                <div class="mb-2 text-muted"><?php echo htmlspecialchars($hotel['Location']); ?> &bull; <?php echo $hotel['Stars']; ?>★</div>
                                <ul class="hotel-features mb-2">
                                    <li><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($hotel['Location']); ?></li>
                                    <li><i class="fas fa-star"></i> <?php echo $hotel['Stars']; ?> Star</li>
                                </ul>
                                <div class="mt-auto d-flex justify-content-between align-items-center">
                                    <div class="hotel-price">
                                        <?php
                                        // Fetch the lowest cost_night for this hotel
                                        $roomStmt = $pdo->prepare("SELECT MIN(cost_night) as min_cost FROM rooms WHERE H_Hotel_ID = ?");
                                        $roomStmt->execute([$hotel['Hotel_ID']]);
                                        $minRoom = $roomStmt->fetch();
                                        $minRoomPrice = $minRoom && $minRoom['min_cost'] ? $minRoom['min_cost'] : null;
                                        ?>
                                        <?php if ($hotel['min_price'] && $hotel['max_price'] && $hotel['min_price'] != $hotel['max_price']): ?>
                                            $<?php echo number_format($hotel['min_price']); ?> - $<?php echo number_format($hotel['max_price']); ?>
                                        <?php elseif ($minRoomPrice): ?>
                                            $<?php echo number_format($minRoomPrice); ?>
                                        <?php else: ?>
                                            No rooms available
                                        <?php endif; ?>
                                        <span class="text-muted" style="font-size:0.9em;">/night</span>
                                    </div>
                                    <a href="rooms.php?hotel_id=<?php echo $hotel['Hotel_ID']; ?>" class="btn btn-primary">View Rooms</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'auth-modal.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="auth.js"></script>
    <script>
    function logout() {
        $.post('auth.php', JSON.stringify({action: 'logout'}), function() {
            window.location.reload();
        });
    }
    </script>
</body>
</html> 