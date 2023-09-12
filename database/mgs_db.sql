-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 27, 2023 at 07:09 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `mgs_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `category_list`
--

CREATE TABLE `category_list` (
  `id` bigint(30) NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `delete_flag` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `category_list`
--

INSERT INTO `category_list` (`id`, `name`, `description`, `status`, `delete_flag`, `created_at`, `updated_at`) VALUES
(1, 'Rock', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 1, 0, '2023-01-26 10:50:48', NULL),
(2, 'EDM', 'Ut at mi ac magna ullamcorper lobortis sit amet vel erat.', 1, 0, '2023-01-27 10:19:47', NULL),
(3, 'POP', 'Sed rhoncus velit enim, ut egestas eros elementum ut.', 1, 0, '2023-01-27 10:20:04', NULL),
(4, 'Alternative', 'Praesent dictum varius metus, at scelerisque lacus iaculis nec.', 1, 0, '2023-01-27 10:20:26', NULL),
(5, 'Alternative Rock', 'Aliquam erat volutpat. Donec eleifend magna purus, sit amet consectetur nunc maximus quis.', 1, 0, '2023-01-27 10:20:46', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `music_list`
--

CREATE TABLE `music_list` (
  `id` bigint(30) NOT NULL,
  `title` text NOT NULL,
  `artist` text NOT NULL,
  `category_id` bigint(20) DEFAULT NULL,
  `description` text NOT NULL,
  `banner_path` text NOT NULL,
  `audio_path` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `music_list`
--

INSERT INTO `music_list` (`id`, `title`, `artist`, `category_id`, `description`, `banner_path`, `audio_path`, `status`, `delete_flag`, `created_at`, `updated_at`) VALUES
(1, 'Music 101', 'Artist 101', 1, 'Fusce convallis velit quam, vitae porta turpis accumsan ac.', 'uploads/music_banners/banner1_1.jpg', 'uploads/audio/Funky Upbeat Hip Hop Background Music For Videos (No Copyright).mp3', 1, 0, '2023-01-27 10:04:31', '2023-01-27 11:43:47'),
(2, 'Music 102', 'Artist 102', 2, 'Donec vitae ipsum quis tortor eleifend consequat nec nec mi.', 'uploads/music_banners/banner2_2.png', 'uploads/audio/Cool Motivational Hip Hop Background Music for Sports and Workout Videos.mp3', 1, 0, '2023-01-27 10:21:21', '2023-01-27 10:21:25'),
(3, 'test', 'test', 4, 'test', 'uploads/music_banners/banner3_1.jpg', '', 1, 0, '2023-01-27 11:44:10', '2023-01-27 11:44:27');

-- --------------------------------------------------------

--
-- Table structure for table `system_info`
--

CREATE TABLE `system_info` (
  `id` int(30) NOT NULL,
  `meta_field` text NOT NULL,
  `meta_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `system_info`
--

INSERT INTO `system_info` (`id`, `meta_field`, `meta_value`) VALUES
(1, 'name', 'Music Gallery Site - PHP'),
(6, 'short_name', 'MGS - PHP'),
(11, 'logo', 'uploads/logo.png?v=1674703192'),
(13, 'user_avatar', 'uploads/user_avatar.jpg'),
(14, 'cover', 'uploads/cover.png?v=1674699976'),
(17, 'phone', '456-987-1231'),
(18, 'mobile', '09123456987 / 094563212222 '),
(19, 'email', 'info@musicschool.com'),
(20, 'address', 'Here St, Down There City, Anywhere Here, 2306 -updated');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(30) NOT NULL,
  `firstname` varchar(250) NOT NULL,
  `middlename` text DEFAULT NULL,
  `lastname` varchar(250) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `avatar` text DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 0,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='2';

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `middlename`, `lastname`, `username`, `password`, `avatar`, `last_login`, `type`, `date_added`, `date_updated`) VALUES
(1, 'Adminstrator', '', 'Admin', 'admin', '0192023a7bbd73250516f069df18b500', 'uploads/avatars/1.png?v=1649834664', NULL, 1, '2021-01-20 14:02:37', '2022-05-16 14:17:49'),
(2, 'John', 'D', 'Smith', 'jsmith', '1254737c076cf867dc53d60a0364f38e', 'uploads/avatars/2.png?v=1653715045', NULL, 2, '2022-05-28 13:17:24', '2022-05-28 13:17:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category_list`
--
ALTER TABLE `category_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `music_list`
--
ALTER TABLE `music_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_info`
--
ALTER TABLE `system_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category_list`
--
ALTER TABLE `category_list`
  MODIFY `id` bigint(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `music_list`
--
ALTER TABLE `music_list`
  MODIFY `id` bigint(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `system_info`
--
ALTER TABLE `system_info`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;
