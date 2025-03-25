-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 25, 2025 at 05:49 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `webhook-handler`
--

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE `config` (
  `id` int(12) NOT NULL,
  `name` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `config`
--

INSERT INTO `config` (`id`, `name`) VALUES
(1, 'a:40:{s:10:\"SITE_TITLE\";s:28:\"Welcome to Signature Manager\";s:14:\"SITE_SORTTITLE\";N;s:8:\"SITE_URL\";N;s:12:\"SITE_SORTURL\";b:0;s:19:\"AdminSessionTimeout\";s:5:\"36000\";s:18:\"UserSessionTimeout\";s:5:\"36000\";s:11:\"cit_dbdebug\";s:1:\"0\";s:11:\"mod_rewrite\";s:1:\"1\";s:6:\"mailer\";s:4:\"smtp\";s:8:\"sendmail\";s:18:\"/usr/sbin/sendmail\";s:8:\"smtpauth\";s:1:\"1\";s:10:\"smtpsecure\";s:3:\"tls\";s:8:\"smtpport\";s:3:\"587\";s:8:\"smtpuser\";s:28:\"support@customesignature.com\";s:8:\"smtppass\";s:16:\"fpxotagcmtwqadrf\";s:8:\"smtphost\";s:14:\"smtp.gmail.com\";s:10:\"smtpdomain\";s:0:\"\";s:4:\"logo\";s:8:\"logo.png\";s:14:\"PAYPAL_EMAILID\";N;s:17:\"SITE_PHONE_NUMBER\";N;s:18:\"SITE_EMAIL_ADDRESS\";N;s:12:\"SITE_ADDRESS\";N;s:10:\"SITE_COLOR\";s:7:\"#000000\";s:12:\"SITE_BGCOLOR\";s:7:\"#00CCFF\";s:11:\"POSTAL_CODE\";N;s:20:\"SITE_GOOGLEANALYTICS\";N;s:12:\"SUPPORT_TEXT\";N;s:13:\"REDIRECT_TEXT\";N;s:11:\"CSS_VERSION\";s:4:\"2.16\";s:10:\"JS_VERSION\";s:4:\"2.16\";s:12:\"SITE_COUNTRY\";N;s:19:\"SITE_CURRENCYSYMBOL\";s:1:\"$\";s:9:\"logoemail\";s:0:\"\";s:7:\"logoapp\";s:0:\"\";s:13:\"SITE_RGBCOLOR\";s:0:\"\";s:17:\"STRIPE_SECRET_KEY\";s:107:\"sk_test_51Lf5JtHp7DgNkCIvp1WlMJ4NaRzuyCiqQFmvpZKvYToQuk0XpyFpBDEPMd10mQLTNsIHih4nHdhop3PknZ3FU4XF004Y7uuWlQ\";s:22:\"STRIPE_PUBLISHABLE_KEY\";s:107:\"pk_test_51Lf5JtHp7DgNkCIvskUjNu75xBD5bJCrsAgaPf0ZT9al8jfTzcJXG8TwD1NZMI03qqINPFFIrrYHuJSN5n9ubfD100czetLSeB\";s:21:\"STRIPE_WEBHOOK_SECRET\";s:38:\"whsec_YF7kVqX2rd2qlEHdJM4725LbpZHulnlr\";s:14:\"ADMIN_LOGINKEY\";s:24:\"asUrty34dGhe78hfVtuk89df\";s:8:\"S3BUCKET\";s:1:\"0\";}');

-- --------------------------------------------------------

--
-- Table structure for table `git_commit_data`
--

CREATE TABLE `git_commit_data` (
  `id` int(11) NOT NULL,
  `commit_id` varchar(255) NOT NULL,
  `commit_message` varchar(255) NOT NULL,
  `commit_filelist` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `registerusers`
--

CREATE TABLE `registerusers` (
  `id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `registerusers_subscription`
--

CREATE TABLE `registerusers_subscription` (
  `user_id` int(6) NOT NULL,
  `plan_id` int(6) NOT NULL,
  `plan_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = Active, 0 = Deactive',
  `customer_id` char(100) NOT NULL COMMENT 'stripe cusId',
  `subscription_id` char(100) NOT NULL COMMENT 'stripe SubId',
  `price_id` char(100) NOT NULL COMMENT 'stripe PriceId',
  `plan_interval` char(50) NOT NULL,
  `plan_signaturelimit` int(11) NOT NULL,
  `period_start` int(12) NOT NULL,
  `period_end` int(12) NOT NULL,
  `invoice_amount` int(6) NOT NULL,
  `invoice_link` varchar(500) NOT NULL,
  `apply_coupon` int(6) NOT NULL,
  `auto_renew` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=on,1=off',
  `free_trial` tinyint(1) NOT NULL COMMENT '1=freetrial',
  `plan_cancel` tinyint(1) NOT NULL COMMENT '1 =canceled',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stripe_webhook`
--

CREATE TABLE `stripe_webhook` (
  `id` int(11) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `event_response` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `git_commit_data`
--
ALTER TABLE `git_commit_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `registerusers`
--
ALTER TABLE `registerusers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `registerusers_subscription`
--
ALTER TABLE `registerusers_subscription`
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `stripe_webhook`
--
ALTER TABLE `stripe_webhook`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `config`
--
ALTER TABLE `config`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `git_commit_data`
--
ALTER TABLE `git_commit_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `registerusers`
--
ALTER TABLE `registerusers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stripe_webhook`
--
ALTER TABLE `stripe_webhook`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
