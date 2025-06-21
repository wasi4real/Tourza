<?php
require_once 'config.php';
require_once 'auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$auth = new Auth();
$isLoggedIn = $auth->isLoggedIn();
$currentUser = $auth->getCurrentUser();

$hotelId = $_GET['hotel_id'] ?? null;
if (!$hotelId) {
    echo '<div class="container py-5"><div class="alert alert-danger">No hotel selected.</div></div>';
    exit;
}

// Fetch hotel info
$hotelStmt = $pdo->prepare("SELECT * FROM hotels WHERE Hotel_ID = ?");
$hotelStmt->execute([$hotelId]);
$hotel = $hotelStmt->fetch();
if (!$hotel) {
    echo '<div class="container py-5"><div class="alert alert-danger">Hotel not found.</div></div>';
    exit;
}

// Fetch all rooms for this hotel
$roomsStmt = $pdo->prepare("SELECT * FROM rooms WHERE H_Hotel_ID = ? ORDER BY cost_night ASC");
$roomsStmt->execute([$hotelId]);
$rooms = $roomsStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooms - <?php echo htmlspecialchars($hotel['Name']); ?> | Tourza</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./styles.css" rel="stylesheet">
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
    <h1 class="mb-4">Rooms at <?php echo htmlspecialchars($hotel['Name']); ?> <span class="text-muted" style="font-size:1rem;">(<?php echo htmlspecialchars($hotel['Location']); ?>)</span></h1>
    <?php if (empty($rooms)): ?>
        <div class="alert alert-info">No rooms available for this hotel.</div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($rooms as $room): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-1"><?php echo htmlspecialchars($room['Room_type']); ?></h5>
                            <div class="mb-2 text-muted">Room #<?php echo htmlspecialchars($room['Room_number']); ?></div>
                            <ul class="mb-2" style="list-style:none;padding-left:0;">
                                <li><i class="fas fa-bed"></i> Bedrooms: <?php echo htmlspecialchars($room['Bedrooms']); ?></li>
                                <li><i class="fas fa-bath"></i> Bathrooms: <?php echo htmlspecialchars($room['Bathrooms']); ?></li>
                                <li><i class="fas fa-users"></i> Capacity: <?php echo htmlspecialchars($room['Capacity']); ?></li>
                            </ul>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <div class="hotel-price">$<?php echo number_format($room['cost_night']); ?> <span class="text-muted" style="font-size:0.9em;">/night</span></div>
                                <a href="checkout.php?type=hotel&id=<?php echo urlencode($room['Room_number']); ?>" class="btn btn-primary">Book Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Rental Cars Section (Show only if offers exist for this hotel) -->
    <?php
    $carStmt = $pdo->prepare("SELECT rc.Model, rc.Capacity, o.Rental_fee FROM offers o JOIN rental_cars rc ON o.C_Car_ID = rc.Car_ID WHERE o.H_Hotel_ID = ? ORDER BY rc.Model ASC");
    $carStmt->execute([$hotelId]);
    $cars = $carStmt->fetchAll();
    ?>
    <?php if (!empty($cars)): ?>
    <h2 class="mt-5 mb-4">Rental Cars Available</h2>
    <p class="text-info">These cars are available for rent during your stay. The cost shown is per day.</p>
        <div class="row g-4">
            <?php foreach ($cars as $car): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-primary">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-1"><?php echo htmlspecialchars($car['Model']); ?></h5>
                            <ul class="mb-2" style="list-style:none;padding-left:0;">
                                <li><i class="fas fa-users"></i> Capacity: <?php echo htmlspecialchars($car['Capacity']); ?></li>
                                <li><i class="fas fa-money-bill-wave"></i> Rental Fee: $<?php echo number_format($car['Rental_fee'], 2); ?> <span class="text-muted" style="font-size:0.9em;">/day</span></li>
                            </ul>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <a href="#" class="btn btn-primary">Book Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
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