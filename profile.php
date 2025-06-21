<?php
require_once 'auth.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$auth = new Auth();
$user = $auth->getCurrentUser();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Tourza</title>
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
                    <img src="<?php echo $user['profile_picture'] ?? './images/default-avatar.png'; ?>" alt="Profile" class="rounded-circle me-2" style="width: 24px; height: 24px;">
                    <?php echo htmlspecialchars($user['first_name']); ?>
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
                <div class="card-body">
                    <h4 class="card-title mb-4">Profile Information</h4>
                    <form id="profileForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control profile" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control profile" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" readonly>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control profile" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control profile" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control profile" id="address" name="address" rows="2" readonly><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control profile" id="city" name="city" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="country" class="form-label">Country</label>
                                <input type="text" class="form-control profile" id="country" name="country" value="<?php echo htmlspecialchars($user['country'] ?? ''); ?>" readonly>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="postal_code" class="form-label">Postal Code</label>
                            <input type="text" class="form-control profile" id="postal_code" name="postal_code" value="<?php echo htmlspecialchars($user['postal_code'] ?? ''); ?>" readonly>
                        </div>
                        <button type="button" class="btn btn-primary" id="editBtn">Edit</button>
                        <button type="submit" class="btn btn-success d-none" id="saveBtn">Save</button>
                        <button type="button" class="btn btn-secondary d-none" id="cancelBtn">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    // Edit button enables fields
    $('#editBtn').on('click', function() {
        $('#profileForm input, #profileForm textarea').prop('readonly', false);
        $('#editBtn').addClass('d-none');
        $('#saveBtn, #cancelBtn').removeClass('d-none');
    });
    // Cancel button disables fields and resets values
    $('#cancelBtn').on('click', function() {
        $('#profileForm')[0].reset();
        $('#profileForm input, #profileForm textarea').prop('readonly', true);
        $('#editBtn').removeClass('d-none');
        $('#saveBtn, #cancelBtn').addClass('d-none');
    });
    // Save button submits form
    $('#profileForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize() + '&action=updateProfile';
        $.post('auth.php', formData, function(response) {
            if (response.success) {
                alert('Profile updated successfully!');
                location.reload();
            } else {
                alert(response.message || 'Failed to update profile');
            }
        }, 'json');
    });
});
function logout() {
    $.post('auth.php', JSON.stringify({action: 'logout'}), function() {
        window.location.reload();
    });
}
</script>
</body>
</html> 