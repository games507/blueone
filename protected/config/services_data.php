<?php
$date_now = date("Y-m-d G:i:s");
$ip_address = $_SERVER['REMOTE_ADDR'];

$services="
INSERT INTO {{services}} (`services_id`, `services_parent_id`, `sevices_name`, `description`, `status`, `sequence`, `date_created`, `date_modified`, `ip_address`) VALUES
(1, 0, 'Pickup & Delivery', 'lorem ipsum', 'published', 0, '$date_now', '$date_now', '$ip_address'),
(2, 0, 'Beauty Services', 'test tets', 'published', 1, '$date_now', '$date_now', '$ip_address'),
(3, 1, 'Courier Service', '', 'published', 0, '$date_now', '$date_now', '$ip_address'),
(4, 2, 'Make Up Artist', '', 'published', 0, '$date_now', '$date_now', '$ip_address'),
(5, 0, 'Repair Services', '', 'published', 2, '$date_now', '$date_now', '$ip_address'),
(6, 1, 'Laundry Service', '', 'published', 1, '$date_now', '$date_now', '$ip_address'),
(7, 1, 'Field Service', '', 'published', 2, '$date_now', '$date_now', '$ip_address'),
(8, 1, 'Grocery Delivery', '', 'published', 3, '$date_now', '$date_now', '$ip_address'),
(9, 1, 'Food Delivery', '', 'published', 4, '$date_now', '$date_now', '$ip_address'),
(10, 2, 'Wedding Stylist', '', 'published', 1, '$date_now', '$date_now', '$ip_address'),
(11, 2, 'Manicurist', '', 'published', 2, '$date_now', '$date_now', '$ip_address'),
(12, 2, 'Hair Stylist', '', 'published', 3, '$date_now', '$date_now', '$ip_address'),
(13, 2, 'Aesthetician', '', 'published', 4, '$date_now', '$date_now', '$ip_address'),
(14, 0, 'Home Services', '', 'published', 3, '$date_now', '$date_now', '$ip_address'),
(15, 0, 'Health and Well Being', '', 'published', 4, '$date_now', '$date_now', '$ip_address'),
(16, 5, 'Electrical Works', '', 'published', 0, '$date_now', '$date_now', '$ip_address'),
(17, 5, 'Computer Repair', '', 'published', 0, '$date_now', '$date_now', '$ip_address'),
(18, 5, 'Appliances Repair', '', 'published', 0, '$date_now', '$date_now', '$ip_address'),
(19, 5, 'Plumbing Repair', '', 'published', 0, '$date_now', '$date_now', '$ip_address'),
(20, 5, 'Auto Repair', '', 'published', 0, '$date_now', '$date_now', '$ip_address'),
(21, 14, 'Home Cleaning', '', 'published', 0, '$date_now', '$date_now', '$ip_address'),
(22, 14, 'Landscaping', '', 'published', 0, '$date_now', '$date_now', '$ip_address'),
(23, 14, 'Maid Service', '', 'published', 0, '$date_now', '$date_now', '$ip_address'),
(24, 14, 'Alarm & Security', '', 'published', 0, '$date_now', '$date_now', '$ip_address'),
(25, 14, 'Personal Organizer', '', 'published', 0, '$date_now', '$date_now', '$ip_address'),
(26, 15, 'Yoga Instructor', '', 'published', 0, '$date_now', '$date_now', '$ip_address'),
(27, 15, 'Personal Trainer', '', 'published', 1, '$date_now', '$date_now', '$ip_address'),
(28, 15, 'Family Physician', '', 'published', 2, '$date_now', '$date_now', '$ip_address'),
(29, 15, 'Alternative Healing', '', 'published', 3, '$date_now', '$date_now', '$ip_address'),
(30, 15, 'Massage Therapist', '', 'published', 4, '$date_now', '$date_now', '$ip_address');
";