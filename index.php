<?php
require_once 'auth.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$auth = new Auth();
$isLoggedIn = $auth->isLoggedIn();
$currentUser = $auth->getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tourza</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
    /* ... existing styles ... */
    /* Tour destination dropdown styling to match flights */
    .tour-destination-dropdown.airport-dropdown {
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
            <div class="d-flex ms-auto">
                <?php if ($isLoggedIn): ?>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <button class="btn btn-outline-secondary me-2" id="adminPanelBtn">Admin Panel</button>
                    <?php endif; ?>
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

    <div class="container">
        <!-- Flight Search Box -->
        <div class="search-box flight-search active" id="flight-search">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">Flight</h2>
                <div>
                    <a href="#hotel-search" class="text-decoration-none hotel-link btn btn-outline-primary" role="button">Hotel</a>
                    <a href="#tour-search" class="text-decoration-none tour-link btn btn-outline-primary" role="button">Tour</a>
                </div>
            </div>
            
            <hr class="divider">
            
            <div class="flight-type">
                <button class="flight-type-btn active" data-type="one-way">One Way</button>
                <button class="flight-type-btn" data-type="round-way">Round Way</button>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="home-location-input">
                        <label>FROM</label>
                        <input type="text" class="form-control" id="from-airport" name="from" value="" placeholder="Select Location" readonly>
                        <div class="airport-dropdown from-dropdown"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="home-location-input">
                        <label>TO</label>
                        <input type="text" class="form-control" id="to-airport" name="to" value="" placeholder="Select Location" readonly>
                        <div class="airport-dropdown to-dropdown"></div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="home-location-input">
                        <label>JOURNEY DATE</label>
                        <input type="text" class="form-control" id="journey-date" value="" placeholder="Select Date" readonly>
                        <small class="text-muted journey-date-day">Friday</small>
                    </div>
                </div>
                <div class="col-md-4 return-date-container">
                    <div class="home-location-input">
                        <label>RETURN DATE</label>
                        <input type="text" class="form-control" id="return-date" placeholder="Select Date" readonly>
                        <small class="text-muted return-date-day d-none"></small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="traveler-class-input">
                        <label>TRAVELER, CLASS</label>
                        <div class="traveler-class-container" id="traveler-class-container">
                            <div class="traveler-class-value">3 Travelers, Economy</div>
                        </div>
                        <div class="guest-info">3 Adults, 0 Children</div>
                        <div class="traveler-class-dropdown" id="traveler-class-dropdown">
                            <div class="passenger-type">
                                <div class="passenger-type-title">Adults</div>
                                <div class="passenger-type-description">12 years and above</div>
                                <div class="passenger-count">
                                    <button class="count-btn" id="adult-minus">-</button>
                                    <span class="count-value" id="adult-count">3</span>
                                    <button class="count-btn" id="adult-plus">+</button>
                                </div>
                            </div>
                            <div class="passenger-type">
                                <div class="passenger-type-title">Children</div>
                                <div class="passenger-type-description">11 years and under</div>
                                <div class="passenger-count">
                                    <button class="count-btn" id="child-minus">-</button>
                                    <span class="count-value" id="child-count">0</span>
                                    <button class="count-btn" id="child-plus">+</button>
                                </div>
                            </div>
                            <div class="class-selection mt-3">
                                <div class="passenger-type-title">Class</div>
                                <div class="btn-group w-100" role="group">
                                    <button type="button" class="btn btn-outline-primary class-option active" data-class="economy">Economy</button>
                                    <button type="button" class="btn btn-outline-primary class-option" data-class="business">Business</button>
                                </div>
                            </div>
                            <button class="done-btn btn btn-primary mt-3" id="done-btn">Done</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <button class="search-btn" onclick="window.location.href='flights.php?from=Dhaka&to=Cox\'s Bazar&journey_date=' + document.getElementById('journey-date').value + '&return_date=' + document.getElementById('return-date').value + '&travelers=' + document.getElementById('adult-count').textContent + '&children=' + document.getElementById('child-count').textContent + '&class=' + document.querySelector('.class-option.active').dataset.class">Search</button>
        </div>

        <!-- Hotel Search Box -->
        <div class="search-box hotel-search" id="hotel-search">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">Hotel</h2>
                <div>
                    <a href="#flight-search" class="text-decoration-none flight-link btn btn-outline-primary" role="button">Flight</a>
                    <a href="#tour-search" class="text-decoration-none tour-link btn btn-outline-primary" role="button">Tour</a>
                </div>
            </div>
            
            <hr class="divider">
            
            <?php
            // Fetch unique hotel locations for dropdown
            $hotelLocations = $pdo->query("SELECT DISTINCT Location FROM hotels ORDER BY Location")->fetchAll(PDO::FETCH_COLUMN);
            ?>
            <div class="location-input" style="position:relative;">
                <label>CITY/HOTEL/RESORT/AREA</label>
                <input type="text" class="form-control" id="hotel-location" placeholder="Select location" autocomplete="off">
                <div class="airport-dropdown hotel-location-dropdown"></div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="location-input">
                        <label>CHECK IN</label>
                        <input type="text" class="form-control" id="checkin-date" value="05 May '25" readonly>
                        <small class="text-muted checkin-date-day">Monday</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="location-input">
                        <label>CHECK OUT</label>
                        <input type="text" class="form-control" id="checkout-date" value="06 May '25" readonly>
                        <small class="text-muted checkout-date-day">Tuesday</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="guest-room-input">
                        <label>ROOMS & GUESTS</label>
                        <div class="guest-room-container" id="guest-room-container">
                            <div class="guest-room-value">1 Room, 2 Guests</div>
                        </div>
                        <div class="guest-info">1 Adult, 1 Child</div>
                        <div class="guest-room-dropdown" id="guest-room-dropdown">
                            <div class="passenger-type">
                                <div class="passenger-type-title">Rooms</div>
                                <div class="guest-room-count">
                                    <button class="guest-room-count-btn" id="room-minus">-</button>
                                    <span class="guest-room-count-value" id="room-count">1</span>
                                    <button class="guest-room-count-btn" id="room-plus">+</button>
                                </div>
                            </div>
                            <div class="passenger-type">
                                <div class="passenger-type-title">Adults</div>
                                <div class="passenger-type-description">11 years and above</div>
                                <div class="guest-room-count">
                                    <button class="guest-room-count-btn" id="hotel-adult-minus">-</button>
                                    <span class="guest-room-count-value" id="hotel-adult-count">1</span>
                                    <button class="guest-room-count-btn" id="hotel-adult-plus">+</button>
                                </div>
                            </div>
                            <div class="passenger-type">
                                <div class="passenger-type-title">Children</div>
                                <div class="passenger-type-description">10 years and under</div>
                                <div class="guest-room-count">
                                    <button class="guest-room-count-btn" id="hotel-child-minus">-</button>
                                    <span class="guest-room-count-value" id="hotel-child-count">1</span>
                                    <button class="guest-room-count-btn" id="hotel-child-plus">+</button>
                                </div>
                            </div>
                            <button class="guest-room-done-btn btn btn-primary mt-3" id="guest-room-done">Done</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <button class="search-btn" id="hotel-search-btn">Search</button>
        </div>

        <!-- Tour Search Box -->
        <div class="search-box tour-search-box" id="tour-search">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">Tour</h2>
                <div>
                    <a href="#flight-search" class="text-decoration-none flight-link btn btn-outline-primary" role="button">Flight</a>
                    <a href="#hotel-search" class="text-decoration-none hotel-link btn btn-outline-primary" role="button">Hotel</a>
                </div>
            </div>
            
            <hr class="divider">
            
            <div class="location-input" style="position:relative;">
                <label>DESTINATION</label>
                <input type="text" class="form-control" id="tour-destination" placeholder="Enter destination" autocomplete="off">
                <div class="airport-dropdown tour-destination-dropdown"></div>
            </div>
            
            <button class="search-btn" onclick="searchTours()">Search Tours</button>
        </div>
    </div>

    <div class="container">
        <footer class="row row-cols-1 row-cols-sm-2 row-cols-md-5 py-5 my-5 border-top">
            <div class="col mb-3">
                <p class="text-body-secondary">© Tourza 2025</p>
            </div>

            <div class="col mb-3">
            </div>

            <div class="col mb-3">
                <h5>Terms & Policies</h5>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2"><a href="#" class="nav-link p-0">Terms</a></li>
                    <li class="nav-item mb-2"><a href="#" class="nav-link p-0">Refund Policy</a></li>
                    <li class="nav-item mb-2"><a href="#" class="nav-link p-0">EMI Policy</a></li>
                    <li class="nav-item mb-2"><a href="#" class="nav-link p-0">Privacy Policy</a></li>
                </ul>
            </div>

            <div class="col mb-3">
                <h5>Help</h5>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2"><a href="#" class="nav-link p-0">FAQs</a></li>
                    <li class="nav-item mb-2"><a href="#" class="nav-link p-0">About</a></li>
                </ul>
            </div>

            <div class="col mb-3">
                <h5>Contact</h5>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2"><a href="#" class="nav-link p-0">info@tourza.com</a></li>
                    <li class="nav-item mb-2"><a href="#" class="nav-link p-0">+8801.........</a></li>
                    <li class="nav-item mb-2"><a href="#" class="nav-link p-0">Location</a></li>
                </ul>
            </div>
        </footer>
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
    function searchTours() {
        const destination = document.getElementById('tour-destination').value;
        window.location.href = `tours.php${destination ? '?destination=' + encodeURIComponent(destination) : ''}`;
    }

    // Tour destination dropdown functionality (styled like flights)
    $(document).ready(function() {
        const $tourDestinationInput = $('#tour-destination');
        const $tourDestinationDropdown = $('.tour-destination-dropdown');
        let tourDestinations = <?php
            $destinations = $pdo->query("SELECT DISTINCT Destination FROM tours ORDER BY Destination")->fetchAll(PDO::FETCH_COLUMN);
            echo json_encode($destinations);
        ?>;

        function showTourDestinationDropdown(filtered = null) {
            const list = filtered || tourDestinations;
            const options = list.map(dest =>
                `<div class=\"airport-option\" data-destination=\"${dest}\"><span class=\"airport-name\">${dest}</span></div>`
            ).join('');
            $tourDestinationDropdown.html(options).show();
        }

        $tourDestinationInput.on('focus click', function() {
            showTourDestinationDropdown();
        });

        $tourDestinationDropdown.on('click', '.airport-option', function() {
            const destination = $(this).data('destination');
            $tourDestinationInput.val(destination);
            $tourDestinationDropdown.hide();
        });

        // Hide dropdown when clicking outside
        $(document).on('mousedown', function(e) {
            if (!$tourDestinationInput.is(e.target) && !$tourDestinationDropdown.is(e.target) && $tourDestinationDropdown.has(e.target).length === 0) {
                $tourDestinationDropdown.hide();
            }
        });

        // Filter destinations as user types
        $tourDestinationInput.on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            const filteredDestinations = tourDestinations.filter(dest => 
                dest.toLowerCase().includes(searchTerm)
            );
            showTourDestinationDropdown(filteredDestinations);
        });
    });

    // Hotel location dropdown functionality
    $(document).ready(function() {
        const $hotelLocationInput = $('#hotel-location');
        const $hotelLocationDropdown = $('.hotel-location-dropdown');
        let hotelLocations = <?php echo json_encode($hotelLocations); ?>;

        function showHotelLocationDropdown(filtered = null) {
            const list = filtered || hotelLocations;
            const options = list.map(loc =>
                `<div class=\"airport-option\" data-location=\"${loc}\"><span class=\"airport-name\">${loc}</span></div>`
            ).join('');
            $hotelLocationDropdown.html(options).show();
        }

        $hotelLocationInput.on('focus click', function() {
            showHotelLocationDropdown();
        });

        $hotelLocationDropdown.on('click', '.airport-option', function() {
            const location = $(this).data('location');
            $hotelLocationInput.val(location);
            $hotelLocationDropdown.hide();
        });

        // Hide dropdown when clicking outside
        $(document).on('mousedown', function(e) {
            if (!$hotelLocationInput.is(e.target) && !$hotelLocationDropdown.is(e.target) && $hotelLocationDropdown.has(e.target).length === 0) {
                $hotelLocationDropdown.hide();
            }
        });

        // Filter locations as user types
        $hotelLocationInput.on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            const filteredLocations = hotelLocations.filter(loc => 
                loc.toLowerCase().includes(searchTerm)
            );
            showHotelLocationDropdown(filteredLocations);
        });

        // Hotel search redirect to hotels.php with filters
        $('#hotel-search-btn').on('click', function(e) {
            e.preventDefault();
            const location = $('#hotel-location').val() || '';
            // Optionally add more filters here (dates, guests, rooms)
            let url = 'hotels.php?';
            if (location) url += 'location=' + encodeURIComponent(location) + '&';
            // You can add checkin/checkout/guests/rooms params here if needed
            url = url.replace(/&$/, '');
            window.location.href = url;
        });
    });

    <?php if ($isLoggedIn): ?>
    const isAdmin = <?php echo (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) ? 'true' : 'false'; ?>;
    $('#adminPanelBtn').on('click', function() {
        if (isAdmin) {
            window.location.href = 'admin.php';
        } else {
            alert('Access denied: You are not the admin.');
        }
    });
    <?php endif; ?>
    </script>
</body>
</html> 