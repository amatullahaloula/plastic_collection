

CREATE TABLE `bottle_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `location` varchar(255) NOT NULL,
  `room_number` varchar(50) NOT NULL,
  `bottles` int(11) NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
);

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `nickname` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `degree` varchar(150) DEFAULT NULL,
  `role` enum('student','cleaner','admin') NOT NULL DEFAULT 'student',
  `password` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `remember_expires` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_info` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
);

CREATE TABLE `collection_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `location` varchar(255) NOT NULL,
  `bottles` int(11) NOT NULL,
  `note` text DEFAULT NULL,
  `status` enum('pending','accepted','completed','cancelled') DEFAULT 'pending',
  `cleaner_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `status_cleaner` enum('pending','accepted','rejected','completed') DEFAULT 'pending',
  `status_admin` enum('pending','completed') DEFAULT 'pending',
  PRIMARY KEY (`id`),
  CONSTRAINT `collection_requests_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `collection_requests_ibfk_2` FOREIGN KEY (`cleaner_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
);

CREATE TABLE `collection_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `cleaner_id` int(11) NOT NULL,
  `bottles` int(11) NOT NULL,
  `collected_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  CONSTRAINT `collection_records_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `collection_requests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `collection_records_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `collection_records_ibfk_3` FOREIGN KEY (`cleaner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

CREATE TABLE `payment_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `method` varchar(20) DEFAULT NULL,
  `momo_number` varchar(20) DEFAULT NULL,
  `network` varchar(20) DEFAULT NULL,
  `bank_name` varchar(50) DEFAULT NULL,
  `account_number` varchar(40) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
);

CREATE TABLE `rewards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `bottles` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `amount_money` decimal(10,2) NOT NULL,
  `paid` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  CONSTRAINT `rewards_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

CREATE TABLE `support_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('pending','in_progress','resolved') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `resolved_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `support_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
);