<?php
require_once 'auth.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$auth = new Auth();
$currentUser = $auth->getCurrentUser();

// Check if user is logged in and is admin
if (!$auth->isLoggedIn() || !$auth->isAdmin()) {
    header('Location: index.php');
    exit;
}

// Get user ID from URL
$userId = $_GET['id'] ?? null;
if (!$userId) {
    header('Location: admin.php');
    exit;
}

// Get user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: admin.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User Profile - Tourza Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php"><img src="./images/logo.svg" alt="tourza brand icon"></a>
            <div class="d-flex ms-auto">
                <a href="admin.php" class="btn btn-outline-primary me-2">Back to Admin Panel</a>
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
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">User Profile</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-3 text-center">
                                <img src="<?php echo $user['profile_picture'] ?? './images/default-avatar.png'; ?>" alt="Profile" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                                <div class="mt-2">
                                    <?php if ($user['is_admin']): ?>
                                        <span class="badge bg-success">Admin</span>
                                    <?php endif; ?>
                                    <?php if ($user['banned']): ?>
                                        <span class="badge bg-danger">Banned</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <h5 class="mb-3"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h5>
                                <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                                <p class="mb-1"><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone_number'] ?? 'Not provided'); ?></p>
                                <p class="mb-1"><strong>Joined:</strong> <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Address</label>
                                <p class="form-control-static"><?php echo htmlspecialchars($user['address'] ?? 'Not provided'); ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">City</label>
                                <p class="form-control-static"><?php echo htmlspecialchars($user['city'] ?? 'Not provided'); ?></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Country</label>
                                <p class="form-control-static"><?php echo htmlspecialchars($user['country'] ?? 'Not provided'); ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Postal Code</label>
                                <p class="form-control-static"><?php echo htmlspecialchars($user['postal_code'] ?? 'Not provided'); ?></p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h5>User's Bookings</h5>
                            <?php
                            $stmt = $pdo->prepare("
                                SELECT * FROM bookings 
                                WHERE user_id = ? 
                                ORDER BY booking_date DESC
                            ");
                            $stmt->execute([$userId]);
                            $bookings = $stmt->fetchAll();
                            
                            if (count($bookings) > 0):
                            ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Details</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($bookings as $booking): ?>
                                        <tr>
                                            <td><?php echo ucfirst($booking['type']); ?></td>
                                            <td>
                                                <?php
                                                $details = json_decode($booking['details'], true);
                                                if ($booking['type'] === 'flight') {
                                                    echo htmlspecialchars($details['airline'] ?? '') . ' ';
                                                    echo htmlspecialchars($details['departure_airport_code'] ?? '') . ' → ' . htmlspecialchars($details['arrival_airport_code'] ?? '');
                                                } elseif ($booking['type'] === 'hotel') {
                                                    echo htmlspecialchars($details['Name'] ?? '');
                                                } elseif ($booking['type'] === 'tour') {
                                                    echo htmlspecialchars($details['Tour_Name'] ?? '');
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo date('Y-m-d H:i', strtotime($booking['booking_date'])); ?></td>
                                            <td>
                                                <span class="badge 
                                                    <?php
                                                        if ($booking['status'] === 'confirmed') echo 'bg-success';
                                                        elseif ($booking['status'] === 'cancelled') echo 'bg-danger';
                                                        else echo 'bg-warning text-dark';
                                                    ?>">
                                                    <?php echo ucfirst($booking['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <p class="text-muted">No bookings found.</p>
                            <?php endif; ?>
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