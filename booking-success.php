<?php
require_once 'auth.php';
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$auth = new Auth();
$isLoggedIn = $auth->isLoggedIn();
$currentUser = $auth->getCurrentUser();

// Redirect if not logged in
if (!$isLoggedIn) {
    header('Location: index.php');
    exit;
}

// Get booking ID from URL
$bookingId = $_GET['booking_id'] ?? '';

if (empty($bookingId)) {
    header('Location: index.php');
    exit;
}

// Fetch booking details
$stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ? AND user_id = ?");
$stmt->execute([$bookingId, $currentUser['id']]);
$booking = $stmt->fetch();

if (!$booking) {
    header('Location: index.php');
    exit;
}

$details = json_decode($booking['details'], true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - Tourza</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="./styles.css" rel="stylesheet">
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
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h2 class="card-title mb-4">Booking Confirmed!</h2>
                        <p class="text-muted mb-4">Your booking has been successfully confirmed. You can view all your bookings in the My Bookings section.</p>
                        
                        <div class="booking-details bg-light p-4 rounded mb-4">
                            <h5 class="mb-3">Booking Details</h5>
                            <?php if ($booking['type'] === 'flight'): ?>
                                <p class="mb-2">
                                    <i class="fas fa-plane-departure me-2"></i>
                                    <?php echo htmlspecialchars($details['departure_city'] . ' (' . $details['departure_airport_code'] . ')'); ?> →
                                    <?php echo htmlspecialchars($details['arrival_city'] . ' (' . $details['arrival_airport_code'] . ')'); ?>
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-calendar me-2"></i>
                                    <?php echo date('D, d M Y', strtotime($details['departure_time'])); ?>
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-clock me-2"></i>
                                    <?php echo date('H:i', strtotime($details['departure_time'])); ?> - 
                                    <?php echo date('H:i', strtotime($details['arrival_time'])); ?>
                                </p>
                            <?php elseif ($booking['type'] === 'hotel'): ?>
                                <p class="mb-2">
                                    <i class="fas fa-hotel me-2"></i>
                                    <?php echo htmlspecialchars($details['hotel_name']); ?>
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    <?php echo htmlspecialchars($details['Location']); ?>
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-bed me-2"></i>
                                    <?php echo htmlspecialchars($details['Room_Type']); ?>
                                </p>
                            <?php elseif ($booking['type'] === 'tour'): ?>
                                <p class="mb-2">
                                    <i class="fas fa-map-marked-alt me-2"></i>
                                    <?php echo htmlspecialchars($details['Tour_Name']); ?>
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    <?php echo htmlspecialchars($details['Destination']); ?>
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-calendar me-2"></i>
                                    Duration: <?php echo htmlspecialchars($details['Duration']); ?> days
                                </p>
                            <?php endif; ?>
                            <p class="mb-0">
                                <i class="fas fa-hashtag me-2"></i>
                                Booking ID: <?php echo $booking['id']; ?>
                            </p>
                        </div>
                        
                        <div class="d-flex justify-content-center gap-3">
                            <a href="bookings.php" class="btn btn-primary">View My Bookings</a>
                            <a href="index.php" class="btn btn-outline-primary">Back to Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="auth.js"></script>
</body>
</html> 