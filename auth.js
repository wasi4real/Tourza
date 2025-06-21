$(document).ready(function() {
    // Tab switching
    $('.auth-tab').click(function() {
        $('.auth-tab').removeClass('active');
        $(this).addClass('active');
        const tab = $(this).data('tab');
        $('.auth-form').removeClass('active');
        $(`#${tab}-form`).addClass('active');
    });

    // Modal switching
    $('#showSignup').on('click', function(e) {
        e.preventDefault();
        $('#authModal').modal('hide');
        $('#signupModal').modal('show');
    });
    $('#showLogin').on('click', function(e) {
        e.preventDefault();
        $('#signupModal').modal('hide');
        $('#authModal').modal('show');
    });

    // Login form submission
    $('#loginForm').on('submit', async function(e) {
        e.preventDefault();
        const email = $('#loginEmail').val();
        const password = $('#loginPassword').val();
        try {
            const response = await fetch('auth.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'login', email, password })
            });
            const data = await response.json();
            if (data.success) {
                if (data.isAdmin) {
                    window.location.href = 'admin.php';
                } else {
                    window.location.reload();
                }
            } else {
                alert(data.message || 'Login failed');
            }
        } catch (error) {
            console.error('Error during login:', error);
            alert('An error occurred during login');
        }
    });

    // Signup form submission
    $('#signupForm').on('submit', async function(e) {
        e.preventDefault();
        const firstName = $('#firstName').val();
        const lastName = $('#lastName').val();
        const email = $('#signupEmail').val();
        const password = $('#signupPassword').val();
        const confirmPassword = $('#confirmPassword').val();
        
        if (password !== confirmPassword) {
            alert('Passwords do not match');
            return;
        }
        
        try {
            const response = await fetch('auth.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    action: 'register', 
                    firstName, 
                    lastName, 
                    email, 
                    password 
                })
            });
            const data = await response.json();
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Registration failed');
            }
        } catch (error) {
            console.error('Error during registration:', error);
            alert('An error occurred during registration');
        }
    });

    // Auth state
    let isLoggedIn = false;
    let currentUser = null;

    // Check auth state on page load
    async function checkAuthState() {
        try {
            const response = await fetch('check-auth.php');
            const data = await response.json();
            if (data.isLoggedIn) {
                isLoggedIn = data.isLoggedIn;
                currentUser = data.user;
                updateUI();
            }
        } catch (error) {
            console.error('Error checking auth state:', error);
        }
    }

    // Update UI based on auth state
    function updateUI() {
        const authButton = document.querySelector('[data-bs-target="#authModal"]');
        const userDropdown = document.querySelector('.dropdown');
        if (isLoggedIn && currentUser) {
            if (authButton) authButton.style.display = 'none';
            if (userDropdown) userDropdown.style.display = 'block';
            // Update user info in dropdown
            const userImage = userDropdown.querySelector('img');
            const userName = userDropdown.querySelector('.dropdown-toggle');
            if (userName) {
                userName.textContent = currentUser.first_name;
            }
        } else {
            if (authButton) authButton.style.display = 'block';
            if (userDropdown) userDropdown.style.display = 'none';
        }
    }

    // Logout
    window.logout = async function() {
        try {
            const response = await fetch('auth.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'logout' })
            });
            const data = await response.json();
            if (data.success) {
                window.location.reload();
            }
        } catch (error) {
            console.error('Error during logout:', error);
            alert('An error occurred during logout');
        }
    };

    // Check auth state when page loads
    checkAuthState();
}); 