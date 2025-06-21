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
$destination = $_GET['destination'] ?? '';
$minPrice = $_GET['min_price'] ?? '';
$maxPrice = $_GET['max_price'] ?? '';

// Build the query with filters
$query = "SELECT t.*, h.Name as Hotel_Name, h.Stars, h.Location as Hotel_Location, 
          f.airline, f.departure_time, f.arrival_time, f.departure_airport_code, f.arrival_airport_code, f.departure_city, f.arrival_city, f.departure_country, f.arrival_country,
          rc.Model as Car_Model, rc.Capacity as Car_Capacity
          FROM tours t
          LEFT JOIN hotels h ON t.H_Hotel_ID = h.Hotel_ID
          LEFT JOIN flights f ON t.P_Plane_ID = f.id
          LEFT JOIN rental_cars rc ON t.C_Car_ID = rc.Car_ID
          WHERE 1=1";

$params = [];

if ($destination) {
    $query .= " AND t.Destination LIKE ?";
    $params[] = "%$destination%";
}

if ($minPrice !== '') {
    $query .= " AND t.Price_Per_Person >= ?";
    $params[] = $minPrice;
}

if ($maxPrice !== '') {
    $query .= " AND t.Price_Per_Person <= ?";
    $params[] = $maxPrice;
}

$query .= " ORDER BY t.Tour_Start_Date";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$tours = $stmt->fetchAll();

// Get unique destinations for filter dropdown
$destinations = $pdo->query("SELECT DISTINCT Destination FROM tours ORDER BY Destination")->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Packages - Tourza</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="./styles.css" rel="stylesheet">
    <style>
        .tour-card {
            transition: transform 0.2s;
            height: 100%;
        }
        .tour-card:hover {
            transform: translateY(-5px);
        }
        .tour-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #0d6efd;
        }
        .tour-features {
            list-style: none;
            padding-left: 0;
        }
        .tour-features li {
            margin-bottom: 0.5rem;
        }
        .tour-features i {
            color: #0d6efd;
            margin-right: 0.5rem;
        }
        .tour-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
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
        /* Dropdown styling to match flights */
        .destination-dropdown.airport-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            max-height: 260px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .airport-option {
            padding: 12px 16px;
            cursor: pointer;
            transition: background-color 0.2s;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1rem;
        }
        .airport-option:hover, .airport-option.active {
            background-color: #e6f0ff;
            color: #0d6efd;
        }
        .airport-name {
            font-weight: 600;
            color: #0d6efd;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php"><img src="./images/logo.svg" alt="tourza brand icon"></a>
            <ul class="navbar-nav flex-row ms-3 justify-content-center" style="gap: 1.5rem; font-weight: 500; font-size: 1.1rem;">
                <li class="nav-item"><a class="nav-link px-2" href="index.php#flight-search">Flights</a></li>
                <li class="nav-item"><a class="nav-link px-2" href="index.php#hotel-search">Hotels</a></li>
                <li class="nav-item"><a class="nav-link px-2 active" href="tours.php">Tours</a></li>
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
        <h1 class="mb-4">Tour Packages</h1>
        
        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="destination" class="form-label">Destination</label>
                    <div class="location-input" style="position:relative;">
                        <input type="text" class="form-control" id="destination" name="destination" value="<?php echo htmlspecialchars($destination); ?>" placeholder="Select Destination" autocomplete="off">
                        <div class="airport-dropdown destination-dropdown"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Price Range</label>
                    <div class="price-range">
                        <input type="number" class="form-control" name="min_price" placeholder="Min Price" value="<?php echo $minPrice; ?>">
                        <span>to</span>
                        <input type="number" class="form-control" name="max_price" placeholder="Max Price" value="<?php echo $maxPrice; ?>">
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Apply Filters</button>
                    <a href="tours.php" class="btn btn-outline-secondary">Clear Filters</a>
                </div>
            </form>
        </div>

        <div class="row g-4">
            <?php if (empty($tours)): ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        No tours found matching your criteria. Please try different filters.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($tours as $tour): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card tour-card shadow-sm">
                            <div class="card-body">
                                <?php if ($tour['Includes_Car_Rental']): ?>
                                    <span class="badge bg-success tour-badge">Car Included</span>
                                <?php endif; ?>
                                <h5 class="card-title"><?php echo htmlspecialchars($tour['Tour_Name']); ?></h5>
                                <p class="card-text text-muted"><?php echo htmlspecialchars($tour['Description']); ?></p>
                                
                                <ul class="tour-features">
                                    <li><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($tour['Destination']); ?></li>
                                    <li><i class="fas fa-calendar-alt"></i> <?php echo date('d M Y', strtotime($tour['Tour_Start_Date'])); ?></li>
                                    <li><i class="fas fa-clock"></i> <?php echo $tour['Duration_Days']; ?> Days</li>
                                    <li><i class="fas fa-hotel"></i> <?php echo htmlspecialchars($tour['Hotel_Name']); ?> (<?php echo $tour['Stars']; ?>★)</li>
                                    <li><i class="fas fa-plane"></i> <?php echo htmlspecialchars($tour['airline']); ?></li>
                                    <?php if ($tour['Includes_Car_Rental']): ?>
                                        <li><i class="fas fa-car"></i> <?php echo htmlspecialchars($tour['Car_Model']); ?> (<?php echo $tour['Car_Capacity']; ?> seats)</li>
                                    <?php endif; ?>
                                    <li><i class="fas fa-users"></i> Group Size: <?php echo $tour['Min_People']; ?>-<?php echo $tour['Max_People']; ?> people</li>
                                </ul>
                                
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div class="tour-price">$<?php echo number_format($tour['Price_Per_Person'], 2); ?></div>
                                    <a class="btn btn-primary" href="checkout.php?type=tour&id=<?php echo $tour['Tour_ID']; ?>">Book Now</a>
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
    function bookTour(tourId) {
        <?php if ($isLoggedIn): ?>
            window.location.href = `book-tour.php?tour_id=${tourId}`;
        <?php else: ?>
            $('#authModal').modal('show');
        <?php endif; ?>
    }

    // Destination dropdown functionality (styled like flights)
    $(document).ready(function() {
        const $destinationInput = $('#destination');
        const $destinationDropdown = $('.destination-dropdown');
        let destinations = <?php echo json_encode($destinations); ?>;

        function showDestinationDropdown(filtered = null) {
            const list = filtered || destinations;
            const options = list.map(dest =>
                `<div class=\"airport-option\" data-destination=\"${dest}\"><span class=\"airport-name\">${dest}</span></div>`
            ).join('');
            $destinationDropdown.html(options).show();
        }

        $destinationInput.on('focus click', function() {
            showDestinationDropdown();
        });

        $destinationDropdown.on('click', '.airport-option', function() {
            const destination = $(this).data('destination');
            $destinationInput.val(destination);
            $destinationDropdown.hide();
        });

        // Hide dropdown when clicking outside
        $(document).on('mousedown', function(e) {
            if (!$destinationInput.is(e.target) && !$destinationDropdown.is(e.target) && $destinationDropdown.has(e.target).length === 0) {
                $destinationDropdown.hide();
            }
        });

        // Filter destinations as user types
        $destinationInput.on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            const filteredDestinations = destinations.filter(dest => 
                dest.toLowerCase().includes(searchTerm)
            );
            showDestinationDropdown(filteredDestinations);
        });
    });
    </script>
</body>
</html> 