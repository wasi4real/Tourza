-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `banned` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `session_token` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `flights`
--

CREATE TABLE `flights` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `airline` varchar(100) NOT NULL,
  `departure_airport_code` varchar(10) NOT NULL,
  `departure_airport_name` varchar(100) NOT NULL,
  `departure_city` varchar(100) NOT NULL,
  `departure_country` varchar(100) NOT NULL,
  `arrival_airport_code` varchar(10) NOT NULL,
  `arrival_airport_name` varchar(100) NOT NULL,
  `arrival_city` varchar(100) NOT NULL,
  `arrival_country` varchar(100) NOT NULL,
  `departure_time` datetime NOT NULL,
  `arrival_time` datetime NOT NULL,
  `duration` int(11) NOT NULL,
  `class` enum('economy','business') NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `flights`
--

INSERT INTO `flights` (`id`, `airline`, `departure_airport_code`, `departure_airport_name`, `departure_city`, `departure_country`, `arrival_airport_code`, `arrival_airport_name`, `arrival_city`, `arrival_country`, `departure_time`, `arrival_time`, `duration`, `class`, `price`, `created_at`) VALUES
(1, 'Biman Bangladesh', 'DAC', 'Hazrat Shahjalal International Airport', 'Dhaka', 'Bangladesh', 'LHR', 'Heathrow Airport', 'London', 'UK', '2025-09-01 08:00:00', '2025-09-01 14:00:00', 360, 'economy', 650.00, '2025-05-13 19:25:59'),
(2, 'Biman Bangladesh', 'DAC', 'Hazrat Shahjalal International Airport', 'Dhaka', 'Bangladesh', 'LHR', 'Heathrow Airport', 'London', 'UK', '2025-09-01 08:00:00', '2025-09-01 14:00:00', 360, 'business', 1200.00, '2025-05-13 19:25:59'),
(3, 'Air France', 'JFK', 'John F. Kennedy International Airport', 'New York', 'USA', 'CDG', 'Charles de Gaulle Airport', 'Paris', 'France', '2025-09-02 19:00:00', '2025-09-03 08:00:00', 420, 'economy', 700.00, '2025-05-13 19:25:59'),
(4, 'Air France', 'JFK', 'John F. Kennedy International Airport', 'New York', 'USA', 'CDG', 'Charles de Gaulle Airport', 'Paris', 'France', '2025-09-02 19:00:00', '2025-09-03 08:00:00', 420, 'business', 1350.00, '2025-05-13 19:25:59'),
(5, 'Qantas', 'HND', 'Haneda Airport', 'Tokyo', 'Japan', 'SYD', 'Sydney Kingsford Smith Airport', 'Sydney', 'Australia', '2025-09-10 22:00:00', '2025-09-11 09:00:00', 540, 'economy', 900.00, '2025-05-13 19:25:59'),
(6, 'Qantas', 'HND', 'Haneda Airport', 'Tokyo', 'Japan', 'SYD', 'Sydney Kingsford Smith Airport', 'Sydney', 'Australia', '2025-09-10 22:00:00', '2025-09-11 09:00:00', 540, 'business', 1700.00, '2025-05-13 19:25:59'),
(7, 'Turkish Airlines', 'IST', 'Istanbul Airport', 'Istanbul', 'Turkey', 'DXB', 'Dubai International Airport', 'Dubai', 'UAE', '2025-10-05 13:00:00', '2025-10-05 18:00:00', 300, 'economy', 400.00, '2025-05-13 19:25:59'),
(8, 'Turkish Airlines', 'IST', 'Istanbul Airport', 'Istanbul', 'Turkey', 'DXB', 'Dubai International Airport', 'Dubai', 'UAE', '2025-10-05 13:00:00', '2025-10-05 18:00:00', 300, 'business', 800.00, '2025-05-13 19:25:59'),
(9, 'Singapore Airlines', 'BOM', 'Chhatrapati Shivaji Maharaj International Airport', 'Mumbai', 'India', 'SIN', 'Changi Airport', 'Singapore', 'Singapore', '2025-11-15 06:00:00', '2025-11-15 12:00:00', 360, 'economy', 350.00, '2025-05-13 19:25:59'),
(10, 'Singapore Airlines', 'BOM', 'Chhatrapati Shivaji Maharaj International Airport', 'Mumbai', 'India', 'SIN', 'Changi Airport', 'Singapore', 'Singapore', '2025-11-15 06:00:00', '2025-11-15 12:00:00', 360, 'business', 650.00, '2025-05-13 19:25:59'),
(11, 'Emirates', 'DXB', 'Dubai International Airport', 'Dubai', 'UAE', 'JFK', 'John F. Kennedy International Airport', 'New York', 'USA', '2025-12-01 02:00:00', '2025-12-01 10:00:00', 480, 'economy', 950.00, '2025-05-13 19:25:59'),
(12, 'Emirates', 'DXB', 'Dubai International Airport', 'Dubai', 'UAE', 'JFK', 'John F. Kennedy International Airport', 'New York', 'USA', '2025-12-01 02:00:00', '2025-12-01 10:00:00', 480, 'business', 1800.00, '2025-05-13 19:25:59'),
(13, 'Qatar Airways', 'DOH', 'Hamad International Airport', 'Doha', 'Qatar', 'SYD', 'Sydney Kingsford Smith Airport', 'Sydney', 'Australia', '2025-12-15 20:00:00', '2025-12-16 08:00:00', 720, 'economy', 1100.00, '2025-05-13 19:25:59'),
(14, 'Qatar Airways', 'DOH', 'Hamad International Airport', 'Doha', 'Qatar', 'SYD', 'Sydney Kingsford Smith Airport', 'Sydney', 'Australia', '2025-12-15 20:00:00', '2025-12-16 08:00:00', 720, 'business', 2100.00, '2025-05-13 19:25:59'),
(15, 'Lufthansa', 'FRA', 'Frankfurt Airport', 'Frankfurt', 'Germany', 'JFK', 'John F. Kennedy International Airport', 'New York', 'USA', '2025-12-20 10:00:00', '2025-12-20 14:00:00', 480, 'economy', 800.00, '2025-05-13 19:25:59'),
(16, 'Lufthansa', 'FRA', 'Frankfurt Airport', 'Frankfurt', 'Germany', 'JFK', 'John F. Kennedy International Airport', 'New York', 'USA', '2025-12-20 10:00:00', '2025-12-20 14:00:00', 480, 'business', 1500.00, '2025-05-13 19:25:59'),
(17, 'Air France', 'CDG', 'Charles de Gaulle Airport', 'Paris', 'France', 'JFK', 'John F. Kennedy International Airport', 'New York', 'USA', '2025-05-14 10:30:00', '2025-05-14 13:00:00', 9, 'business', 899.99, '2025-05-13 20:19:12'),
(18, 'EgyptAir', 'JFK', 'John F. Kennedy International Airport', 'New York', 'USA', 'CAI', 'Cairo International Airport', 'Cairo', 'Egypt', '2025-05-15 18:00:00', '2025-05-16 10:00:00', 10, 'business', 999.99, '2025-05-13 20:19:12'),
(19, 'Qantas', 'SYD', 'Sydney Kingsford Smith Airport', 'Sydney', 'Australia', 'YYZ', 'Toronto Pearson International Airport', 'Niagara', 'Canada', '2025-05-16 08:00:00', '2025-05-16 17:30:00', 18, 'business', 1299.99, '2025-05-13 20:19:12'),
(20, 'British Airways', 'LHR', 'Heathrow Airport', 'London', 'UK', 'CDG', 'Charles de Gaulle Airport', 'Paris', 'France', '2025-05-17 09:00:00', '2025-05-17 11:15:00', 2, 'economy', 199.99, '2025-05-13 20:19:12'),
(21, 'Thai Airways', 'SEA', 'Seattle–Tacoma International Airport', 'Seattle', 'USA', 'BKK', 'Suvarnabhumi Airport', 'Bangkok', 'Thailand', '2025-05-18 22:00:00', '2025-05-19 07:30:00', 15, 'business', 1099.99, '2025-05-13 20:19:12'),
(22, 'Turkish Airlines', 'ISB', 'Islamabad International Airport', 'Islamabad', 'Pakistan', 'IST', 'Istanbul Airport', 'Istanbul', 'Turkey', '2025-05-19 06:00:00', '2025-05-19 09:30:00', 5, 'business', 499.99, '2025-05-13 20:19:12'),
(23, 'SriLankan Airlines', 'CMB', 'Bandaranaike International Airport', 'Colombo', 'Sri Lanka', 'LHR', 'Heathrow Airport', 'London', 'UK', '2025-05-20 01:00:00', '2025-05-20 08:00:00', 11, 'business', 899.99, '2025-05-13 20:19:12'),
(24, 'Qantas', 'BKK', 'Suvarnabhumi Airport', 'Bangkok', 'Thailand', 'MEL', 'Melbourne Airport', 'Melbourne', 'Australia', '2025-05-21 14:30:00', '2025-05-21 23:00:00', 9, 'business', 799.99, '2025-05-13 20:19:12'),
(25, 'United Airlines', 'BNE', 'Brisbane Airport', 'Brisbane', 'Australia', 'JFK', 'John F. Kennedy International Airport', 'New York', 'USA', '2025-05-22 07:00:00', '2025-05-22 18:00:00', 17, 'business', 1399.99, '2025-05-13 20:19:12'),
(26, 'American Airlines', 'MIA', 'Miami International Airport', 'Miami', 'USA', 'LHR', 'Heathrow Airport', 'London', 'UK', '2025-05-23 20:00:00', '2025-05-24 08:30:00', 9, 'business', 899.99, '2025-05-13 20:19:12'),
(27, 'Air France', 'CDG', 'Charles de Gaulle Airport', 'Paris', 'France', 'JFK', 'John F. Kennedy International Airport', 'New York', 'USA', '2025-05-01 08:00:00', '2025-05-01 11:00:00', 9, 'business', 890.00, '2025-05-13 20:19:20'),
(28, 'British Airways', 'LHR', 'Heathrow Airport', 'London', 'UK', 'DXB', 'Dubai International Airport', 'Dubai', 'UAE', '2025-05-02 14:00:00', '2025-05-02 23:30:00', 7, 'economy', 520.00, '2025-05-13 20:19:20'),
(29, 'Emirates', 'DXB', 'Dubai International Airport', 'Dubai', 'UAE', 'SYD', 'Kingsford Smith Airport', 'Sydney', 'Australia', '2025-05-03 09:30:00', '2025-05-04 05:45:00', 14, 'business', 1350.00, '2025-05-13 20:19:20'),
(30, 'Singapore Airlines', 'SIN', 'Changi Airport', 'Singapore', 'Singapore', 'LAX', 'Los Angeles International Airport', 'Los Angeles', 'USA', '2025-05-04 21:00:00', '2025-05-05 11:30:00', 17, 'economy', 960.00, '2025-05-13 20:19:20'),
(31, 'Delta', 'JFK', 'John F. Kennedy International Airport', 'New York', 'USA', 'LHR', 'Heathrow Airport', 'London', 'UK', '2025-05-05 18:00:00', '2025-05-06 06:30:00', 8, 'economy', 620.00, '2025-05-13 20:19:20'),
(32, 'Qatar Airways', 'DOH', 'Hamad International Airport', 'Doha', 'Qatar', 'BOM', 'Chhatrapati Shivaji International Airport', 'Mumbai', 'India', '2025-05-06 01:00:00', '2025-05-06 07:00:00', 4, 'business', 780.00, '2025-05-13 20:19:20'),
(33, 'Lufthansa', 'FRA', 'Frankfurt Airport', 'Frankfurt', 'Germany', 'ORD', 'O\'Hare International Airport', 'Chicago', 'USA', '2025-05-07 12:30:00', '2025-05-07 17:00:00', 10, 'economy', 700.00, '2025-05-13 20:19:20'),
(34, 'Turkish Airlines', 'IST', 'Istanbul Airport', 'Istanbul', 'Turkey', 'ISB', 'Islamabad International Airport', 'Islamabad', 'Pakistan', '2025-05-08 06:00:00', '2025-05-08 11:00:00', 5, 'economy', 430.00, '2025-05-13 20:19:20'),
(35, 'Thai Airways', 'BKK', 'Suvarnabhumi Airport', 'Bangkok', 'Thailand', 'MEL', 'Melbourne Airport', 'Melbourne', 'Australia', '2025-05-09 20:00:00', '2025-05-10 06:00:00', 9, 'business', 1150.00, '2025-05-13 20:19:20'),
(36, 'United Airlines', 'ORD', 'O\'Hare International Airport', 'Chicago', 'USA', 'CDG', 'Charles de Gaulle Airport', 'Paris', 'France', '2025-05-10 17:30:00', '2025-05-11 08:00:00', 8, 'economy', 690.00, '2025-05-13 20:19:20'),
(37, 'American Airlines', 'MIA', 'Miami International Airport', 'Miami', 'USA', 'JFK', 'John F. Kennedy International Airport', 'New York', 'USA', '2025-05-11 06:00:00', '2025-05-11 09:00:00', 3, 'economy', 210.00, '2025-05-13 20:19:20'),
(38, 'KLM', 'AMS', 'Amsterdam Schiphol Airport', 'Amsterdam', 'Netherlands', 'DXB', 'Dubai International Airport', 'Dubai', 'UAE', '2025-05-12 11:00:00', '2025-05-12 20:00:00', 7, 'economy', 640.00, '2025-05-13 20:19:20'),
(39, 'Etihad Airways', 'AUH', 'Abu Dhabi International Airport', 'Abu Dhabi', 'UAE', 'LAX', 'Los Angeles International Airport', 'Los Angeles', 'USA', '2025-05-13 23:00:00', '2025-05-14 14:30:00', 16, 'business', 1400.00, '2025-05-13 20:19:20'),
(40, 'Cathay Pacific', 'HKG', 'Hong Kong International Airport', 'Hong Kong', 'China', 'JFK', 'John F. Kennedy International Airport', 'New York', 'USA', '2025-05-14 10:00:00', '2025-05-14 18:00:00', 16, 'business', 1280.00, '2025-05-13 20:19:20'),
(41, 'Air India', 'DEL', 'Indira Gandhi International Airport', 'New Delhi', 'India', 'LHR', 'Heathrow Airport', 'London', 'UK', '2025-05-15 04:00:00', '2025-05-15 11:30:00', 9, 'economy', 500.00, '2025-05-13 20:19:20'),
(42, 'Qantas', 'SYD', 'Sydney Airport', 'Sydney', 'Australia', 'SIN', 'Changi Airport', 'Singapore', 'Singapore', '2025-05-16 05:00:00', '2025-05-16 10:30:00', 8, 'business', 950.00, '2025-05-13 20:19:20'),
(43, 'ANA', 'HND', 'Haneda Airport', 'Tokyo', 'Japan', 'LAX', 'Los Angeles International Airport', 'Los Angeles', 'USA', '2025-05-17 19:00:00', '2025-05-17 13:00:00', 10, 'economy', 830.00, '2025-05-13 20:19:20'),
(44, 'Turkish Airlines', 'IST', 'Istanbul Airport', 'Istanbul', 'Turkey', 'FRA', 'Frankfurt Airport', 'Frankfurt', 'Germany', '2025-05-18 07:00:00', '2025-05-18 09:30:00', 3, 'economy', 330.00, '2025-05-13 20:19:20'),
(45, 'Lufthansa', 'FRA', 'Frankfurt Airport', 'Frankfurt', 'Germany', 'BOM', 'Chhatrapati Shivaji International Airport', 'Mumbai', 'India', '2025-05-19 12:00:00', '2025-05-19 23:00:00', 9, 'business', 950.00, '2025-05-13 20:19:20'),
(46, 'Emirates', 'DXB', 'Dubai International Airport', 'Dubai', 'UAE', 'ICN', 'Incheon International Airport', 'Seoul', 'South Korea', '2025-05-20 09:00:00', '2025-05-20 20:00:00', 10, 'business', 1230.00, '2025-05-13 20:19:20'),
(47, 'Delta', 'ATL', 'Hartsfield–Jackson Atlanta International Airport', 'Atlanta', 'USA', 'CDG', 'Charles de Gaulle Airport', 'Paris', 'France', '2025-05-21 17:00:00', '2025-05-22 06:30:00', 9, 'economy', 720.00, '2025-05-13 20:19:20'),
(48, 'Korean Air', 'ICN', 'Incheon International Airport', 'Seoul', 'South Korea', 'SYD', 'Sydney Airport', 'Sydney', 'Australia', '2025-05-22 01:00:00', '2025-05-22 11:00:00', 10, 'economy', 890.00, '2025-05-13 20:19:20'),
(49, 'Swiss', 'ZRH', 'Zurich Airport', 'Zurich', 'Switzerland', 'JFK', 'John F. Kennedy International Airport', 'New York', 'USA', '2025-05-23 13:00:00', '2025-05-23 19:00:00', 8, 'business', 1020.00, '2025-05-13 20:19:20'),
(50, 'Qatar Airways', 'DOH', 'Hamad International Airport', 'Doha', 'Qatar', 'JNB', 'O. R. Tambo International Airport', 'Johannesburg', 'South Africa', '2025-05-24 02:00:00', '2025-05-24 10:30:00', 8, 'business', 920.00, '2025-05-13 20:19:20'),
(51, 'Air Canada', 'YYZ', 'Toronto Pearson International Airport', 'Toronto', 'Canada', 'LHR', 'Heathrow Airport', 'London', 'UK', '2025-05-25 21:00:00', '2025-05-26 10:00:00', 7, 'economy', 670.00, '2025-05-13 20:19:20'),
(52, 'Turkish Airlines', 'IST', 'Istanbul Airport', 'Istanbul', 'Turkey', 'LHR', 'Heathrow Airport', 'London', 'UK', '2025-05-26 14:00:00', '2025-05-26 17:30:00', 4, 'economy', 410.00, '2025-05-13 20:19:20'),
(53, 'Air New Zealand', 'AKL', 'Auckland Airport', 'Auckland', 'New Zealand', 'SFO', 'San Francisco International Airport', 'San Francisco', 'USA', '2025-05-27 10:00:00', '2025-05-27 21:00:00', 12, 'business', 1450.00, '2025-05-13 20:19:20'),
(54, 'Finnair', 'HEL', 'Helsinki Airport', 'Helsinki', 'Finland', 'DXB', 'Dubai International Airport', 'Dubai', 'UAE', '2025-05-28 15:00:00', '2025-05-28 23:30:00', 7, 'economy', 590.00, '2025-05-13 20:19:20'),
(55, 'Vietnam Airlines', 'SGN', 'Tan Son Nhat International Airport', 'Ho Chi Minh City', 'Vietnam', 'ICN', 'Incheon International Airport', 'Seoul', 'South Korea', '2025-05-29 06:00:00', '2025-05-29 11:30:00', 5, 'economy', 410.00, '2025-05-13 20:19:20'),
(56, 'Qantas', 'SYD', 'Sydney Airport', 'Sydney', 'Australia', 'JFK', 'John F. Kennedy International Airport', 'New York', 'USA', '2025-05-30 08:00:00', '2025-05-30 22:00:00', 16, 'business', 1350.00, '2025-05-13 20:19:20'),
(57, 'Delta', 'JFK', 'John F. Kennedy', 'New York', 'USA', 'JNB', 'OR Tambo', 'Johannesburg', 'South Africa', '2025-08-19 04:11:00', '2025-08-19 15:41:00', 690, 'economy', 2557.83, '2025-05-13 14:11:27'),
(58, 'United', 'JNB', 'OR Tambo', 'Johannesburg', 'South Africa', 'LHR', 'Heathrow', 'London', 'UK', '2025-06-08 16:19:00', '2025-06-09 02:19:00', 600, 'business', 1019.36, '2025-05-13 14:11:27'),
(59, 'Singapore Airlines', 'LHR', 'Heathrow', 'London', 'UK', 'DEL', 'Indira Gandhi Intl', 'Delhi', 'India', '2025-07-27 13:06:00', '2025-07-28 00:26:00', 680, 'business', 524.62, '2025-05-13 14:11:27'),
(60, 'Lufthansa', 'LHR', 'Heathrow', 'London', 'UK', 'JFK', 'John F. Kennedy', 'New York', 'USA', '2025-06-05 06:20:00', '2025-06-05 20:06:00', 826, 'economy', 2243.19, '2025-05-13 14:11:27'),
(61, 'Turkish Airlines', 'HND', 'Haneda', 'Tokyo', 'Japan', 'IST', 'Istanbul Airport', 'Istanbul', 'Turkey', '2025-05-10 21:29:00', '2025-05-11 10:47:00', 798, 'economy', 2307.64, '2025-05-13 14:11:27'),
(62, 'United', 'DXB', 'Dubai Intl', 'Dubai', 'UAE', 'LHR', 'Heathrow', 'London', 'UK', '2025-07-24 21:09:00', '2025-07-25 04:12:00', 423, 'business', 1779.46, '2025-05-13 14:11:27'),
(63, 'Lufthansa', 'DXB', 'Dubai Intl', 'Dubai', 'UAE', 'IST', 'Istanbul Airport', 'Istanbul', 'Turkey', '2025-08-03 11:21:00', '2025-08-03 16:29:00', 308, 'economy', 775.51, '2025-05-13 14:11:27'),
(64, 'British Airways', 'JFK', 'John F. Kennedy', 'New York', 'USA', 'JNB', 'OR Tambo', 'Johannesburg', 'South Africa', '2025-05-04 05:18:00', '2025-05-04 13:34:00', 496, 'economy', 2730.56, '2025-05-13 14:11:27'),
(65, 'Turkish Airlines', 'JNB', 'OR Tambo', 'Johannesburg', 'South Africa', 'SIN', 'Changi', 'Singapore', 'Singapore', '2025-07-16 23:04:00', '2025-07-17 07:28:00', 504, 'economy', 1451.45, '2025-05-13 14:11:27'),
(66, 'Emirates', 'SIN', 'Changi', 'Singapore', 'Singapore', 'CDG', 'Charles de Gaulle', 'Paris', 'France', '2025-06-17 01:37:00', '2025-06-17 12:30:00', 653, 'economy', 513.64, '2025-05-13 14:11:27'),
(67, 'American Airlines', 'LHR', 'Heathrow', 'London', 'UK', 'SIN', 'Changi', 'Singapore', 'Singapore', '2025-05-02 20:35:00', '2025-05-03 03:51:00', 436, 'economy', 1809.91, '2025-05-13 14:11:27'),
(68, 'Air France', 'LHR', 'Heathrow', 'London', 'UK', 'IST', 'Istanbul Airport', 'Istanbul', 'Turkey', '2025-05-20 01:10:00', '2025-05-20 14:06:00', 776, 'economy', 1728.10, '2025-05-13 14:11:27'),
(69, 'United', 'LHR', 'Heathrow', 'London', 'UK', 'IST', 'Istanbul Airport', 'Istanbul', 'Turkey', '2025-07-16 15:05:00', '2025-07-17 05:00:00', 835, 'economy', 1740.42, '2025-05-13 14:11:27'),
(70, 'British Airways', 'JNB', 'OR Tambo', 'Johannesburg', 'South Africa', 'LHR', 'Heathrow', 'London', 'UK', '2025-06-21 22:29:00', '2025-06-22 09:59:00', 690, 'economy', 1729.18, '2025-05-13 14:11:27'),
(71, 'United', 'CDG', 'Charles de Gaulle', 'Paris', 'France', 'DEL', 'Indira Gandhi Intl', 'Delhi', 'India', '2025-06-11 00:28:00', '2025-06-11 10:16:00', 588, 'business', 1498.12, '2025-05-13 14:11:27'),
(72, 'United', 'JNB', 'OR Tambo', 'Johannesburg', 'South Africa', 'LHR', 'Heathrow', 'London', 'UK', '2025-06-27 01:22:00', '2025-06-27 07:23:00', 361, 'economy', 2824.82, '2025-05-13 14:11:27'),
(73, 'Qatar Airways', 'SIN', 'Changi', 'Singapore', 'Singapore', 'JNB', 'OR Tambo', 'Johannesburg', 'South Africa', '2025-08-25 03:29:00', '2025-08-25 13:41:00', 612, 'business', 2644.94, '2025-05-13 14:11:27'),
(74, 'United', 'DEL', 'Indira Gandhi Intl', 'Delhi', 'India', 'DXB', 'Dubai Intl', 'Dubai', 'UAE', '2025-06-27 13:29:00', '2025-06-28 04:15:00', 886, 'business', 2792.47, '2025-05-13 14:11:27'),
(75, 'British Airways', 'HND', 'Haneda', 'Tokyo', 'Japan', 'LHR', 'Heathrow', 'London', 'UK', '2025-05-01 07:03:00', '2025-05-01 12:29:00', 326, 'economy', 2460.57, '2025-05-13 14:11:27'),
(76, 'Turkish Airlines', 'SYD', 'Sydney Kingsford Smith', 'Sydney', 'Australia', 'JNB', 'OR Tambo', 'Johannesburg', 'South Africa', '2025-08-20 02:18:00', '2025-08-20 14:06:00', 708, 'economy', 938.59, '2025-05-13 14:11:27'),
(77, 'United', 'SYD', 'Sydney Kingsford Smith', 'Sydney', 'Australia', 'JNB', 'OR Tambo', 'Johannesburg', 'South Africa', '2025-05-02 11:21:00', '2025-05-02 18:21:00', 420, 'business', 593.94, '2025-05-13 14:11:27'),
(78, 'Delta', 'IST', 'Istanbul Airport', 'Istanbul', 'Turkey', 'DEL', 'Indira Gandhi Intl', 'Delhi', 'India', '2025-08-07 08:27:00', '2025-08-07 22:34:00', 847, 'economy', 1557.04, '2025-05-13 14:11:27'),
(79, 'United', 'JFK', 'John F. Kennedy', 'New York', 'USA', 'HND', 'Haneda', 'Tokyo', 'Japan', '2025-08-03 16:21:00', '2025-08-04 02:05:00', 584, 'business', 1597.31, '2025-05-13 14:11:27'),
(80, 'Turkish Airlines', 'JNB', 'OR Tambo', 'Johannesburg', 'South Africa', 'SYD', 'Sydney Kingsford Smith', 'Sydney', 'Australia', '2025-06-28 12:11:00', '2025-06-28 19:14:00', 423, 'economy', 1391.74, '2025-05-13 14:11:27'),
(81, 'Air France', 'HND', 'Haneda', 'Tokyo', 'Japan', 'CDG', 'Charles de Gaulle', 'Paris', 'France', '2025-07-08 01:30:00', '2025-07-08 16:23:00', 893, 'business', 1203.95, '2025-05-13 14:11:27'),
(82, 'Air France', 'SYD', 'Sydney Kingsford Smith', 'Sydney', 'Australia', 'JNB', 'OR Tambo', 'Johannesburg', 'South Africa', '2025-07-10 09:03:00', '2025-07-10 18:59:00', 596, 'business', 1703.04, '2025-05-13 14:11:27'),
(83, 'Air France', 'CDG', 'Charles de Gaulle', 'Paris', 'France', 'DXB', 'Dubai Intl', 'Dubai', 'UAE', '2025-05-11 18:26:00', '2025-05-12 01:55:00', 449, 'economy', 1039.66, '2025-05-13 14:11:27'),
(84, 'Emirates', 'SYD', 'Sydney Kingsford Smith', 'Sydney', 'Australia', 'CDG', 'Charles de Gaulle', 'Paris', 'France', '2025-06-11 16:10:00', '2025-06-12 06:15:00', 845, 'economy', 1267.88, '2025-05-13 14:11:27'),
(85, 'Air France', 'JNB', 'OR Tambo', 'Johannesburg', 'South Africa', 'IST', 'Istanbul Airport', 'Istanbul', 'Turkey', '2025-05-03 14:08:00', '2025-05-03 23:11:00', 543, 'business', 711.27, '2025-05-13 14:11:27'),
(86, 'Delta', 'JNB', 'OR Tambo', 'Johannesburg', 'South Africa', 'SIN', 'Changi', 'Singapore', 'Singapore', '2025-06-24 03:02:00', '2025-06-24 17:28:00', 866, 'economy', 1001.75, '2025-05-13 14:11:27'),
(87, 'Air France', 'JFK', 'John F. Kennedy', 'New York', 'USA', 'IST', 'Istanbul Airport', 'Istanbul', 'Turkey', '2025-07-23 05:48:00', '2025-07-23 17:21:00', 693, 'economy', 1311.14, '2025-05-13 14:11:27'),
(88, 'Turkish Airlines', 'SIN', 'Changi', 'Singapore', 'Singapore', 'HND', 'Haneda', 'Tokyo', 'Japan', '2025-06-10 03:51:00', '2025-06-10 14:08:00', 617, 'economy', 1316.16, '2025-05-13 14:11:27'),
(89, 'American Airlines', 'SIN', 'Changi', 'Singapore', 'Singapore', 'CDG', 'Charles de Gaulle', 'Paris', 'France', '2025-05-27 04:41:00', '2025-05-27 19:31:00', 890, 'economy', 2193.08, '2025-05-13 14:11:27'),
(90, 'Qatar Airways', 'DEL', 'Indira Gandhi Intl', 'Delhi', 'India', 'JFK', 'John F. Kennedy', 'New York', 'USA', '2025-05-14 10:45:00', '2025-05-14 17:03:00', 378, 'economy', 972.04, '2025-05-13 14:11:27'),
(91, 'Turkish Airlines', 'IST', 'Istanbul Airport', 'Istanbul', 'Turkey', 'SYD', 'Sydney Kingsford Smith', 'Sydney', 'Australia', '2025-06-26 10:14:00', '2025-06-26 16:39:00', 385, 'economy', 2137.53, '2025-05-13 14:11:27'),
(92, 'Singapore Airlines', 'DXB', 'Dubai Intl', 'Dubai', 'UAE', 'HND', 'Haneda', 'Tokyo', 'Japan', '2025-07-22 02:06:00', '2025-07-22 12:41:00', 635, 'economy', 2268.27, '2025-05-13 14:11:27'),
(93, 'American Airlines', 'SIN', 'Changi', 'Singapore', 'Singapore', 'JFK', 'John F. Kennedy', 'New York', 'USA', '2025-08-08 18:41:00', '2025-08-09 06:40:00', 719, 'business', 816.22, '2025-05-13 14:11:27'),
(94, 'United', 'LHR', 'Heathrow', 'London', 'UK', 'SYD', 'Sydney Kingsford Smith', 'Sydney', 'Australia', '2025-08-12 04:40:00', '2025-08-12 13:33:00', 533, 'economy', 1558.62, '2025-05-13 14:11:27'),
(95, 'Qatar Airways', 'JNB', 'OR Tambo', 'Johannesburg', 'South Africa', 'HND', 'Haneda', 'Tokyo', 'Japan', '2025-05-05 17:56:00', '2025-05-06 07:31:00', 815, 'economy', 1260.24, '2025-05-13 14:11:27'),
(96, 'United', 'JFK', 'John F. Kennedy', 'New York', 'USA', 'IST', 'Istanbul Airport', 'Istanbul', 'Turkey', '2025-05-13 20:58:00', '2025-05-14 06:31:00', 573, 'business', 901.50, '2025-05-13 14:11:27'),
(97, 'British Airways', 'IST', 'Istanbul Airport', 'Istanbul', 'Turkey', 'JFK', 'John F. Kennedy', 'New York', 'USA', '2025-07-17 21:44:00', '2025-07-18 03:18:00', 334, 'economy', 2231.35, '2025-05-13 14:11:27'),
(98, 'Qatar Airways', 'SIN', 'Changi', 'Singapore', 'Singapore', 'LHR', 'Heathrow', 'London', 'UK', '2025-06-24 23:05:00', '2025-06-25 08:45:00', 580, 'business', 2262.96, '2025-05-13 14:11:27'),
(99, 'Turkish Airlines', 'CDG', 'Charles de Gaulle', 'Paris', 'France', 'JNB', 'OR Tambo', 'Johannesburg', 'South Africa', '2025-07-01 00:41:00', '2025-07-01 07:43:00', 422, 'economy', 1028.43, '2025-05-13 14:11:27'),
(100, 'United', 'DEL', 'Indira Gandhi Intl', 'Delhi', 'India', 'DXB', 'Dubai Intl', 'Dubai', 'UAE', '2025-07-04 17:07:00', '2025-07-05 06:47:00', 820, 'business', 2738.27, '2025-05-13 14:11:27'),
(101, 'Singapore Airlines', 'SYD', 'Sydney Kingsford Smith', 'Sydney', 'Australia', 'IST', 'Istanbul Airport', 'Istanbul', 'Turkey', '2025-06-12 08:25:00', '2025-06-12 17:56:00', 571, 'business', 2006.25, '2025-05-13 14:11:27'),
(102, 'American Airlines', 'JNB', 'OR Tambo', 'Johannesburg', 'South Africa', 'DXB', 'Dubai Intl', 'Dubai', 'UAE', '2025-06-10 08:24:00', '2025-06-10 17:40:00', 556, 'economy', 2915.47, '2025-05-13 14:11:27'),
(103, 'Turkish Airlines', 'DXB', 'Dubai Intl', 'Dubai', 'UAE', 'IST', 'Istanbul Airport', 'Istanbul', 'Turkey', '2025-08-15 17:22:00', '2025-08-16 02:07:00', 525, 'business', 1925.35, '2025-05-13 14:11:27'),
(104, 'Lufthansa', 'HND', 'Haneda', 'Tokyo', 'Japan', 'LHR', 'Heathrow', 'London', 'UK', '2025-05-15 14:43:00', '2025-05-16 03:56:00', 793, 'economy', 2544.17, '2025-05-13 14:11:27'),
(105, 'Emirates', 'DXB', 'Dubai Intl', 'Dubai', 'UAE', 'CDG', 'Charles de Gaulle', 'Paris', 'France', '2025-06-23 21:27:00', '2025-06-24 12:10:00', 883, 'business', 1269.69, '2025-05-13 14:11:27'),
(106, 'Turkish Airlines', 'DEL', 'Indira Gandhi Intl', 'Delhi', 'India', 'JFK', 'John F. Kennedy', 'New York', 'USA', '2025-08-24 02:56:00', '2025-08-24 13:03:00', 607, 'business', 1943.14, '2025-05-13 14:11:27'),
(107, 'Delta', 'DEL', 'Indira Gandhi Intl', 'Delhi', 'India', 'HND', 'Haneda', 'Tokyo', 'Japan', '2025-08-01 14:53:00', '2025-08-02 01:58:00', 665, 'economy', 2612.01, '2025-05-13 14:11:27'),
(108, 'Emirates', 'IST', 'Istanbul Airport', 'Istanbul', 'Turkey', 'JFK', 'John F. Kennedy', 'New York', 'USA', '2025-07-27 10:37:00', '2025-07-27 18:45:00', 488, 'business', 1959.71, '2025-05-13 14:11:27'),
(109, 'United', 'JFK', 'John F. Kennedy', 'New York', 'USA', 'JNB', 'OR Tambo', 'Johannesburg', 'South Africa', '2025-08-15 23:08:00', '2025-08-16 06:14:00', 426, 'economy', 2716.00, '2025-05-13 14:11:27'),
(110, 'Qatar Airways', 'SYD', 'Sydney Kingsford Smith', 'Sydney', 'Australia', 'IST', 'Istanbul Airport', 'Istanbul', 'Turkey', '2025-06-02 11:58:00', '2025-06-03 01:42:00', 824, 'economy', 644.36, '2025-05-13 14:11:27'),
(111, 'Singapore Airlines', 'LHR', 'Heathrow', 'London', 'UK', 'JNB', 'OR Tambo', 'Johannesburg', 'South Africa', '2025-08-10 23:29:00', '2025-08-11 14:24:00', 895, 'economy', 1975.29, '2025-05-13 14:11:27'),
(112, 'Lufthansa', 'DEL', 'Indira Gandhi Intl', 'Delhi', 'India', 'DXB', 'Dubai Intl', 'Dubai', 'UAE', '2025-06-16 05:49:00', '2025-06-16 16:43:00', 654, 'business', 2401.50, '2025-05-13 14:11:27'),
(113, 'United', 'JFK', 'John F. Kennedy', 'New York', 'USA', 'LHR', 'Heathrow', 'London', 'UK', '2025-07-19 23:51:00', '2025-07-20 11:11:00', 680, 'economy', 1508.48, '2025-05-13 14:11:27'),
(114, 'Delta', 'LHR', 'Heathrow', 'London', 'UK', 'JNB', 'OR Tambo', 'Johannesburg', 'South Africa', '2025-07-02 17:07:00', '2025-07-03 00:28:00', 441, 'business', 1526.52, '2025-05-13 14:11:27'),
(115, 'American Airlines', 'SYD', 'Sydney Kingsford Smith', 'Sydney', 'Australia', 'DXB', 'Dubai Intl', 'Dubai', 'UAE', '2025-05-12 01:04:00', '2025-05-12 08:48:00', 464, 'economy', 1471.65, '2025-05-13 14:11:27'),
(116, 'British Airways', 'JNB', 'OR Tambo', 'Johannesburg', 'South Africa', 'JFK', 'John F. Kennedy', 'New York', 'USA', '2025-07-23 02:59:00', '2025-07-23 14:46:00', 707, 'business', 2689.48, '2025-05-13 14:11:27'),
(117, 'Turkish Airlines', 'SYD', 'Sydney Kingsford Smith', 'Sydney', 'Australia', 'JFK', 'John F. Kennedy', 'New York', 'USA', '2025-08-21 09:09:00', '2025-08-21 18:36:00', 567, 'business', 2292.92, '2025-05-13 14:11:27'),
(118, 'Air France', 'LHR', 'Heathrow', 'London', 'UK', 'CDG', 'Charles de Gaulle', 'Paris', 'France', '2025-08-01 20:55:00', '2025-08-02 08:03:00', 668, 'economy', 790.67, '2025-05-13 14:11:27'),
(119, 'Qatar Airways', 'LHR', 'Heathrow', 'London', 'UK', 'HND', 'Haneda', 'Tokyo', 'Japan', '2025-06-13 11:21:00', '2025-06-13 22:54:00', 693, 'economy', 2473.44, '2025-05-13 14:11:27'),
(120, 'Singapore Airlines', 'IST', 'Istanbul Airport', 'Istanbul', 'Turkey', 'JNB', 'OR Tambo', 'Johannesburg', 'South Africa', '2025-06-28 20:50:00', '2025-06-29 05:08:00', 498, 'business', 1959.80, '2025-05-13 14:11:27'),
(121, 'Lufthansa', 'IST', 'Istanbul Airport', 'Istanbul', 'Turkey', 'DXB', 'Dubai Intl', 'Dubai', 'UAE', '2025-05-16 08:25:00', '2025-05-16 14:25:00', 360, 'economy', 1458.77, '2025-05-13 14:11:27'),
(122, 'Delta', 'SYD', 'Sydney Kingsford Smith', 'Sydney', 'Australia', 'JNB', 'OR Tambo', 'Johannesburg', 'South Africa', '2025-08-21 07:12:00', '2025-08-21 12:29:00', 317, 'business', 1639.73, '2025-05-13 14:11:27'),
(123, 'Air France', 'JNB', 'OR Tambo', 'Johannesburg', 'South Africa', 'SYD', 'Sydney Kingsford Smith', 'Sydney', 'Australia', '2025-05-18 02:49:00', '2025-05-18 13:22:00', 633, 'economy', 1822.38, '2025-05-13 14:11:27'),
(124, 'Emirates', 'SYD', 'Sydney Kingsford Smith', 'Sydney', 'Australia', 'CDG', 'Charles de Gaulle', 'Paris', 'France', '2025-06-07 09:45:00', '2025-06-07 22:42:00', 777, 'business', 2968.09, '2025-05-13 14:11:27'),
(125, 'American Airlines', 'SIN', 'Changi', 'Singapore', 'Singapore', 'IST', 'Istanbul Airport', 'Istanbul', 'Turkey', '2025-05-26 07:41:00', '2025-05-26 13:52:00', 371, 'business', 977.24, '2025-05-13 14:11:27'),
(126, 'Turkish Airlines', 'DXB', 'Dubai Intl', 'Dubai', 'UAE', 'DEL', 'Indira Gandhi Intl', 'Delhi', 'India', '2025-05-12 13:58:00', '2025-05-13 01:10:00', 672, 'economy', 2773.58, '2025-05-13 14:11:27'),
(127, 'Turkish Airlines', 'DXB', 'Dubai Intl', 'Dubai', 'UAE', 'SIN', 'Changi', 'Singapore', 'Singapore', '2025-07-14 14:41:00', '2025-07-15 02:19:00', 698, 'business', 968.68, '2025-05-13 14:11:27'),
(128, 'Qatar Airways', 'IST', 'Istanbul Airport', 'Istanbul', 'Turkey', 'DXB', 'Dubai Intl', 'Dubai', 'UAE', '2025-08-16 02:04:00', '2025-08-16 09:26:00', 442, 'business', 2074.15, '2025-05-13 14:11:27'),
(129, 'American Airlines', 'JFK', 'John F. Kennedy', 'New York', 'USA', 'JNB', 'OR Tambo', 'Johannesburg', 'South Africa', '2025-06-16 15:30:00', '2025-06-16 21:50:00', 380, 'business', 2463.60, '2025-05-13 14:11:27'),
(130, 'Air France', 'SIN', 'Changi', 'Singapore', 'Singapore', 'LHR', 'Heathrow', 'London', 'UK', '2025-05-08 00:45:00', '2025-05-08 14:45:00', 840, 'economy', 2427.72, '2025-05-13 14:11:27'),
(131, 'Emirates', 'DXB', 'Dubai Intl', 'Dubai', 'UAE', 'SYD', 'Sydney Kingsford Smith', 'Sydney', 'Australia', '2025-07-03 22:59:00', '2025-07-04 13:22:00', 863, 'business', 976.95, '2025-05-13 14:11:27'),
(132, 'Lufthansa', 'IST', 'Istanbul Airport', 'Istanbul', 'Turkey', 'LHR', 'Heathrow', 'London', 'UK', '2025-08-24 00:39:00', '2025-08-24 13:41:00', 782, 'economy', 2478.54, '2025-05-13 14:11:27'),
(133, 'Delta', 'SYD', 'Sydney Kingsford Smith', 'Sydney', 'Australia', 'SIN', 'Changi', 'Singapore', 'Singapore', '2025-06-19 13:14:00', '2025-06-19 18:41:00', 327, 'economy', 2276.64, '2025-05-13 14:11:27'),
(134, 'American Airlines', 'IST', 'Istanbul Airport', 'Istanbul', 'Turkey', 'DEL', 'Indira Gandhi Intl', 'Delhi', 'India', '2025-06-27 06:46:00', '2025-06-27 18:12:00', 686, 'economy', 904.01, '2025-05-13 14:11:27'),
(135, 'British Airways', 'DXB', 'Dubai Intl', 'Dubai', 'UAE', 'CDG', 'Charles de Gaulle', 'Paris', 'France', '2025-06-07 17:47:00', '2025-06-08 07:16:00', 809, 'economy', 2396.09, '2025-05-13 14:11:27'),
(136, 'Turkish Airlines', 'DXB', 'Dubai Intl', 'Dubai', 'UAE', 'SIN', 'Changi', 'Singapore', 'Singapore', '2025-08-19 06:42:00', '2025-08-19 18:39:00', 717, 'economy', 2734.96, '2025-05-13 14:11:27'),
(137, 'British Airways', 'HND', 'Haneda', 'Tokyo', 'Japan', 'SYD', 'Sydney Kingsford Smith', 'Sydney', 'Australia', '2025-06-16 10:54:00', '2025-06-17 00:06:00', 792, 'economy', 2587.04, '2025-05-13 14:11:27'),
(138, 'Qatar Airways', 'HND', 'Haneda', 'Tokyo', 'Japan', 'DXB', 'Dubai Intl', 'Dubai', 'UAE', '2025-05-07 10:19:00', '2025-05-07 21:25:00', 666, 'business', 2857.79, '2025-05-13 14:11:27'),
(139, 'Qatar Airways', 'JFK', 'John F. Kennedy', 'New York', 'USA', 'DEL', 'Indira Gandhi Intl', 'Delhi', 'India', '2025-07-28 06:48:00', '2025-07-28 12:46:00', 358, 'business', 1820.89, '2025-05-13 14:11:27'),
(140, 'Air France', 'IST', 'Istanbul Airport', 'Istanbul', 'Turkey', 'DXB', 'Dubai Intl', 'Dubai', 'UAE', '2025-06-26 11:38:00', '2025-06-26 21:11:00', 573, 'economy', 873.37, '2025-05-13 14:11:27'),
(141, 'Emirates', 'DEL', 'Indira Gandhi Intl', 'Delhi', 'India', 'IST', 'Istanbul Airport', 'Istanbul', 'Turkey', '2025-08-13 09:21:00', '2025-08-13 21:39:00', 738, 'business', 2919.40, '2025-05-13 14:11:27'),
(142, 'American Airlines', 'SIN', 'Changi', 'Singapore', 'Singapore', 'CDG', 'Charles de Gaulle', 'Paris', 'France', '2025-06-17 05:11:00', '2025-06-17 19:08:00', 837, 'business', 857.27, '2025-05-13 14:11:27'),
(143, 'Singapore Airlines', 'DXB', 'Dubai Intl', 'Dubai', 'UAE', 'LHR', 'Heathrow', 'London', 'UK', '2025-08-04 12:22:00', '2025-08-05 01:03:00', 761, 'economy', 917.48, '2025-05-13 14:11:27'),
(144, 'Turkish Airlines', 'HND', 'Haneda', 'Tokyo', 'Japan', 'IST', 'Istanbul Airport', 'Istanbul', 'Turkey', '2025-07-03 09:05:00', '2025-07-03 21:26:00', 741, 'economy', 2699.65, '2025-05-13 14:11:27'),
(145, 'British Airways', 'JFK', 'John F. Kennedy', 'New York', 'USA', 'SIN', 'Changi', 'Singapore', 'Singapore', '2025-05-27 01:45:00', '2025-05-27 13:50:00', 725, 'business', 839.59, '2025-05-13 14:11:27'),
(146, 'American Airlines', 'JFK', 'John F. Kennedy', 'New York', 'USA', 'DEL', 'Indira Gandhi Intl', 'Delhi', 'India', '2025-05-18 03:55:00', '2025-05-18 09:05:00', 310, 'business', 1147.60, '2025-05-13 14:11:27'),
(147, 'Delta', 'HND', 'Haneda', 'Tokyo', 'Japan', 'DXB', 'Dubai Intl', 'Dubai', 'UAE', '2025-05-10 20:51:00', '2025-05-11 03:53:00', 422, 'business', 2695.95, '2025-05-13 14:11:27'),
(148, 'Qatar Airways', 'JNB', 'OR Tambo', 'Johannesburg', 'South Africa', 'CDG', 'Charles de Gaulle', 'Paris', 'France', '2025-07-03 16:27:00', '2025-07-03 23:59:00', 452, 'business', 2121.58, '2025-05-13 14:11:27'),
(149, 'United', 'IST', 'Istanbul Airport', 'Istanbul', 'Turkey', 'DEL', 'Indira Gandhi Intl', 'Delhi', 'India', '2025-08-05 15:42:00', '2025-08-06 02:37:00', 655, 'economy', 2009.54, '2025-05-13 14:11:27'),
(150, 'Qatar Airways', 'SIN', 'Changi', 'Singapore', 'Singapore', 'HND', 'Haneda', 'Tokyo', 'Japan', '2025-06-03 01:52:00', '2025-06-03 07:39:00', 347, 'business', 1572.47, '2025-05-13 14:11:27'),
(151, 'United', 'SIN', 'Changi', 'Singapore', 'Singapore', 'HND', 'Haneda', 'Tokyo', 'Japan', '2025-05-06 00:56:00', '2025-05-06 14:54:00', 838, 'economy', 962.81, '2025-05-13 14:11:27'),
(152, 'Delta', 'DEL', 'Indira Gandhi Intl', 'Delhi', 'India', 'SIN', 'Changi', 'Singapore', 'Singapore', '2025-06-02 11:57:00', '2025-06-03 01:41:00', 824, 'economy', 1150.22, '2025-05-13 14:11:27'),
(153, 'Singapore Airlines', 'SIN', 'Changi', 'Singapore', 'Singapore', 'DXB', 'Dubai Intl', 'Dubai', 'UAE', '2025-08-11 00:13:00', '2025-08-11 10:27:00', 614, 'business', 1921.48, '2025-05-13 14:11:27'),
(154, 'Delta', 'CDG', 'Charles de Gaulle', 'Paris', 'France', 'SYD', 'Sydney Kingsford Smith', 'Sydney', 'Australia', '2025-05-20 21:05:00', '2025-05-21 06:51:00', 586, 'economy', 2766.04, '2025-05-13 14:11:27'),
(155, 'Qatar Airways', 'LHR', 'Heathrow', 'London', 'UK', 'CDG', 'Charles de Gaulle', 'Paris', 'France', '2025-07-07 23:58:00', '2025-07-08 14:58:00', 900, 'economy', 1531.66, '2025-05-13 14:11:27'),
(156, 'Turkish Airlines', 'HND', 'Haneda', 'Tokyo', 'Japan', 'SIN', 'Changi', 'Singapore', 'Singapore', '2025-08-28 20:12:00', '2025-08-29 02:12:00', 360, 'business', 2924.21, '2025-05-13 14:11:27');

-- --------------------------------------------------------

--
-- Table structure for table `hotels`
--

CREATE TABLE `hotels` (
  `Hotel_ID` int(11) NOT NULL,
  `Location` varchar(100) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Stars` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`Hotel_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotels`
--

INSERT INTO `hotels` (`Hotel_ID`, `Location`, `Name`, `Stars`) VALUES
(101, 'Paris, France', 'Hotel Lumière', '5'),
(102, 'Paris, France', 'Eiffel Stay Inn', '4'),
(103, 'Paris, France', 'Seine View Hotel', '3'),
(107, 'New York, USA', 'Empire Grand Hotel', '5'),
(108, 'New York, USA', 'Central Park Stay', '4'),
(109, 'New York, USA', 'Hudson Budget Hotel', '3'),
(128, 'Cairo, Egypt', 'Pyramid View Resort', '5'),
(129, 'Cairo, Egypt', 'Nile Budget Lodge', '3'),
(131, 'California, USA', 'Golden Gate View Inn', '4'),
(135, 'Niagara, Canada', 'Fallsview Grand Hotel', '5'),
(136, 'Niagara, Canada', 'Rainbow Inn', '3'),
(137, 'Niagara, Canada', 'Horseshoe Retreat', '4'),
(138, 'Niagara, Canada', 'Budget Breeze Motel', '2'),
(147, 'Sydney, Australia', 'Opera House Hotel', '5'),
(148, 'Sydney, Australia', 'Harbour View Inn', '4'),
(149, 'Sydney, Australia', 'Bondi Beach Motel', '3'),
(150, 'Sydney, Australia', 'Koala Budget Lodge', '4'),
(151, 'Colombo, Sri Lanka', 'Ocean Breeze Hotel Colombo', '4'),
(152, 'Jaipur, Rajasthan, India', 'Royal Palace Hotel', '5'),
(153, 'Srinagar, Kashmir, India', 'Heavenly Peaks Resort', '4'),
(154, 'Islamabad, Pakistan', 'Margalla View Hotel', '4'),
(155, 'Istanbul, Turkey', 'Blue Mosque Boutique Hotel', '5'),
(156, 'Cappadocia, Turkey', 'Fairy Chimney Cave Inn', '4'),
(157, 'Miami, Florida, USA', 'Sunset Beachfront Hotel', '4'),
(158, 'Seattle, Washington, USA', 'Emerald City View Inn', '5'),
(159, 'Melbourne, Victoria, Australia', 'Yarra River Grand Hotel', '4'),
(160, 'Brisbane, Queensland, Australia', 'River City Comfort Inn', '3'),
(213, 'London, UK', 'Westminster Luxury Hotel', '5'),
(214, 'London, UK', 'Thames Budget Stay', '3'),
(322, 'Bangkok, Thailand', 'Siam Royal Hotel', '5'),
(323, 'Bangkok, Thailand', 'Chao Phraya Riverside Inn', '4'),
(324, 'Bangkok, Thailand', 'Backpacker Budget Stay', '2');

-- --------------------------------------------------------

--
-- Table structure for table `offers`
--

CREATE TABLE `offers` (
  `C_Car_ID` int(11) NOT NULL,
  `H_Hotel_ID` int(11) NOT NULL,
  `Rental_fee` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`C_Car_ID`, `H_Hotel_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `offers`
--

INSERT INTO `offers` (`C_Car_ID`, `H_Hotel_ID`, `Rental_fee`) VALUES
(90001, 109, 45.99),
(90001, 128, 49.95),
(90001, 129, 52.50),
(90001, 131, 48.75),
(90001, 147, 46.99),
(90001, 150, 54.95),
(90002, 129, 42.75),
(90002, 131, 47.50),
(90002, 147, 44.99),
(90002, 151, 46.50),
(90002, 154, 49.95),
(90002, 155, 45.75),
(90003, 128, 69.95),
(90003, 129, 72.50),
(90003, 131, 74.99),
(90003, 147, 68.75),
(90003, 150, 75.95),
(90003, 151, 71.50),
(90004, 109, 62.99),
(90004, 129, 65.50),
(90004, 150, 59.95),
(90004, 151, 67.50),
(90004, 156, 64.75),
(90005, 128, 78.50),
(90005, 131, 82.99),
(90005, 147, 79.95),
(90005, 150, 84.75),
(90005, 151, 77.50),
(90005, 154, 81.99),
(90005, 155, 83.50),
(90006, 109, 56.75),
(90006, 128, 53.99),
(90006, 129, 57.50),
(90006, 131, 54.95),
(90006, 150, 59.99),
(90006, 151, 55.75),
(90006, 154, 58.50),
(90006, 155, 52.99),
(90006, 156, 56.50),
(90007, 109, 87.99),
(90007, 128, 85.50),
(90007, 129, 89.95),
(90007, 147, 84.75),
(90007, 150, 88.99),
(90007, 151, 86.50),
(90007, 154, 90.99),
(90007, 155, 89.50),
(90007, 156, 87.75),
(90008, 109, 57.99),
(90008, 131, 54.50),
(90008, 147, 59.95),
(90008, 150, 56.75),
(90008, 151, 58.50),
(90008, 154, 55.99),
(90008, 156, 61.50),
(90009, 109, 64.99),
(90009, 128, 67.50),
(90009, 129, 66.75),
(90009, 147, 69.95),
(90009, 151, 65.50),
(90009, 154, 68.99),
(90009, 155, 63.75),
(90009, 156, 67.95),
(90010, 109, 72.50),
(90010, 128, 69.99),
(90010, 131, 74.95),
(90010, 155, 71.50),
(90010, 156, 76.99);

-- --------------------------------------------------------

--
-- Table structure for table `rental_cars`
--

CREATE TABLE `rental_cars` (
  `Car_ID` int(11) NOT NULL,
  `Model` varchar(50) DEFAULT NULL,
  `Capacity` int(11) DEFAULT NULL,
  PRIMARY KEY (`Car_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rental_cars`
--

INSERT INTO `rental_cars` (`Car_ID`, `Model`, `Capacity`) VALUES
(90001, 'Toyota Corolla', 5),
(90002, 'Honda Civic', 5),
(90003, 'Tesla Model 3', 5),
(90004, 'Ford Explorer', 7),
(90005, 'BMW X5', 5),
(90006, 'Hyundai Tucson', 5),
(90007, 'Chevrolet Suburban', 8),
(90008, 'Kia Sorento', 7),
(90009, 'Nissan Patrol', 7),
(90010, 'Mercedes-Benz V-Class', 8);

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `Room_number` varchar(10) NOT NULL,
  `Bedrooms` int(11) DEFAULT NULL,
  `Bathrooms` int(11) DEFAULT NULL,
  `Room_type` varchar(50) DEFAULT NULL,
  `Capacity` int(11) DEFAULT NULL,
  `H_Hotel_ID` int(11) NOT NULL,
  `cost_night` int(11) DEFAULT NULL,
  PRIMARY KEY (`Room_number`, `H_Hotel_ID`),
  KEY `H_Hotel_ID` (`H_Hotel_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`Room_number`, `Bedrooms`, `Bathrooms`, `Room_type`, `Capacity`, `H_Hotel_ID`, `cost_night`) VALUES
('3001', 1, 1, 'Executive', 2, 101, 250),
('3002', 3, 1, 'Deluxe', 6, 101, 450),
('3003', 3, 1, 'Suite', 4, 101, 600),
('3004', 1, 1, 'Executive', 6, 102, 200),
('3005', 1, 1, 'Executive', 3, 102, 180),
('3006', 1, 1, 'Standard', 4, 102, 150),
('3007', 3, 2, 'Executive', 2, 103, 220),
('3008', 2, 2, 'Deluxe', 5, 103, 280),
('3009', 3, 1, 'Deluxe', 3, 103, 260),
('3010', 3, 2, 'Deluxe', 6, 107, 500),
('3011', 1, 1, 'Executive', 5, 107, 300),
('3012', 3, 2, 'Suite', 5, 107, 650),
('3013', 1, 1, 'Executive', 5, 108, 250),
('3014', 2, 1, 'Executive', 5, 108, 280),
('3015', 3, 1, 'Executive', 3, 108, 320),
('3016', 3, 1, 'Executive', 6, 109, 200),
('3017', 3, 1, 'Executive', 2, 109, 180),
('3018', 2, 1, 'Executive', 3, 109, 160),
('3019', 3, 3, 'Deluxe', 5, 128, 450),
('3020', 2, 2, 'Suite', 3, 128, 550),
('3021', 3, 2, 'Suite', 2, 128, 500),
('3022', 3, 3, 'Deluxe', 5, 129, 280),
('3023', 2, 2, 'Deluxe', 3, 129, 220),
('3024', 1, 1, 'Deluxe', 2, 129, 180),
('3025', 3, 3, 'Suite', 5, 131, 400),
('3026', 1, 1, 'Standard', 2, 131, 180),
('3027', 2, 1, 'Executive', 6, 131, 280),
('3028', 1, 1, 'Standard', 2, 135, 200),
('3029', 2, 2, 'Standard', 6, 135, 300),
('3030', 2, 1, 'Deluxe', 2, 135, 350),
('3031', 1, 1, 'Suite', 3, 136, 220),
('3032', 3, 1, 'Standard', 5, 136, 180),
('3033', 3, 2, 'Executive', 5, 136, 250),
('3034', 1, 1, 'Executive', 6, 137, 280),
('3035', 3, 2, 'Suite', 3, 137, 380),
('3036', 1, 1, 'Standard', 2, 137, 180),
('3037', 3, 1, 'Standard', 2, 138, 120),
('3038', 1, 1, 'Deluxe', 4, 138, 150),
('3039', 3, 3, 'Executive', 6, 138, 200),
('3040', 2, 2, 'Deluxe', 4, 147, 450),
('3041', 1, 1, 'Suite', 4, 147, 500),
('3042', 2, 2, 'Deluxe', 2, 147, 400),
('3043', 2, 1, 'Executive', 5, 148, 350),
('3044', 3, 2, 'Standard', 2, 148, 280),
('3045', 2, 2, 'Standard', 2, 148, 250),
('3046', 1, 1, 'Standard', 3, 149, 180),
('3047', 1, 1, 'Executive', 4, 149, 220),
('3048', 2, 1, 'Executive', 6, 149, 280),
('3049', 2, 1, 'Standard', 4, 150, 250),
('3050', 2, 2, 'Standard', 4, 150, 280),
('3051', 2, 1, 'Executive', 5, 150, 320),
('3052', 1, 1, 'Suite', 4, 151, 350),
('3053', 3, 2, 'Executive', 3, 151, 380),
('3054', 3, 1, 'Deluxe', 3, 151, 320),
('3055', 3, 2, 'Standard', 4, 152, 400),
('3056', 1, 1, 'Deluxe', 2, 152, 350),
('3057', 2, 2, 'Executive', 2, 152, 380),
('3058', 3, 1, 'Suite', 3, 153, 320),
('3059', 3, 2, 'Standard', 4, 153, 280),
('3060', 3, 2, 'Suite', 4, 153, 380),
('3061', 2, 1, 'Suite', 2, 154, 300),
('3062', 1, 1, 'Suite', 5, 154, 280),
('3063', 3, 2, 'Executive', 4, 154, 320),
('3064', 3, 1, 'Standard', 6, 155, 400),
('3065', 2, 1, 'Executive', 4, 155, 450),
('3066', 1, 1, 'Deluxe', 4, 155, 380),
('3067', 2, 2, 'Deluxe', 5, 156, 320),
('3068', 1, 1, 'Deluxe', 4, 156, 280),
('3069', 2, 1, 'Suite', 3, 156, 350),
('3070', 3, 3, 'Suite', 3, 157, 380),
('3071', 2, 1, 'Deluxe', 6, 157, 320),
('3072', 1, 1, 'Standard', 2, 157, 220),
('3073', 3, 2, 'Suite', 6, 158, 500),
('3074', 1, 1, 'Executive', 5, 158, 400),
('3075', 1, 1, 'Deluxe', 2, 158, 350);

-- --------------------------------------------------------

--
-- Table structure for table `tours`
--

CREATE TABLE `tours` (
  `Tour_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Tour_Name` varchar(100) NOT NULL,
  `Description` text DEFAULT NULL,
  `Destination` varchar(100) NOT NULL,
  `Duration_Days` int(11) NOT NULL,
  `H_Hotel_ID` int(11) NOT NULL,
  `Room_Type` varchar(50) NOT NULL,
  `P_Plane_ID` int(11) NOT NULL,
  `Seat_Class` varchar(30) NOT NULL,
  `Min_People` int(11) NOT NULL,
  `Max_People` int(11) NOT NULL,
  `Price_Per_Person` decimal(10,2) NOT NULL,
  `Includes_Car_Rental` tinyint(1) DEFAULT 0,
  `C_Car_ID` int(11) DEFAULT NULL,
  `Tour_Start_Date` date NOT NULL,
  PRIMARY KEY (`Tour_ID`),
  KEY `H_Hotel_ID` (`H_Hotel_ID`),
  KEY `P_Plane_ID` (`P_Plane_ID`),
  KEY `C_Car_ID` (`C_Car_ID`),
  CONSTRAINT `tours_ibfk_1` FOREIGN KEY (`H_Hotel_ID`) REFERENCES `hotels` (`Hotel_ID`),
  CONSTRAINT `tours_ibfk_2` FOREIGN KEY (`P_Plane_ID`) REFERENCES `flights` (`id`),
  CONSTRAINT `tours_ibfk_3` FOREIGN KEY (`C_Car_ID`) REFERENCES `rental_cars` (`Car_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tours`
--

INSERT INTO `tours` (`Tour_ID`, `Tour_Name`, `Description`, `Destination`, `Duration_Days`, `H_Hotel_ID`, `Room_Type`, `P_Plane_ID`, `Seat_Class`, `Min_People`, `Max_People`, `Price_Per_Person`, `Includes_Car_Rental`, `C_Car_ID`, `Tour_Start_Date`) VALUES
(1, 'Parisian Elegance', 'Experience the romantic charm of Paris with luxury accommodations and iconic sightseeing opportunities.', 'Paris, France', 7, 101, 'Executive', 100, 'Business (Window)', 2, 4, 2499.99, 0, NULL, '2025-04-29'),
(2, 'NYC Explorer', 'Discover the vibrant energy of New York City with centrally located accommodations and Broadway shows.', 'New York, USA', 5, 107, 'Suite', 103, 'Economy (Window)', 2, 6, 1899.99, 1, 90002, '2025-05-02'),
(3, 'Ancient Egypt Adventure', 'Journey through time exploring ancient pyramids and experiencing authentic Egyptian culture.', 'Cairo, Egypt', 8, 128, 'Deluxe', 105, 'Business (Middle)', 4, 10, 2299.99, 1, 90004, '2025-05-06'),
(4, 'Sydney Discovery', 'Experience the beauty of Sydney Harbor, iconic Opera House, and stunning Australian beaches.', 'Sydney, Australia', 10, 147, 'Deluxe', 111, 'Business (Far End)', 2, 8, 3199.99, 1, 90010, '2025-05-07'),
(5, 'Niagara Falls Escape', 'Be mesmerized by the majestic Niagara Falls with premium accommodations and scenic tours.', 'Niagara, Canada', 4, 135, 'Standard', 108, 'Business (Window)', 2, 6, 1699.99, 0, NULL, '2025-05-07'),
(6, 'Istanbul Heritage Tour', 'Explore the rich cultural history where East meets West in magnificent Istanbul.', 'Istanbul, Turkey', 6, 155, 'Deluxe', 123, 'Business (Window)', 2, 8, 2699.99, 1, 90001, '2025-05-02'),
(7, 'Royal Rajasthan Experience', 'Immerse yourself in the royal heritage and vibrant culture of majestic Rajasthan.', 'Jaipur, Rajasthan, India', 9, 152, 'Standard', 117, 'Business (Far End)', 4, 12, 2099.99, 1, 90008, '2025-04-28'),
(8, 'London Classic', 'Experience the timeless elegance of London with its rich history and modern attractions.', 'London, UK', 6, 213, 'Executive', 137, 'Economy (Window)', 2, 6, 2399.99, 0, NULL, '2025-05-03'),
(9, 'Miami Beach Getaway', 'Relax on pristine beaches and enjoy the vibrant nightlife of Miami, Florida.', 'Miami, Florida, USA', 5, 157, 'Suite', 129, 'Economy (Window)', 2, 4, 1899.99, 1, 90006, '2025-05-07'),
(10, 'Thai Cultural Immersion', 'Discover the rich cultural heritage and spiritual temples of beautiful Bangkok.', 'Bangkok, Thailand', 7, 322, 'Executive', 139, 'Business (Window)', 2, 8, 2199.99, 0, NULL, '2025-05-07'),
(11, 'Emerald Seattle', 'Experience the natural beauty and urban sophistication of the Pacific Northwest.', 'Seattle, Washington, USA', 5, 158, 'Deluxe', 130, 'Business (Far End)', 2, 6, 2099.99, 1, 90009, '2025-05-07'),
(12, 'Kashmir Paradise', 'Discover the breathtaking landscapes and serene lakes of \"Heaven on Earth\" in Kashmir.', 'Srinagar, Kashmir, India', 8, 153, 'Suite', 119, 'Business (Window)', 2, 6, 2399.99, 0, NULL, '2025-05-06'),
(13, 'California Dreams', 'Experience the diverse attractions of the Golden State, from beaches to national parks.', 'California, USA', 7, 131, 'Standard', 107, 'Economy (Window)', 2, 8, 2299.99, 1, 90004, '2025-05-03'),
(14, 'Cappadocia Wonders', 'Witness the mesmerizing landscape of fairy chimneys and hot air balloons in unique Cappadocia.', 'Cappadocia, Turkey', 6, 156, 'Deluxe', 126, 'Business (Middle)', 2, 8, 2499.99, 1, 90003, '2025-05-01'),
(15, 'Melbourne City Break', 'Explore the cultural capital of Australia with its eclectic arts scene and food culture.', 'Melbourne, Victoria, Australia', 6, 159, 'Executive', 132, 'Business (Window)', 2, 6, 2799.99, 0, NULL, '2025-05-01'),
(16, 'Paris Budget Experience', 'Discover the beauty of Paris without breaking the bank with comfortable accommodations.', 'Paris, France', 5, 103, 'Standard', 102, 'Economy (Middle)', 2, 4, 1299.99, 0, NULL, '2025-05-05'),
(17, 'Sri Lankan Paradise', 'Experience the tropical beauty and rich cultural heritage of enchanting Sri Lanka.', 'Colombo, Sri Lanka', 9, 151, 'Executive', 114, 'Business (Window)', 2, 8, 2599.99, 1, 90006, '2025-05-02'),
(18, 'Pakistan Explorer', 'Discover the natural beauty and historical treasures of Pakistans capital region.', 'Islamabad, Pakistan', 7, 154, 'Suite', 122, 'Business (Middle)', 4, 10, 2099.99, 1, 90003, '2025-04-29'),
(19, 'Brisbane Sunshine', 'Enjoy the subtropical climate and outdoor lifestyle of Queenslands vibrant capital.', 'Brisbane, Queensland, Australia', 6, 160, 'Standard', 134, 'Business (Window)', 2, 6, 2399.99, 1, 90010, '2025-04-30'),
(20, 'Bangkok Budget Explorer', 'Experience the vibrant culture and street food scene of Bangkok at an affordable price.', 'Bangkok, Thailand', 5, 324, 'Standard', 139, 'Economy (Far End)', 2, 8, 1199.99, 0, NULL, '2025-05-07');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE `bookings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT(11) NOT NULL,
  `type` ENUM('flight','hotel','tour') NOT NULL,
  `item_id` INT NOT NULL,
  `details` JSON,
  `booking_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_flight_booking` FOREIGN KEY (`item_id`) REFERENCES `flights`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_hotel_booking` FOREIGN KEY (`item_id`) REFERENCES `hotels`(`Hotel_ID`) ON DELETE CASCADE,
  CONSTRAINT `fk_tour_booking` FOREIGN KEY (`item_id`) REFERENCES `tours`(`Tour_ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
