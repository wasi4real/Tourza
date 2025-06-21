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

// Get booking type and ID from URL
$type = $_GET['type'] ?? '';
$id = $_GET['id'] ?? '';

if (empty($type) || empty($id)) {
    header('Location: index.php');
    exit;
}

// Fetch booking details based on type
$bookingDetails = null;
$price = 0;
$title = '';

switch ($type) {
    case 'flight':
        $stmt = $pdo->prepare("SELECT * FROM flights WHERE id = ?");
        $stmt->execute([$id]);
        $flight = $stmt->fetch();
        if ($flight) {
            $bookingDetails = $flight;
            $price = $flight['price'];
            $title = $flight['airline'] . ' - ' . $flight['departure_airport_code'] . ' to ' . $flight['arrival_airport_code'];
        }
        break;
        
    case 'hotel':
        $stmt = $pdo->prepare("SELECT r.*, h.Name as hotel_name, h.Location FROM rooms r 
                              JOIN hotels h ON r.H_Hotel_ID = h.Hotel_ID 
                              WHERE r.Room_number = ?");
        $stmt->execute([$id]);
        $room = $stmt->fetch();
        if ($room) {
            $bookingDetails = $room;
            $price = $room['cost_night'];
            $title = $room['hotel_name'] . ' - ' . $room['Room_type'];
        }
        break;
        
    case 'tour':
        $stmt = $pdo->prepare("SELECT * FROM tours WHERE Tour_ID = ?");
        $stmt->execute([$id]);
        $tour = $stmt->fetch();
        if ($tour) {
            $bookingDetails = $tour;
            $price = $tour['Price_Per_Person'];
            $title = $tour['Tour_Name'];
        }
        break;
}

if (!$bookingDetails) {
    header('Location: index.php');
    exit;
}

// Handle AJAX/JSON booking
$contentType = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
file_put_contents('debug_checkout.txt', 'CONTENT_TYPE: ' . $contentType . PHP_EOL, FILE_APPEND);
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    strpos($contentType, 'application/json') !== false
) {
    file_put_contents('debug_checkout.txt', 'IN JSON HANDLER' . PHP_EOL, FILE_APPEND);
    $rawInput = file_get_contents('php://input');
    file_put_contents('debug_checkout.txt', 'RAW INPUT: ' . $rawInput . PHP_EOL, FILE_APPEND);
    $input = json_decode($rawInput, true);
    file_put_contents('debug_checkout.txt', print_r($input, true), FILE_APPEND);
    if (isset($input['action']) && $input['action'] === 'confirm_booking') {
        $userId = $input['user_id'] ?? null;
        $itemId = $input['flight_id'] ?? $input['hotel_id'] ?? $input['tour_id'] ?? null;
        $type = $input['type'] ?? 'flight'; // Default to flight if not provided
        $totalPrice = $input['total_price'] ?? null;
        // You may want to validate these values here
        if ($userId && $itemId) {
            try {
                $stmt = $pdo->prepare("INSERT INTO bookings (user_id, type, item_id, details, booking_date, status) VALUES (?, ?, ?, ?, NOW(), 'confirmed')");
                // You may want to fetch and encode details as in the regular flow
                $details = json_encode(['total_price' => $totalPrice]);
                $stmt->execute([$userId, $type, $itemId, $details]);
                echo json_encode(['success' => true]);
                exit;
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Booking failed: ' . $e->getMessage()]);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Missing user or item ID']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Create booking record
        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, type, item_id, details, booking_date, status) 
                              VALUES (?, ?, ?, ?, NOW(), 'confirmed')");
        
        $details = json_encode($bookingDetails);
        $stmt->execute([$currentUser['id'], $type, $id, $details]);
        
        // Redirect to success page
        header('Location: booking-success.php?booking_id=' . $pdo->lastInsertId());
        exit;
    } catch (PDOException $e) {
        $error = "An error occurred while processing your booking. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Tourza</title>
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
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Booking Details</h4>
                        <div class="booking-details">
                            <h5><?php echo htmlspecialchars($title); ?></h5>
                            <?php if ($type === 'flight'): ?>
                                <p class="mb-2">
                                    <i class="fas fa-plane-departure me-2"></i>
                                    <?php echo htmlspecialchars($bookingDetails['departure_city'] . ' (' . $bookingDetails['departure_airport_code'] . ')'); ?> →
                                    <?php echo htmlspecialchars($bookingDetails['arrival_city'] . ' (' . $bookingDetails['arrival_airport_code'] . ')'); ?>
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-calendar me-2"></i>
                                    <?php echo date('D, d M Y', strtotime($bookingDetails['departure_time'])); ?>
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-clock me-2"></i>
                                    <?php echo date('H:i', strtotime($bookingDetails['departure_time'])); ?> - 
                                    <?php echo date('H:i', strtotime($bookingDetails['arrival_time'])); ?>
                                </p>
                            <?php elseif ($type === 'hotel'): ?>
                                <p class="mb-2">
                                    <i class="fas fa-hotel me-2"></i>
                                    <?php echo htmlspecialchars($bookingDetails['hotel_name']); ?>
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    <?php echo htmlspecialchars($bookingDetails['Location']); ?>
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-bed me-2"></i>
                                    <?php echo htmlspecialchars($bookingDetails['Room_type']); ?>
                                </p>
                            <?php elseif ($type === 'tour'): ?>
                                <p class="mb-2">
                                    <i class="fas fa-map-marked-alt me-2"></i>
                                    <?php echo htmlspecialchars($bookingDetails['Destination']); ?>
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-calendar me-2"></i>
                                    Duration: <?php echo htmlspecialchars($bookingDetails['Duration_Days']); ?> days
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Payment Information</h4>
                        <form id="payment-form" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Card Number</label>
                                <input type="text" class="form-control" placeholder="1234 5678 9012 3456" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Expiry Date</label>
                                    <input type="text" class="form-control" placeholder="MM/YY" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">CVV</label>
                                    <input type="text" class="form-control" placeholder="123" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Name on Card</label>
                                <input type="text" class="form-control" placeholder="John Doe" required>
                            </div>
                            <div class="d-grid mt-3">
                                <button id="confirmBtn" class="btn btn-success">Confirm Booking (AJAX)</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Price Summary</h4>
                        <div class="price-breakdown">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Base Price</span>
                                <span>BDT <?php echo number_format($price, 2); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Taxes & Fees</span>
                                <span>BDT <?php echo number_format($price * 0.15, 2); ?></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total</span>
                                <span>BDT <?php echo number_format($price * 1.15, 2); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="auth.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const confirmBtn = document.getElementById('confirmBtn');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', async function(e) {
                e.preventDefault();
                // Gather booking data
                const userId = <?php echo json_encode($currentUser['id']); ?>;
                const type = <?php echo json_encode($type); ?>;
                const itemId = <?php echo json_encode($id); ?>;
                const totalPrice = <?php echo json_encode($price * 1.15); ?>;
                let payload = {
                    action: 'confirm_booking',
                    user_id: userId,
                    total_price: totalPrice,
                    type: type
                };
                if (type === 'flight') payload.flight_id = itemId;
                else if (type === 'hotel') payload.hotel_id = itemId;
                else if (type === 'tour') payload.tour_id = itemId;
                try {
                    const response = await fetch('checkout.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });
                    const result = await response.json();
                    if (result.success) {
                        alert('Booking successful!');
                        window.location.href = 'bookings.php';
                    } else {
                        alert(result.message || 'Booking failed');
                    }
                } catch (err) {
                    console.error('Booking error:', err);
                    alert('An error occurred while booking.');
                }
            });
            // Optionally hide the classic form submit button if JS is enabled
            const paymentForm = document.getElementById('payment-form');
            if (paymentForm) {
                const submitBtn = paymentForm.querySelector('button[type="submit"]');
                if (submitBtn) submitBtn.style.display = 'none';
            }
        }
    });
    </script>
</body>
</html> 