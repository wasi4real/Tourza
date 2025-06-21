$(document).ready(function() {
    // Navigation between search boxes
    const $flightSearch = $('#flight-search');
    const $hotelSearch = $('#hotel-search');
    const $tourSearch = $('#tour-search');
    
    // Function to switch between search boxes
    function showFlightSearch() {
        $('.search-box').removeClass('active');
        $flightSearch.addClass('active');
        updateNavActiveState('flight');
    }
    
    function showHotelSearch() {
        $('.search-box').removeClass('active');
        $hotelSearch.addClass('active');
        updateNavActiveState('hotel');
    }
    
    function showTourSearch() {
        $('.search-box').removeClass('active');
        $tourSearch.addClass('active');
        updateNavActiveState('tour');
    }

    function updateNavActiveState(activeTab) {
        $('.nav-link').removeClass('active');
        $(`.${activeTab}-nav-link`).addClass('active');
    }
    
    // Show flight search when clicking Flight links
    $('.flight-link, .flight-nav-link').on('click', function(e) {
        e.preventDefault();
        showFlightSearch();
    });
    
    // Show hotel search when clicking Hotel links
    $('.hotel-link, .hotel-nav-link').on('click', function(e) {
        e.preventDefault();
        showHotelSearch();
    });
    
    // Show tour search when clicking Tour links
    $('.tour-link, .tour-nav-link').on('click', function(e) {
        e.preventDefault();
        showTourSearch();
    });

    // Check URL hash on page load
    if (window.location.hash === '#hotel-search') {
        showHotelSearch();
    } else if (window.location.hash === '#tour-search') {
        showTourSearch();
    } else {
        showFlightSearch();
    }

    // Hotel date pickers
    const checkinDate = flatpickr("#checkin-date", {
        minDate: "today",
        dateFormat: "d M 'y",
        defaultDate: "2025-05-05",
        onChange: function(selectedDates, dateStr, instance) {
            const dayName = new Date(selectedDates[0]).toLocaleDateString('en-US', { weekday: 'long' });
            $('.checkin-date-day').text(dayName);
            checkoutDate.set('minDate', dateStr);
        }
    });

    const checkoutDate = flatpickr("#checkout-date", {
        minDate: "2025-05-06",
        dateFormat: "d M 'y",
        defaultDate: "2025-05-06",
        onChange: function(selectedDates, dateStr, instance) {
            const dayName = new Date(selectedDates[0]).toLocaleDateString('en-US', { weekday: 'long' });
            $('.checkout-date-day').text(dayName);
        }
    });

    // Hotel guest and room selection
    const $guestRoomContainer = $('#guest-room-container');
    const $guestRoomDropdown = $('#guest-room-dropdown');
    const $roomCount = $('#room-count');
    const $hotelAdultCount = $('#hotel-adult-count');
    const $hotelChildCount = $('#hotel-child-count');
    const $guestRoomValue = $('#guest-room-container .guest-room-value');
    const $guestRoomInfo = $('#guest-room-container').parent().find('.guest-info');

    // Toggle dropdown
    $guestRoomContainer.on('click', function(e) {
        e.stopPropagation();
        $guestRoomDropdown.toggleClass('show');
    });

    // Handle count changes
    function handleHotelCountChange(e) {
        e.stopPropagation();
        const target = $(e.target);
        if (target.attr('id') === 'room-plus') {
            $roomCount.text(parseInt($roomCount.text()) + 1);
        } else if (target.attr('id') === 'room-minus' && parseInt($roomCount.text()) > 1) {
            $roomCount.text(parseInt($roomCount.text()) - 1);
        } else if (target.attr('id') === 'hotel-adult-plus') {
            $hotelAdultCount.text(parseInt($hotelAdultCount.text()) + 1);
        } else if (target.attr('id') === 'hotel-adult-minus' && parseInt($hotelAdultCount.text()) > 1) {
            $hotelAdultCount.text(parseInt($hotelAdultCount.text()) - 1);
        } else if (target.attr('id') === 'hotel-child-plus') {
            $hotelChildCount.text(parseInt($hotelChildCount.text()) + 1);
        } else if (target.attr('id') === 'hotel-child-minus' && parseInt($hotelChildCount.text()) > 0) {
            $hotelChildCount.text(parseInt($hotelChildCount.text()) - 1);
        }
        updateGuestRoomInput();
    }

    $('#room-plus, #room-minus, #hotel-adult-plus, #hotel-adult-minus, #hotel-child-plus, #hotel-child-minus').on('click', handleHotelCountChange);

    // Close dropdown when done is clicked
    $('#guest-room-done').on('click', function(e) {
        e.stopPropagation();
        $guestRoomDropdown.removeClass('show');
    });

    // Update guest room input field
    function updateGuestRoomInput() {
        const rooms = parseInt($roomCount.text());
        const adults = parseInt($hotelAdultCount.text());
        const children = parseInt($hotelChildCount.text());
        const totalGuests = adults + children;
        $guestRoomValue.text(
            `${rooms} ${rooms === 1 ? 'Room' : 'Rooms'}, ${totalGuests} ${totalGuests === 1 ? 'Guest' : 'Guests'}`
        );
        $guestRoomInfo.text(
            `${adults} ${adults === 1 ? 'Adult' : 'Adults'}${children > 0 ? `, ${children} ${children === 1 ? 'Child' : 'Children'}` : ''}`
        );
    }

    // Set initial values for hotel guest/room
    updateGuestRoomInput();

    // Close dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$guestRoomContainer.is(e.target) && !$guestRoomDropdown.is(e.target) && $guestRoomDropdown.has(e.target).length === 0) {
            $guestRoomDropdown.removeClass('show');
        }
    });

    // Flight type selection
    $('.flight-type-btn').on('click', function() {
        $('.flight-type-btn').removeClass('active');
        $(this).addClass('active');
        
        const returnDateContainer = $('.return-date-container');
        if ($(this).data('type') === 'one-way') {
            returnDateContainer.hide();
        } else {
            returnDateContainer.show();
        }
    });
    
    // Initially hide return date for one-way
    $('.return-date-container').hide();

    // Date pickers
    const journeyDate = flatpickr("#journey-date", {
        minDate: "today",
        dateFormat: "Y-m-d",
        defaultDate: $("#journey-date").val() || "2025-08-01",
        onChange: function(selectedDates, dateStr, instance) {
            const dayName = new Date(selectedDates[0]).toLocaleDateString('en-US', { weekday: 'long' });
            $('.journey-date-day').text(dayName);
            returnDate.set('minDate', dateStr);
        }
    });

    const returnDate = flatpickr("#return-date", {
        minDate: $("#journey-date").val() || "2025-08-01",
        dateFormat: "Y-m-d",
        defaultDate: $("#return-date").val() || null,
        onChange: function(selectedDates, dateStr, instance) {
            const dayName = new Date(selectedDates[0]).toLocaleDateString('en-US', { weekday: 'long' });
            $('.return-date-day').text(dayName).removeClass('d-none');
        }
    });

    // Traveler and class selection
    const $travelerClassContainer = $('#traveler-class-container');
    const $travelerClassDropdown = $('#traveler-class-dropdown');
    const $adultCount = $('#adult-count');
    const $childCount = $('#child-count');
    const $classOptions = $('.class-option');
    let selectedClass = 'economy';

    // Toggle dropdown
    $travelerClassContainer.on('click', function(e) {
        e.stopPropagation();
        $travelerClassDropdown.toggleClass('show');
    });

    // Handle passenger count changes
    function handleCountChange(e) {
        e.stopPropagation();
        const target = $(e.target);
        if (target.attr('id') === 'adult-plus') {
            $adultCount.text(parseInt($adultCount.text()) + 1);
        } else if (target.attr('id') === 'adult-minus' && parseInt($adultCount.text()) > 1) {
            $adultCount.text(parseInt($adultCount.text()) - 1);
        } else if (target.attr('id') === 'child-plus') {
            $childCount.text(parseInt($childCount.text()) + 1);
        } else if (target.attr('id') === 'child-minus' && parseInt($childCount.text()) > 0) {
            $childCount.text(parseInt($childCount.text()) - 1);
        }
        updateTravelerInput();
    }

    $('#adult-plus, #adult-minus, #child-plus, #child-minus').on('click', handleCountChange);

    // Handle class selection
    $classOptions.on('click', function(e) {
        e.stopPropagation();
        $classOptions.removeClass('active btn-primary').addClass('btn-outline-primary');
        $(this).addClass('active btn-primary').removeClass('btn-outline-primary');
        selectedClass = $(this).data('class');
        updateTravelerInput();
    });

    // Close dropdown when done is clicked
    $('#done-btn').on('click', function(e) {
        e.stopPropagation();
        $travelerClassDropdown.removeClass('show');
    });

    // Update traveler input field
    function updateTravelerInput() {
        const adults = parseInt($adultCount.text());
        const children = parseInt($childCount.text());
        const totalTravelers = adults + children;
        
        $('.traveler-class-value').text(
            `${totalTravelers} ${totalTravelers === 1 ? 'Traveler' : 'Travelers'}, ${selectedClass === 'economy' ? 'Economy' : 'Business'}`
        );
        $('.traveler-class-container + .guest-info').text(
            `${adults} ${adults === 1 ? 'Adult' : 'Adults'}${children > 0 ? `, ${children} ${children === 1 ? 'Child' : 'Children'}` : ''}`
        );
    }

    // Set initial values
    updateTravelerInput();
    updateGuestRoomInput();

    // Close dropdown when clicking outside
    $(document).on('click', function() {
        $travelerClassDropdown.removeClass('show');
    });

    // FLIGHT AIRPORT DROPDOWN (like hotels/tours)
    const $fromInput = $('#from-airport');
    const $toInput = $('#to-airport');
    const $fromDropdown = $('.from-dropdown');
    const $toDropdown = $('.to-dropdown');
    let airportCodes = [];

    // Fetch airport codes from backend
    async function fetchAirports() {
        if (airportCodes.length === 0) {
            try {
                const response = await fetch('flights.php?action=get_airports');
                airportCodes = await response.json();
            } catch (e) {
                airportCodes = [];
            }
        }
        return airportCodes;
    }

    function showAirportDropdown($input, $dropdown, filtered = null) {
        const list = filtered || airportCodes;
        const options = list.map(airport =>
            `<div class="airport-option" data-airport="${airport.code}" data-display="${airport.city}, ${airport.country} (${airport.code})">
                <span class="airport-name">${airport.city}, ${airport.country} (${airport.code})</span>
            </div>`
        ).join('');
        $dropdown.html(options).show();
    }

    $fromInput.on('focus click', async function() {
        await fetchAirports();
        showAirportDropdown($fromInput, $fromDropdown);
    });
    $toInput.on('focus click', async function() {
        await fetchAirports();
        showAirportDropdown($toInput, $toDropdown);
    });

    $fromDropdown.on('click', '.airport-option', function() {
        const display = $(this).data('display');
        $fromInput.val(display);
        $fromDropdown.hide();
    });
    $toDropdown.on('click', '.airport-option', function() {
        const display = $(this).data('display');
        $toInput.val(display);
        $toDropdown.hide();
    });

    // Filter as user types
    $fromInput.on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        const filtered = airportCodes.filter(airport =>
            (airport.city && airport.city.toLowerCase().includes(searchTerm)) ||
            (airport.country && airport.country.toLowerCase().includes(searchTerm)) ||
            (airport.code && airport.code.toLowerCase().includes(searchTerm))
        );
        showAirportDropdown($fromInput, $fromDropdown, filtered);
    });
    $toInput.on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        const filtered = airportCodes.filter(airport =>
            (airport.city && airport.city.toLowerCase().includes(searchTerm)) ||
            (airport.country && airport.country.toLowerCase().includes(searchTerm)) ||
            (airport.code && airport.code.toLowerCase().includes(searchTerm))
        );
        showAirportDropdown($toInput, $toDropdown, filtered);
    });

    // Function to update search parameters in URL
    function getAirportCodeFromDisplay(display) {
        // Extract code from 'City, Country (CODE)'
        const match = display.match(/\(([^)]+)\)$/);
        return match ? match[1] : display;
    }

    function updateSearchParams() {
        const fromInput = $('#from-airport');
        const toInput = $('#to-airport');
        const fromCode = getAirportCodeFromDisplay(fromInput.val());
        const toCode = getAirportCodeFromDisplay(toInput.val());
        const params = new URLSearchParams({
            from: fromCode,
            to: toCode,
            journey_date: $('#journey-date').val(),
            return_date: $('#return-date').val(),
            travelers: $('#adult-count').text(),
            children: $('#child-count').text(),
            class: $('.class-option.active').data('class')
        });
        return params.toString();
    }

    // Redirect to flights.php when flight search button is clicked
    $('#flight-search .search-btn').on('click', function(e) {
        e.preventDefault();
        const params = updateSearchParams();
        window.location.href = 'flights.php?' + params;
    });

    // Header traveler/class dropdown logic for flights page
    const $headerAdultCount = $('#adult-count');
    const $headerChildCount = $('#child-count');
    const $headerClassOptions = $('#traveler-class-dropdown .class-option');
    const $headerAdultHidden = $('#header-adult-count');
    const $headerChildHidden = $('#header-child-count');
    const $headerClassHidden = $('#header-class');
    const $headerTravelerClassValue = $('.traveler-class-value');

    function updateHeaderTravelerFields() {
        const adults = parseInt($headerAdultCount.text());
        const children = parseInt($headerChildCount.text());
        const classType = $('#traveler-class-dropdown .class-option.active').data('class');
        $headerAdultHidden.val(adults);
        $headerChildHidden.val(children);
        $headerClassHidden.val(classType);
        $headerTravelerClassValue.text(
            `${adults + children} ${(adults + children) === 1 ? 'Traveler' : 'Travelers'}, ${classType.charAt(0).toUpperCase() + classType.slice(1)}`
        );
    }

    // Update on plus/minus click
    $('#adult-plus, #adult-minus, #child-plus, #child-minus').on('click', function() {
        updateHeaderTravelerFields();
    });
    // Update on class selection
    $headerClassOptions.on('click', function() {
        $headerClassOptions.removeClass('active btn-primary').addClass('btn-outline-primary');
        $(this).addClass('active btn-primary').removeClass('btn-outline-primary');
        updateHeaderTravelerFields();
    });
    // Update on done
    $('#done-btn').on('click', function() {
        updateHeaderTravelerFields();
    });
    // Initialize on page load
    updateHeaderTravelerFields();

    // Close airport dropdowns when clicking outside
    $(document).on('mousedown', function(e) {
        if (!$(e.target).closest('#from-airport, .from-dropdown').length) {
            $('.from-dropdown').hide();
        }
        if (!$(e.target).closest('#to-airport, .to-dropdown').length) {
            $('.to-dropdown').hide();
        }
    });

    function syncFlightSearchFields() {
        // Get values from the visible search box
        const fromVal = $('#from-airport').val();
        const toVal = $('#to-airport').val();
        const journeyDateVal = $('#journey-date').val();
        const returnDateVal = $('#return-date').val();
        const travelersVal = $('#adult-count').text();
        const childrenVal = $('#child-count').text();
        const classVal = $('.class-option.active').data('class');
        // Set values in all matching fields (header and section)
        $("#from-airport").val(fromVal);
        $("#to-airport").val(toVal);
        $("#journey-date").val(journeyDateVal);
        $("#return-date").val(returnDateVal);
        $("#adult-count").text(travelersVal);
        $("#child-count").text(childrenVal);
        $('.class-option').removeClass('active btn-primary').addClass('btn-outline-primary');
        $('.class-option[data-class="' + classVal + '"]').addClass('active btn-primary').removeClass('btn-outline-primary');
        updateTravelerInput();
        updateHeaderTravelerFields();
    }

    // Call syncFlightSearchFields after any search or when switching tabs
    $('#flight-search .search-btn').on('click', function() {
        syncFlightSearchFields();
    });
    $('.flight-link, .flight-nav-link').on('click', function() {
        setTimeout(syncFlightSearchFields, 100);
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
});
