<?php
require_once 'auth.php';
require_once 'config.php';

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

// Handle admin status changes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'] ?? null;
    $action = $_POST['action'] ?? '';
    
    if ($userId && $action) {
        if ($action === 'make_admin') {
            $auth->makeAdmin($userId);
        } elseif ($action === 'remove_admin') {
            $auth->removeAdmin($userId);
        } elseif ($action === 'ban_user') {
            $auth->banUser($userId);
        } elseif ($action === 'unban_user') {
            $auth->unbanUser($userId);
        }
    }
    
    // Redirect to prevent form resubmission
    header('Location: admin.php');
    exit;
}

// Get all users
$stmt = $pdo->query("SELECT id, first_name, last_name, email, is_admin, banned, created_at FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();

// Get all bookings with user info
$stmt = $pdo->query("
    SELECT 
        b.id, b.type, b.item_id, b.details, b.booking_date, b.status,
        u.first_name, u.last_name, u.email
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    ORDER BY b.booking_date DESC
");
$bookings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Tourza</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php"><img src="./images/logo.svg" alt="tourza brand icon"></a>
            <div class="d-flex ms-auto">
                <a href="index.php" class="btn btn-outline-primary me-2">Back to Home</a>
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
        <h1 class="mb-4">Admin Panel</h1>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Manage Users</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Joined Date</th>
                                <th>Admin Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <?php if ($user['is_admin']): ?>
                                        <span class="badge bg-success">Admin</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">User</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($user['id'] !== $currentUser['id']): ?>
                                        <?php if ($user['is_admin']): ?>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <input type="hidden" name="action" value="remove_admin">
                                                <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Are you sure you want to remove admin status from this user?')">
                                                    Remove Admin
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <input type="hidden" name="action" value="make_admin">
                                                <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Are you sure you want to make this user an admin?')">
                                                    Make Admin
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <?php if ($user['banned']): ?>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <input type="hidden" name="action" value="unban_user">
                                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to unban this user?')">
                                                    Unban User
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <input type="hidden" name="action" value="ban_user">
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to ban this user?')">
                                                    Ban User
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <a href="view-profile.php?id=<?php echo $user['id']; ?>" class="btn btn-info btn-sm">View Profile</a>
                                    <?php else: ?>
                                        <span class="text-muted">Current User</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- All Bookings Table -->
        <div class="card mt-5">
            <div class="card-header">
                <h5 class="mb-0">All Bookings</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Type</th>
                                <th>Details</th>
                                <th>Booking Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (
                                $bookings as $booking): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($booking['email']); ?></td>
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
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="auth.js"></script>
</body>
</html> 