<?php
require_once 'auth.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$auth = new Auth();
$isLoggedIn = $auth->isLoggedIn();
$currentUser = $auth->getCurrentUser();

// Get flights from database
require_once 'config.php';

// Get search parameters
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$journey_date = $_GET['journey_date'] ?? '';
$return_date = $_GET['return_date'] ?? '';
$travelers = $_GET['travelers'] ?? 1;
$children = $_GET['children'] ?? 0;
$class = $_GET['class'] ?? 'economy';

// Debug output for date parsing
error_log('JOURNEY_DATE (raw): ' . $journey_date);
error_log('JOURNEY_DATE (parsed): ' . date('Y-m-d', strtotime($journey_date)));

// Get sorting and filtering parameters
$sort = $_GET['sort'] ?? 'cheapest';
$stops = $_GET['stops'] ?? '';
$price_range = $_GET['price_range'] ?? '';
$schedule = $_GET['schedule'] ?? '';
$airlines = $_GET['airlines'] ?? [];

// Build the query
$query = "SELECT * FROM flights WHERE 1=1";
$params = [];

if ($from) {
    $query .= " AND departure_airport_code = ?";
    $params[] = $from;
}

if ($to) {
    $query .= " AND arrival_airport_code = ?";
    $params[] = $to;
}

if ($journey_date) {
    $query .= " AND DATE(departure_time) = ?";
    $params[] = date('Y-m-d', strtotime($journey_date));
}

if ($return_date) {
    $query .= " AND DATE(arrival_time) = ?";
    $params[] = date('Y-m-d', strtotime($return_date));
}

if ($price_range && is_numeric($price_range)) {
    $query .= " AND price <= ?";
    $params[] = $price_range;
}

// Add class filter
if ($class) {
    $query .= " AND class = ?";
    $params[] = $class;
}

if ($schedule) {
    $scheduleParts = explode('-', $schedule);
    if (count($scheduleParts) === 2) {
        $start = str_pad($scheduleParts[0], 2, '0', STR_PAD_LEFT);
        $end = str_pad($scheduleParts[1], 2, '0', STR_PAD_LEFT);
        if ($end === '00') $end = '24';
        $query .= " AND HOUR(departure_time) >= ? AND HOUR(departure_time) < ?";
        $params[] = (int)$start;
        $params[] = (int)$end;
    }
}

if (isset($_GET['airlines']) && $_GET['airlines']) {
    $airlinesArr = explode(',', $_GET['airlines']);
    $in = str_repeat('?,', count($airlinesArr) - 1) . '?';
    $query .= " AND airline IN ($in)";
    $params = array_merge($params, $airlinesArr);
}

// Now append the ORDER BY clause
switch ($sort) {
    case 'cheapest':
        $query .= " ORDER BY price ASC";
        break;
    case 'fastest':
        $query .= " ORDER BY duration ASC";
        break;
    case 'earliest':
        $query .= " ORDER BY departure_time ASC";
        break;
    case 'latest':
        $query .= " ORDER BY departure_time DESC";
        break;
    default:
        $query .= " ORDER BY price ASC";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$flights = $stmt->fetchAll();

// Get unique airlines for filter
$airlines_query = "SELECT DISTINCT airline FROM flights ORDER BY airline";
$airlines_stmt = $pdo->query($airlines_query);
$available_airlines = $airlines_stmt->fetchAll(PDO::FETCH_COLUMN);

// Get price range for filter
$price_query = "SELECT MIN(price) as min_price, MAX(price) as max_price FROM flights";
$price_stmt = $pdo->query($price_query);
$price_range = $price_stmt->fetch();

// Handle airport data request
if (isset($_GET['action']) && $_GET['action'] === 'get_airports') {
    header('Content-Type: application/json');
    $sql = "SELECT departure_airport_code as code, departure_city as city, departure_country as country FROM flights
            UNION
            SELECT arrival_airport_code as code, arrival_city as city, arrival_country as country FROM flights
            ORDER BY city, country, code";
    $stmt = $pdo->query($sql);
    $airports = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Remove duplicates by code
    $unique = [];
    foreach ($airports as $airport) {
        $unique[$airport['code']] = $airport;
    }
    $airports = array_values($unique);
    echo json_encode($airports);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flights - Tourza</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php"><img src="./images/logo.svg" alt="tourza brand icon"></a>
            <ul class="navbar-nav flex-row ms-3 justify-content-center" style="gap: 1.5rem; font-weight: 500; font-size: 1.1rem;">
                <li class="nav-item"><a class="nav-link px-2" href="index.php#flight-search">Flights</a></li>
                <li class="nav-item"><a class="nav-link px-2" href="index.php#hotel-search">Hotels</a></li>
                <li class="nav-item"><a class="nav-link px-2" href="index.php#tour-search">Tours</a></li>
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

    <div class="container my-4">
        <!-- Compact Interactive Search Header -->
        <div class="search-box flight-search active p-3" id="flight-search">
            <form class="row g-2 align-items-end flex-nowrap" method="get" action="flights.php" id="header-search-form">
                <div class="col-md-2">
                    <label class="form-label mb-1">From</label>
                    <div class="location-input">
                        <input type="text" class="form-control" id="from-airport" name="from" value="<?php echo htmlspecialchars($from); ?>" placeholder="Select Location">
                        <div class="airport-dropdown from-dropdown"></div>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1">To</label>
                    <div class="location-input">
                        <input type="text" class="form-control" id="to-airport" name="to" value="<?php echo htmlspecialchars($to); ?>" placeholder="Select Location">
                        <div class="airport-dropdown to-dropdown"></div>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1">Journey Date</label>
                    <input type="text" class="form-control" id="journey-date" name="journey_date" value="<?php echo htmlspecialchars($journey_date); ?>" readonly>
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1">Return Date</label>
                    <input type="text" class="form-control" id="return-date" name="return_date" value="<?php echo htmlspecialchars($return_date); ?>" readonly>
                </div>
                <div class="col-md-2 position-relative">
                    <label class="form-label mb-1">Traveler, Class</label>
                    <input type="hidden" name="travelers" id="header-adult-count" value="<?php echo htmlspecialchars($travelers); ?>">
                    <input type="hidden" name="children" id="header-child-count" value="<?php echo htmlspecialchars($children); ?>">
                    <input type="hidden" name="class" id="header-class" value="<?php echo htmlspecialchars($class); ?>">
                    <div class="form-control" id="traveler-class-container" style="cursor:pointer;">
                        <div class="traveler-class-value"><?php echo $travelers + $children; ?> Traveler<?php echo ($travelers + $children > 1) ? 's' : ''; ?>, <?php echo ucfirst($class); ?></div>
                    </div>
                    <div class="traveler-class-dropdown" id="traveler-class-dropdown" style="position: absolute; left: 0; top: 100%; z-index: 1000;">
                        <div class="passenger-type">
                            <div class="passenger-type-title">Adults</div>
                            <div class="passenger-type-description">12 years and above</div>
                            <div class="passenger-count">
                                <button type="button" class="count-btn" id="adult-minus">-</button>
                                <span class="count-value" id="adult-count"><?php echo htmlspecialchars($travelers); ?></span>
                                <button type="button" class="count-btn" id="adult-plus">+</button>
                            </div>
                        </div>
                        <div class="passenger-type">
                            <div class="passenger-type-title">Children</div>
                            <div class="passenger-type-description">11 years and under</div>
                            <div class="passenger-count">
                                <button type="button" class="count-btn" id="child-minus">-</button>
                                <span class="count-value" id="child-count"><?php echo htmlspecialchars($children); ?></span>
                                <button type="button" class="count-btn" id="child-plus">+</button>
                            </div>
                        </div>
                        <div class="class-selection mt-3">
                            <div class="passenger-type-title">Class</div>
                            <div class="btn-group w-100" role="group">
                                <button type="button" class="btn btn-outline-primary class-option <?php echo $class === 'economy' ? 'active btn-primary' : 'btn-outline-primary'; ?>" data-class="economy">Economy</button>
                                <button type="button" class="btn btn-outline-primary class-option <?php echo $class === 'business' ? 'active btn-primary' : 'btn-outline-primary'; ?>" data-class="business">Business</button>
                            </div>
                        </div>
                        <button class="done-btn btn btn-primary mt-3" id="done-btn">Done</button>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-center">
                    <button type="submit" class="btn btn-primary w-100 search-btn">Modify</button>
                </div>
            </form>
        </div>

        <!-- Sort Bar -->
        <div class="bg-white p-3 rounded shadow-sm mb-3 d-flex align-items-center justify-content-between mt-4">
            <div class="fw-bold">Sort by:</div>
            <div class="d-flex align-items-center gap-3" id="sort-bar">
                <button class="btn btn-outline-primary btn-sm <?php echo $sort === 'cheapest' ? 'active' : ''; ?>" data-sort="cheapest">Cheapest</button>
                <button class="btn btn-outline-primary btn-sm <?php echo $sort === 'fastest' ? 'active' : ''; ?>" data-sort="fastest">Fastest</button>
                <button class="btn btn-outline-primary btn-sm <?php echo $sort === 'earliest' ? 'active' : ''; ?>" data-sort="earliest">Earliest</button>
                <button class="btn btn-outline-primary btn-sm <?php echo $sort === 'latest' ? 'active' : ''; ?>" data-sort="latest">Latest</button>
            </div>
        </div>

        <div class="row">
            <!-- Filter Sidebar -->
            <div class="col-md-3 mb-4">
                <div class="bg-white p-3 rounded shadow-sm">
                    <h6 class="fw-bold mb-3">Price Range</h6>
                    <div class="d-flex align-items-center mb-3">
                        <span>BDT <?php echo number_format($price_range['min_price'], 2); ?></span>
                        <input type="range" class="form-range mx-2" min="<?php echo $price_range['min_price']; ?>" max="<?php echo $price_range['max_price']; ?>" value="<?php echo isset($_GET['price_range']) ? htmlspecialchars($_GET['price_range']) : $price_range['max_price']; ?>" id="price-range">
                        <span>BDT <?php echo number_format($price_range['max_price'], 2); ?></span>
                    </div>
                    <h6 class="fw-bold mb-3">Schedule</h6>
                    <div class="mb-3" id="schedule-filter">
                        <button class="btn btn-outline-secondary btn-sm me-2 mb-2 <?php echo $schedule === '00-06' ? 'active' : ''; ?>" data-schedule="00-06">00-06</button>
                        <button class="btn btn-outline-secondary btn-sm me-2 mb-2 <?php echo $schedule === '06-12' ? 'active' : ''; ?>" data-schedule="06-12">06-12</button>
                        <button class="btn btn-outline-secondary btn-sm me-2 mb-2 <?php echo $schedule === '12-18' ? 'active' : ''; ?>" data-schedule="12-18">12-18</button>
                        <button class="btn btn-outline-secondary btn-sm mb-2 <?php echo $schedule === '18-00' ? 'active' : ''; ?>" data-schedule="18-00">18-00</button>
                    </div>
                    <h6 class="fw-bold mb-3">Airlines</h6>
                    <?php foreach ($available_airlines as $airline): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="airline-<?php echo strtolower(str_replace(' ', '-', $airline)); ?>" 
                               value="<?php echo htmlspecialchars($airline); ?>" 
                               <?php echo (isset($_GET['airlines']) && in_array($airline, explode(',', $_GET['airlines']))) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="airline-<?php echo strtolower(str_replace(' ', '-', $airline)); ?>">
                            <?php echo htmlspecialchars($airline); ?>
                        </label>
                    </div>
                    <?php endforeach; ?>
                    <div class="d-flex justify-content-between mt-3">
                        <button class="btn btn-outline-secondary btn-sm" id="close-filters">Close</button>
                        <button class="btn btn-outline-secondary btn-sm" id="reset-filters">Reset All Filters</button>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9">
                <?php if (empty($flights)): ?>
                    <div class="no-flights-found bg-white p-4 rounded shadow-sm text-center">
                        <h4 class="mb-3">No Flights Found</h4>
                        <p class="text-muted mb-3">We couldn't find any flights matching your search criteria.</p>
                        <div class="search-details mb-3">
                            <p class="mb-1"><strong>From:</strong> <?php echo htmlspecialchars($from); ?></p>
                            <p class="mb-1"><strong>To:</strong> <?php echo htmlspecialchars($to); ?></p>
                            <p class="mb-1"><strong>Date:</strong> <?php echo htmlspecialchars($journey_date); ?></p>
                            <?php if ($return_date): ?>
                                <p class="mb-1"><strong>Return Date:</strong> <?php echo htmlspecialchars($return_date); ?></p>
                            <?php endif; ?>
                        </div>
                        <button class="btn btn-primary" onclick="window.location.href='index.php'">Modify Search</button>
                    </div>
                <?php else: ?>
                    <!-- Flight Cards -->
                    <?php foreach ($flights as $flight): 
                        // Calculate duration in hours and minutes
                        $hours = floor($flight['duration'] / 60);
                        $minutes = $flight['duration'] % 60;
                        $duration = $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";
                        
                        // Calculate price per person
                        $adults = (int)$travelers;
                        $kids = (int)$children;
                        $pricePerAdult = $flight['price'];
                        $pricePerChild = $flight['price'] * 0.5;
                        $totalPrice = $adults * $pricePerAdult + $kids * $pricePerChild;
                        
                        // Format times
                        $departureTime = date('H:i', strtotime($flight['departure_time']));
                        $arrivalTime = date('H:i', strtotime($flight['arrival_time']));
                        $departureDate = date('D, d M', strtotime($flight['departure_time']));
                        $arrivalDate = date('D, d M', strtotime($flight['arrival_time']));
                        
                        // Determine if it's a next day arrival
                        $isNextDay = strtotime($flight['arrival_time']) > strtotime($flight['departure_time']) + 86400;
                    ?>
                    <div class="flight-card bg-white p-3 rounded shadow-sm mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <div class="airline-logo me-3">
                                    <img src="./images/<?php echo strtolower(str_replace(' ', '-', $flight['airline'])); ?>.png" 
                                         alt="<?php echo htmlspecialchars($flight['airline']); ?>" 
                                         class="airline-logo-img"
                                         >
                                </div>
                                <div>
                                    <div class="fw-bold"><?php echo htmlspecialchars($flight['airline']); ?></div>
                                    <div class="text-muted small">
                                        <?php echo htmlspecialchars($flight['departure_city'] . ', ' . $flight['departure_country'] . ' (' . $flight['departure_airport_code'] . ')'); ?>
                                        →
                                        <?php echo htmlspecialchars($flight['arrival_city'] . ', ' . $flight['arrival_country'] . ' (' . $flight['arrival_airport_code'] . ')'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="flight-status">
                                <span class="badge bg-success">Available</span>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="text-center col-md-3">
                                <div class="fw-bold fs-4"><?php echo $departureTime; ?></div>
                                <div class="text-muted small"><?php echo $departureDate; ?></div>
                                <div class="text-muted small"><?php echo htmlspecialchars($flight['departure_city'] . ', ' . $flight['departure_country'] . ' (' . $flight['departure_airport_code'] . ')'); ?></div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="flight-duration text-center">
                                    <div class="duration-line">
                                        <div class="duration-dot start"></div>
                                        <div class="duration-line-inner"></div>
                                        <div class="duration-dot end"></div>
                                    </div>
                                    <div class="duration-text">
                                        <div class="fw-bold"><?php echo $duration; ?></div>
                                        <div class="text-muted small"><?php echo $isNextDay ? 'Next day arrival' : 'Direct flight'; ?></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center col-md-3">
                                <div class="fw-bold fs-4"><?php echo $arrivalTime; ?></div>
                                <div class="text-muted small"><?php echo $arrivalDate; ?></div>
                                <div class="text-muted small"><?php echo htmlspecialchars($flight['arrival_city'] . ', ' . $flight['arrival_country'] . ' (' . $flight['arrival_airport_code'] . ')'); ?></div>
                            </div>
                            
                            <div class="text-end col-md-2">
                                <div class="price-info">
                                    <div class="text-primary fw-bold">BDT <?php echo number_format($totalPrice, 2); ?></div>
                                    <div class="text-muted small">BDT <?php echo number_format($pricePerAdult, 2); ?> per adult, <?php echo number_format($pricePerChild, 2); ?> per child</div>
                                </div>
                                <div class="mt-2">
                                    <span class="badge bg-info">Class: <?php echo ucfirst($flight['class']); ?></span>
                                    <a class="btn btn-primary btn-sm" href="checkout.php?type=flight&id=<?php echo $flight['id']; ?>">Select</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Flight Details Modal -->
    <div class="modal fade" id="flightDetailsModal" tabindex="-1" aria-labelledby="flightDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="flightDetailsModalLabel">Flight Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="flight-details-content">
                        <!-- Content will be loaded dynamically -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="position-fixed top-0 start-0 w-100 h-100 d-none" style="background: rgba(255,255,255,0.8); z-index: 9999;">
        <div class="position-absolute top-50 start-50 translate-middle text-center">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="text-primary">Searching for flights...</div>
        </div>
    </div>

    <!-- Include auth modal -->
    <?php include 'auth-modal.php'; ?>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="main.js"></script>
    <script src="auth.js"></script>
    <script>
        function bookFlight(flightId) {
            <?php if ($isLoggedIn): ?>
                window.location.href = 'booking.php?flight_id=' + flightId;
            <?php else: ?>
                $('#authModal').modal('show');
            <?php endif; ?>
        }

        function showFlightDetails(flightId) {
            // Show loading overlay
            $('#loading-overlay').removeClass('d-none');
            
            // Fetch flight details
            fetch('flight-details.php?id=' + flightId)
                .then(response => response.json())
                .then(data => {
                    const modal = $('#flightDetailsModal');
                    const content = modal.find('.flight-details-content');
                    
                    // Build the content HTML
                    let html = `
                        <div class="flight-info mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <img src="images/airlines/${data.airline.toLowerCase().replace(' ', '-')}.png" 
                                     alt="${data.airline}" 
                                     class="airline-logo-img me-3"
                                     onerror="this.src='images/airlines/default.png'">
                                <div>
                                    <h5 class="mb-1">${data.airline}</h5>
                                    <div class="text-muted">Flight ${data.flight_number}</div>
                                </div>
                            </div>
                            
                            <div class="flight-route d-flex align-items-center justify-content-between mb-4">
                                <div class="text-center">
                                    <div class="fw-bold fs-4">${data.departure_time}</div>
                                    <div class="text-muted">${data.departure_city}, ${data.departure_country} (${data.departure_airport_code})</div>
                                    <div class="text-muted small">${data.departure_date}</div>
                                </div>
                                
                                <div class="flight-duration text-center flex-grow-1 mx-4">
                                    <div class="duration-line">
                                        <div class="duration-dot start"></div>
                                        <div class="duration-line-inner"></div>
                                        <div class="duration-dot end"></div>
                                    </div>
                                    <div class="duration-text">
                                        <div class="fw-bold">${data.duration}</div>
                                        <div class="text-muted small">${data.is_next_day ? 'Next day arrival' : 'Direct flight'}</div>
                                    </div>
                                </div>
                                
                                <div class="text-center">
                                    <div class="fw-bold fs-4">${data.arrival_time}</div>
                                    <div class="text-muted">${data.arrival_city}, ${data.arrival_country} (${data.arrival_airport_code})</div>
                                    <div class="text-muted small">${data.arrival_date}</div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-3">Fare Details</h6>
                                    <div class="fare-breakdown">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Base Fare</span>
                                            <span>BDT ${data.base_fare}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Taxes & Fees</span>
                                            <span>BDT ${data.taxes}</span>
                                        </div>
                                        <div class="d-flex justify-content-between fw-bold">
                                            <span>Total</span>
                                            <span>BDT ${data.total_price}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-3">Baggage Allowance</h6>
                                    <div class="baggage-info">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Cabin Baggage</span>
                                            <span>${data.cabin_baggage}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Check-in Baggage</span>
                                            <span>${data.checkin_baggage}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="amenities mt-4">
                                <h6 class="fw-bold mb-3">Flight Amenities</h6>
                                <div class="d-flex flex-wrap gap-3">
                                    ${data.amenities.map(amenity => `
                                        <div class="amenity-item">
                                            <i class="${amenity.icon}"></i>
                                            <span>${amenity.name}</span>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        </div>
                    `;
                    
                    content.html(html);
                    modal.modal('show');
                })
                .catch(error => {
                    console.error('Error fetching flight details:', error);
                    alert('Error loading flight details. Please try again.');
                })
                .finally(() => {
                    $('#loading-overlay').addClass('d-none');
                });
        }

        // Show loading overlay when searching
        $('.search-btn').on('click', function() {
            $('#loading-overlay').removeClass('d-none');
        });

        // Handle search parameters
        $(document).ready(function() {
            // Load airports for the header search
            async function loadAirports() {
                try {
                    const response = await fetch('flights.php?action=get_airports');
                    const airports = await response.json();
                    
                    const airportOptions = airports.map(code => 
                        `<div class=\"airport-option\" data-airport=\"${code}\">\n` +
                        `    <div class=\"airport-code\">${code}</div>\n` +
                        `</div>`
                    ).join('');
                    
                    $('.from-dropdown, .to-dropdown').html(airportOptions);
                } catch (error) {
                    console.error('Error loading airports:', error);
                }
            }

            loadAirports();

            // Handle airport selection
            $(document).on('click', '.airport-option', function() {
                const code = $(this).data('airport');
                const isFrom = $(this).closest('.from-dropdown').length > 0;
                const input = isFrom ? $('#from-airport') : $('#to-airport');
                input.val(code);
                $(this).closest('.airport-dropdown').hide();
            });

            // Show/hide dropdowns on input click
            $('#from-airport, #to-airport').on('click', function() {
                const isFrom = $(this).attr('id') === 'from-airport';
                const dropdown = isFrom ? $('.from-dropdown') : $('.to-dropdown');
                
                $('.airport-dropdown').hide();
                dropdown.show();
            });

            // Hide dropdowns when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.location-input').length) {
                    $('.airport-dropdown').hide();
                }
            });

            // Handle search button click
            $('.search-btn').on('click', function() {
                const fromInput = $('#from-airport');
                const toInput = $('#to-airport');
                
                // Extract code from display value (e.g., "CGP - Chittagong" -> "CGP")
                const fromCode = fromInput.val().split(' - ')[0];
                const toCode = toInput.val().split(' - ')[0];
                
                const params = new URLSearchParams({
                    from: fromCode,
                    to: toCode,
                    journey_date: $('#journey-date').val(),
                    return_date: $('#return-date').val(),
                    travelers: $('#adult-count').text(),
                    children: $('#child-count').text(),
                    class: $('.class-option.active').data('class')
                });

                window.location.href = 'flights.php?' + params.toString();
            });

            // Handle sort buttons
            $('#sort-bar button').on('click', function() {
                const sort = $(this).data('sort');
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('sort', sort);
                window.location.href = currentUrl.toString();
            });

            // Handle price range
            $('#price-range').on('change', function() {
                const price = $(this).val();
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('price_range', price);
                window.location.href = currentUrl.toString();
            });

            // Handle schedule filter
            $('#schedule-filter button').on('click', function() {
                const schedule = $(this).data('schedule');
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('schedule', schedule);
                window.location.href = currentUrl.toString();
            });

            // Handle airline filters
            $('.form-check-input').on('change', function() {
                const selectedAirlines = [];
                $('.form-check-input:checked').each(function() {
                    selectedAirlines.push($(this).val());
                });
                
                const currentUrl = new URL(window.location.href);
                if (selectedAirlines.length > 0) {
                    currentUrl.searchParams.set('airlines', selectedAirlines.join(','));
                } else {
                    currentUrl.searchParams.delete('airlines');
                }
                window.location.href = currentUrl.toString();
            });

            // Handle reset filters
            $('#reset-filters').on('click', function() {
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.delete('sort');
                currentUrl.searchParams.delete('price_range');
                currentUrl.searchParams.delete('schedule');
                currentUrl.searchParams.delete('airlines');
                window.location.href = currentUrl.toString();
            });

            const journeyDate = flatpickr("#journey-date", {
                minDate: "today",
                dateFormat: "Y-m-d",
                defaultDate: "2025-08-01",
                onChange: function(selectedDates, dateStr, instance) {
                    // ... existing code ...
                }
            });
        });
    </script>
</body>
</html> 