-- phpMyAdmin SQL Dump
-- version 5.0.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 21, 2021 at 11:41 AM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `deepsound_test`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `track_id` int(11) NOT NULL DEFAULT 0,
  `type` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `admin_invitations`
--

CREATE TABLE `admin_invitations` (
  `id` int(11) NOT NULL,
  `code` varchar(300) NOT NULL DEFAULT '0',
  `posted` varchar(50) NOT NULL DEFAULT '0',
  `status` varchar(50) CHARACTER SET utf8mb4 DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ads_transactions`
--

CREATE TABLE `ads_transactions` (
  `id` int(11) NOT NULL,
  `ad_id` int(11) NOT NULL DEFAULT 0,
  `track_owner` int(11) NOT NULL DEFAULT 0,
  `amount` varchar(11) NOT NULL DEFAULT '0',
  `type` varchar(50) NOT NULL DEFAULT '',
  `time` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `albums`
--

CREATE TABLE `albums` (
  `id` int(11) NOT NULL,
  `album_id` varchar(16) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL DEFAULT 0,
  `title` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `category_id` int(11) NOT NULL DEFAULT 0,
  `thumbnail` varchar(200) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT 0,
  `registered` varchar(15) NOT NULL DEFAULT '00/0000',
  `price` float NOT NULL DEFAULT 0,
  `purchases` int(11) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `announcement`
--

CREATE TABLE `announcement` (
  `id` int(11) NOT NULL,
  `text` text DEFAULT NULL,
  `time` int(32) NOT NULL DEFAULT 0,
  `active` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `announcement_views`
--

CREATE TABLE `announcement_views` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `announcement_id` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `apps_sessions`
--

CREATE TABLE `apps_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `session_id` varchar(120) NOT NULL DEFAULT '',
  `platform` varchar(32) NOT NULL DEFAULT '',
  `platform_details` text DEFAULT NULL,
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `artists_tags`
--

CREATE TABLE `artists_tags` (
  `id` int(11) NOT NULL,
  `artist_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `track_id` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0,
  `approved` int(11) NOT NULL DEFAULT 0,
  `seen` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `artist_requests`
--

CREATE TABLE `artist_requests` (
  `id` int(11) NOT NULL,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `website` varchar(100) NOT NULL DEFAULT '',
  `details` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `category_id` int(11) NOT NULL DEFAULT 0,
  `photo` varchar(150) NOT NULL DEFAULT '',
  `passport` varchar(150) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bad_login`
--

CREATE TABLE `bad_login` (
  `id` int(11) NOT NULL,
  `ip` varchar(100) NOT NULL DEFAULT '',
  `time` int(50) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `bank_receipts`
--

CREATE TABLE `bank_receipts` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `description` tinytext DEFAULT NULL,
  `price` varchar(50) NOT NULL DEFAULT '0',
  `mode` varchar(50) NOT NULL DEFAULT '',
  `track_id` varchar(50) CHARACTER SET utf8mb4 DEFAULT '',
  `approved` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `receipt_file` varchar(250) NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approved_at` int(11) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `banned_ip`
--

CREATE TABLE `banned_ip` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(32) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `blocks`
--

CREATE TABLE `blocks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `blocked_id` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `blog`
--

CREATE TABLE `blog` (
  `id` int(11) NOT NULL,
  `title` varchar(120) NOT NULL DEFAULT '',
  `content` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `posted` varchar(300) DEFAULT '0',
  `category` int(255) DEFAULT 0,
  `thumbnail` varchar(100) DEFAULT 'upload/photos/d-blog.jpg',
  `view` int(11) DEFAULT 0,
  `shared` int(11) DEFAULT 0,
  `tags` varchar(300) DEFAULT '',
  `created_at` int(11) NOT NULL DEFAULT 0,
  `created_by` int(11) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `blog_comments`
--

CREATE TABLE `blog_comments` (
  `id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `blog_likes`
--

CREATE TABLE `blog_likes` (
  `id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `comment_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `cateogry_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `tracks` int(11) NOT NULL DEFAULT 0,
  `color` varchar(20) NOT NULL DEFAULT '#333',
  `background_thumb` varchar(120) NOT NULL DEFAULT '',
  `time` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `cateogry_name`, `tracks`, `color`, `background_thumb`, `time`) VALUES
(1, 'Other', 0, '#000000', 'upload/photos/2019/04/FaS2oOegTOBm5OpFJiCK_17_6ad5d4edf1fb542961a2a64a8d0768e7_image.jpg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `track_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `songseconds` float NOT NULL DEFAULT 0,
  `songpercentage` float NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `comment_replies`
--

CREATE TABLE `comment_replies` (
  `id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE `config` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `value` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `config`
--

INSERT INTO `config` (`id`, `name`, `value`) VALUES
(1, 'theme', 'default'),
(2, 'censored_words', ''),
(3, 'title', 'DeepSound'),
(4, 'name', 'DeepSound'),
(5, 'keyword', 'deepsound,video sharing'),
(6, 'email', 'deendoughouz@gmail.com'),
(7, 'description', 'DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!'),
(8, 'validation', 'off'),
(9, 'recaptcha', 'off'),
(10, 'recaptcha_key', ''),
(11, 'language', 'english'),
(12, 'google_app_ID', ''),
(13, 'google_app_key', ''),
(14, 'facebook_app_ID', ''),
(15, 'facebook_app_key', ''),
(16, 'twitter_app_ID', ''),
(17, 'twitter_app_key', ''),
(21, 'smtp_or_mail', 'mail'),
(22, 'smtp_host', ''),
(23, 'smtp_username', ''),
(24, 'smtp_password', ''),
(25, 'smtp_encryption', 'ssl'),
(26, 'smtp_port', ''),
(27, 'delete_account', 'on'),
(36, 'last_admin_collection', '1575458193'),
(37, 'user_statics', '[{\"month\":\"January\",\"new_users\":0},{\"month\":\"February\",\"new_users\":0},{\"month\":\"March\",\"new_users\":0},{\"month\":\"April\",\"new_users\":0},{\"month\":\"May\",\"new_users\":0},{\"month\":\"June\",\"new_users\":0},{\"month\":\"July\",\"new_users\":0},{\"month\":\"August\",\"new_users\":0},{\"month\":\"September\",\"new_users\":0},{\"month\":\"October\",\"new_users\":0},{\"month\":\"November\",\"new_users\":0},{\"month\":\"December\",\"new_users\":1}]'),
(38, 'audio_statics', '[{&quot;month&quot;:&quot;January&quot;,&quot;new_videos&quot;:0},{&quot;month&quot;:&quot;February&quot;,&quot;new_videos&quot;:0},{&quot;month&quot;:&quot;March&quot;,&quot;new_videos&quot;:0},{&quot;month&quot;:&quot;April&quot;,&quot;new_videos&quot;:0},{&quot;month&quot;:&quot;May&quot;,&quot;new_videos&quot;:0},{&quot;month&quot;:&quot;June&quot;,&quot;new_videos&quot;:0},{&quot;month&quot;:&quot;July&quot;,&quot;new_videos&quot;:15},{&quot;month&quot;:&quot;August&quot;,&quot;new_videos&quot;:18},{&quot;month&quot;:&quot;September&quot;,&quot;new_videos&quot;:0},{&quot;month&quot;:&quot;October&quot;,&quot;new_videos&quot;:0},{&quot;month&quot;:&quot;November&quot;,&quot;new_videos&quot;:0},{&quot;month&quot;:&quot;December&quot;,&quot;new_videos&quot;:0}]'),
(45, 'user_registration', 'on'),
(46, 'verification_badge', 'on'),
(49, 'fb_login', 'off'),
(50, 'tw_login', 'off'),
(51, 'plus_login', 'off'),
(52, 'wowonder_app_ID', ''),
(53, 'wowonder_app_key', ''),
(54, 'wowonder_domain_uri', ''),
(56, 'wowonder_login', 'off'),
(57, 'wowonder_img', ''),
(58, 'google', ''),
(59, 'last_created_sitemap', '22-03-2019'),
(60, 'is_ok', '1'),
(63, 'go_pro', 'on'),
(64, 'paypal_id', ''),
(65, 'paypal_secret', ''),
(66, 'paypal_mode', 'sandbox'),
(67, 'last_backup', '22-03-2019'),
(68, 'user_ads', 'on'),
(70, 'max_upload', '512000000'),
(71, 's3_upload', 'off'),
(72, 's3_bucket_name', ''),
(73, 'amazone_s3_key', ''),
(74, 'amazone_s3_s_key', ''),
(75, 'region', 'us-east-1'),
(81, 'apps_api_key', 'cfdb063a99d830ee9e3c1f1e49174f44d04f439d'),
(82, 'ffmpeg_system', 'off'),
(83, 'ffmpeg_binary_file', './ffmpeg/ffmpeg'),
(84, 'user_max_upload', '96000000'),
(86, 'convert_speed', 'faster'),
(87, 'night_mode', 'night'),
(90, 'ftp_host', 'localhost'),
(91, 'ftp_port', '21'),
(92, 'ftp_username', ''),
(93, 'ftp_password', ''),
(94, 'ftp_upload', 'off'),
(95, 'ftp_endpoint', 'storage.wowonder.com'),
(96, 'ftp_path', './'),
(111, 'currency', 'USD'),
(112, 'commission', '50'),
(113, 'pro_upload_limit', '50'),
(114, 'pro_price', '9'),
(115, 'server_key', ''),
(116, 'facebook_url', ''),
(117, 'twitter_url', ''),
(118, 'google_url', ''),
(119, 'currency_symbol', '$'),
(120, 'maintenance_mode', 'off'),
(121, 'auto_friend_users', 'admin'),
(122, 'waves_color', '#f98f1d'),
(123, 'total_songs', '0'),
(124, 'total_albums', '0'),
(125, 'total_plays', '0'),
(126, 'total_sales', '0.00'),
(127, 'total_users', '1'),
(128, 'total_artists', '0'),
(129, 'total_playlists', '0'),
(130, 'total_unactive_users', '0'),
(131, 'user_statics', '[{\"month\":\"January\",\"new_users\":0},{\"month\":\"February\",\"new_users\":0},{\"month\":\"March\",\"new_users\":0},{\"month\":\"April\",\"new_users\":0},{\"month\":\"May\",\"new_users\":0},{\"month\":\"June\",\"new_users\":0},{\"month\":\"July\",\"new_users\":0},{\"month\":\"August\",\"new_users\":0},{\"month\":\"September\",\"new_users\":0},{\"month\":\"October\",\"new_users\":0},{\"month\":\"November\",\"new_users\":0},{\"month\":\"December\",\"new_users\":1}]'),
(132, 'songs_statics', '[{\"month\":\"January\",\"new_songs\":0},{\"month\":\"February\",\"new_songs\":0},{\"month\":\"March\",\"new_songs\":0},{\"month\":\"April\",\"new_songs\":0},{\"month\":\"May\",\"new_songs\":0},{\"month\":\"June\",\"new_songs\":0},{\"month\":\"July\",\"new_songs\":0},{\"month\":\"August\",\"new_songs\":0},{\"month\":\"September\",\"new_songs\":0},{\"month\":\"October\",\"new_songs\":0},{\"month\":\"November\",\"new_songs\":0},{\"month\":\"December\",\"new_songs\":0}]'),
(133, 'version', '1.3.5'),
(134, 'artist_sell', 'on'),
(135, 'stripe_version', ''),
(136, 'stripe_secret', ''),
(137, 'bank_payment', 'off'),
(138, 'bank_transfer_note', 'In order to confirm the bank transfer, you will need to upload a receipt or take a screenshot of your transfer within 1 day from your payment date. If a bank transfer is made but no receipt is uploaded within this period, your order will be cancelled. We will verify and confirm your receipt within 3 working days from the date you upload it.'),
(139, 'who_can_download', 'pro'),
(140, 'stripe_currency', 'USD'),
(141, 'paypal_currency', 'USD'),
(142, 'push', '0'),
(143, 'android_push_native', '0'),
(144, 'ios_push_native', '0'),
(145, 'android_m_push_id', ''),
(146, 'android_m_push_key', ''),
(147, 'ios_m_push_id', ''),
(148, 'ios_m_push_key', ''),
(149, 'displaymode', 'night'),
(150, 'bank_description', '<div class=\"dt_settings_header bg_gradient\">\n    <div class=\"bank_info_innr\">\n        <h4 class=\"bank_name\">Garanti Bank</h4>\n        <div class=\"row\">\n            <div class=\"col-md-12\">\n                <div class=\"bank_account\">\n                    <p>4796824372433055</p>\n                    <span class=\"help-block\">Account number / IBAN</span>\n                </div>\n            </div>\n            <div class=\"col-md-12\">\n                <div class=\"bank_account_holder\">\n                    <p>Antoian Kordiyal</p>\n                    <span class=\"help-block\">Account name</span>\n                </div>\n            </div>\n            <div class=\"col-md-6\">\n                <div class=\"bank_account_code\">\n                    <p>TGBATRISXXX</p>\n                    <span class=\"help-block\">Routing code</span>\n                </div>\n            </div>\n            <div class=\"col-md-6\">\n                <div class=\"bank_account_country\">\n                    <p>United States</p>\n                    <span class=\"help-block\">Country</span>\n                </div>\n            </div>\n        </div>\n    </div>\n</div>'),
(151, 'stripe_payment', 'on'),
(152, 'paypal_payment', 'on'),
(153, 'multiple_upload', 'on'),
(154, 'usr_v_mon', 'on'),
(155, 'stripe_id', ''),
(156, 'ad_v_price', '0.1'),
(157, 'pub_price', '0.02'),
(158, 'ad_c_price', '0.5'),
(159, 'sound_cloud_client_id', ''),
(160, 'soundcloud_login', 'off'),
(161, 'sound_cloud_client_secret', ''),
(162, 'emailNotification', 'off'),
(163, 'login_auth', '0'),
(164, 'two_factor', '0'),
(165, 'two_factor_type', 'email'),
(166, 'sms_twilio_username', ''),
(167, 'sms_twilio_password', ''),
(168, 'sms_t_phone_number', ''),
(169, 'sms_phone_number', ''),
(170, 'rapidapi_key', '69bd0c7193msh3c4abb39db3a82fp18c336jsn8470910ae9f0'),
(171, 'soundcloud_go', 'off'),
(172, 'soundcloud_import', 'off'),
(173, 'radio_station_import', 'off'),
(174, 'affiliate_system', '0'),
(175, 'affiliate_type', '0'),
(176, 'amount_ref', '0.10'),
(177, 'amount_percent_ref', '0'),
(178, 'discover_land', '0'),
(179, 'itunes_import', 'on'),
(180, 'itunes_affiliate', 'admin'),
(181, 'itunes_partner_token', ''),
(182, 'deezer_import', 'off'),
(183, 'audio_ads', 'on'),
(184, 'who_audio_ads', 'users'),
(185, 'who_can_upload', 'all'),
(186, 'android_n_push_id', ''),
(187, 'android_n_push_key', ''),
(188, 'ios_n_push_id', ''),
(189, 'ios_n_push_key', ''),
(190, 'web_push_id', ''),
(191, 'web_push_key', ''),
(192, 'allow_user_create_blog', 'off'),
(193, 'point_system', 'on'),
(194, 'point_system_comment_cost', '10'),
(195, 'point_system_upload_cost', '30'),
(196, 'point_system_replay_comment_cost', '40'),
(197, 'point_system_like_track_cost', '45'),
(198, 'point_system_dislike_track_cost', '50'),
(199, 'point_system_like_comment_cost', '55'),
(200, 'point_system_repost_cost', '60'),
(201, 'point_system_track_download_cost', '65'),
(202, 'point_system_like_blog_comment_cost', '70'),
(203, 'point_system_unlike_comment_cost', '55'),
(204, 'point_system_unlike_blog_comment_cost', '70'),
(205, 'point_system_import_cost', '80'),
(206, 'point_system_purchase_track_cost', '81'),
(207, 'point_system_go_pro_cost', '82'),
(208, 'point_system_review_track_cost', '83'),
(209, 'point_system_report_track_cost', '84'),
(210, 'point_system_report_comment_cost', '85'),
(211, 'point_system_add_to_playlist_cost', '86'),
(212, 'point_system_create_new_playlist_cost', '87'),
(213, 'point_system_update_profile_picture_cost', '5'),
(214, 'point_system_update_profile_cover_cost', '10'),
(215, 'point_system_points_to_dollar', '0.001'),
(216, 'prevent_system', '0'),
(217, 'bad_login_limit', '4'),
(218, 'lock_time', '10'),
(219, 'fame_system', '1'),
(220, 'views_count', '100000'),
(221, 'donate_system', '1'),
(222, 'tag_artist_system', '1'),
(223, 'maxCharacters', '640');

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `id` int(11) NOT NULL,
  `user_one` int(11) NOT NULL DEFAULT 0,
  `user_two` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `copyrights`
--

CREATE TABLE `copyrights` (
  `id` int(11) NOT NULL,
  `track_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `custompages`
--

CREATE TABLE `custompages` (
  `id` int(11) NOT NULL,
  `page_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `page_title` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `page_content` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `page_type` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dislikes`
--

CREATE TABLE `dislikes` (
  `id` int(11) NOT NULL,
  `track_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `comment_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `downloads`
--

CREATE TABLE `downloads` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `fingerprint` varchar(120) NOT NULL DEFAULT '',
  `track_id` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `track_id` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `followers`
--

CREATE TABLE `followers` (
  `id` int(11) NOT NULL,
  `follower_id` int(11) NOT NULL DEFAULT 0,
  `following_id` int(11) NOT NULL DEFAULT 0,
  `artist_id` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `langs`
--

CREATE TABLE `langs` (
  `id` int(11) NOT NULL,
  `ref` varchar(250) DEFAULT '',
  `options` text NOT NULL,
  `lang_key` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `english` text DEFAULT NULL,
  `arabic` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `dutch` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `french` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `german` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `russian` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `spanish` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `turkish` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `langs`
--

INSERT INTO `langs` (`id`, `ref`, `options`, `lang_key`, `english`, `arabic`, `dutch`, `french`, `german`, `russian`, `spanish`, `turkish`) VALUES
(1, '', '', 'signup', 'Signup', 'سجل', 'Inschrijven', 'S\'inscrire', 'Anmelden', 'Зарегистрироваться', 'Regístrate', 'Kaydol'),
(2, '', '', 'get_access_to_your_music__playlists_and_account', 'Get access to your music, playlists and account', 'احصل على حق الوصول إلى الموسيقى وقوائم التشغيل والحساب', 'Krijg toegang tot je muziek, afspeellijsten en account', 'Accédez à votre musique, à vos playlists et à votre compte', 'Erhalten Sie Zugriff auf Ihre Musik, Wiedergabelisten und Ihr Konto', 'Получите доступ к своей музыке, плейлистам и аккаунту', 'Accede a tu música, listas de reproducción y cuenta.', 'Müziğinize, çalma listelerinize ve hesabınıza erişin'),
(3, '', '', 'full_name', 'Full Name', 'الاسم الكامل', 'Voor-en achternaam', 'Nom complet', 'Vollständiger Name', 'ФИО', 'Nombre completo', 'Ad Soyad'),
(4, '', '', 'username', 'Username', 'اسم المستخدم', 'Gebruikersnaam', 'Nom d\'utilisateur', 'Nutzername', 'имя пользователя', 'Nombre de usuario', 'Kullanıcı adı'),
(5, '', '', 'email_address', 'Email address', 'عنوان بريد الكتروني', 'E-mailadres', 'Adresse électronique', 'E-Mail-Addresse', 'Адрес электронной почты', 'Dirección de correo electrónico', 'E'),
(6, '', '', 'password', 'Password', 'كلمه السر', 'Wachtwoord', 'Mot de passe', 'Passwort', 'пароль', 'Contraseña', 'Parola'),
(7, '', '', 'confirm_password', 'Confirm Password', 'تأكيد كلمة المرور', 'bevestig wachtwoord', 'Confirmez le mot de passe', 'Passwort bestätigen', 'Подтвердите Пароль', 'Confirmar contraseña', 'Şifreyi Onayla'),
(8, '', '', 'already_have_an_account_', 'Already have an account?', 'هل لديك حساب؟', 'Heb je al een account?', 'Vous avez déjà un compte?', 'Hast du schon ein Konto?', 'Уже есть аккаунт?', '¿Ya tienes una cuenta?', 'Zaten hesabınız var mı?'),
(9, '', '', 'login', 'Login', 'تسجيل الدخول', 'Log in', 'S\'identifier', 'Anmeldung', 'Авторизоваться', 'Iniciar sesión', 'Oturum aç'),
(10, '', '', 'by_signing_up__you_agree_to_our', 'By signing up, you agree to our', 'بالتسجيل ، أنت توافق على موقعنا', 'Door je aan te melden, ga je akkoord met onze', 'En vous inscrivant, vous acceptez notre', 'Mit der Anmeldung stimmen Sie unserem zu', 'Регистрируясь, вы соглашаетесь с нашими', 'Al registrarte, aceptas nuestra', 'Kaydolarak kabul etmiş sayılırsınız.'),
(11, '', '', 'terms', 'Terms', 'شروط', 'Voorwaarden', 'termes', 'Bedingungen', 'термины', 'Condiciones', 'şartlar'),
(12, '', '', 'and', 'and', 'و', 'en', 'et', 'und', 'а также', 'y', 've'),
(13, '', '', 'privacy_policy', 'Privacy Policy', 'سياسة خاصة', 'Privacybeleid', 'Politique de confidentialité', 'Datenschutz-Bestimmungen', 'политика конфиденциальности', 'Política de privacidad', 'Gizlilik Politikası'),
(14, '', '', 'please_wait..', 'Please wait..', 'ارجوك انتظر..', 'Even geduld aub..', 'S\'il vous plaît, attendez..', 'Warten Sie mal..', 'Пожалуйста, подождите..', 'Por favor espera..', 'Lütfen bekle..'),
(15, '', '', 'search_for_songs__artists__playlists_and_more', 'Search for songs, artists, playlists and more..', 'البحث عن الأغاني والفنانين وقوائم التشغيل والمزيد ..', 'Zoeken naar nummers, artiesten, afspeellijsten en meer ..', 'Recherchez des chansons, des artistes, des playlists et plus encore.', 'Suchen Sie nach Liedern, Künstlern, Wiedergabelisten und mehr ..', 'Поиск песен, исполнителей, плейлистов и многое другое ..', 'Busca canciones, artistas, listas de reproducción y más.', 'Şarkıları, sanatçıları, çalma listelerini ve daha fazlasını arayın ..'),
(16, '', '', 'trending_now', 'Trending Now', 'تتجه الآن', 'Nu populair', 'À la mode maintenant', 'Gerade angesagt', 'Актуальные', 'Siendo tendencia ahora', 'Bu aralar moda'),
(17, '', '', 'advanced_search', 'Advanced Search', 'البحث المتقدم', 'geavanceerd zoeken', 'Recherche Avancée', 'Erweiterte Suche', 'Расширенный поиск', 'Búsqueda Avanzada', 'gelişmiş Arama'),
(18, '', '', 'feed', 'Feed', 'تغذية', 'Voeden', 'Alimentation', 'Futter', 'Кормить', 'Alimentar', 'besleme'),
(19, '', '', 'upload', 'Upload', 'رفع', 'Uploaden', 'Télécharger', 'Hochladen', 'Загрузить', 'Subir', 'Yükleme'),
(20, '', '', 'dashboard', 'Dashboard', 'لوحة القيادة', 'Dashboard', 'Tableau de bord', 'Instrumententafel', 'Приборная доска', 'Tablero', 'gösterge paneli'),
(21, '', '', 'settings', 'Settings', 'الإعدادات', 'instellingen', 'Réglages', 'die Einstellungen', 'настройки', 'Ajustes', 'Ayarlar'),
(22, '', '', 'recently_played', 'Recently Played', 'لعبت مؤخرا', 'Recent gespeeld', 'Joué récemment', 'Kürzlich gespielt', 'Недавно играл', 'Recientemente jugado', 'En Son Oynanan'),
(23, '', '', 'my_playlists', 'My Playlists', 'قوائم التشغيل الخاصة بي', 'Mijn afspeellijsten', 'Mes playlists', 'Meine Wiedergabelisten', 'Мои плейлисты', 'Mis playlists', 'Oynatma Listelerim'),
(24, '', '', 'favourites', 'Favourites', 'المفضلة', 'favorieten', 'Favoris', 'Favoriten', 'Избранные', 'Favoritos', 'Favoriler'),
(25, '', '', 'logout', 'Logout', 'الخروج', 'Uitloggen', 'Connectez - Out', 'Ausloggen', 'Выйти', 'Cerrar sesión', 'Çıkış Yap'),
(26, '', '', 'register', 'Register', 'تسجيل', 'Registreren', 'registre', 'Registrieren', 'регистр', 'Registro', 'Kayıt olmak'),
(27, '', '', 'forgot_your_password_', 'Forgot your password?', 'نسيت رقمك السري؟', 'uw wachtwoord vergeten?', 'Mot de passe oublié?', 'Haben Sie Ihr Passwort vergessen?', 'Забыли пароль?', '¿Olvidaste tu contraseña?', 'Parolanızı mı unuttunuz?'),
(28, '', '', 'don_t_have_an_account_', 'Don&#039;t have an account?', 'ليس لديك حساب؟', 'Heb je geen account?', 'Vous n\'avez pas de compte?', 'Ich habe noch kein Konto', 'У вас нет аккаунта?', '¿No tienes una cuenta?', 'Hesabınız yok mu?'),
(29, '', '', 'sign_up', 'Sign Up', 'سجل', 'Inschrijven', 'S\'inscrire', 'Anmelden', 'Подписаться', 'Regístrate', 'Kaydol'),
(30, '', '', 'or', 'OR', 'أو', 'OF', 'OU', 'ODER', 'ИЛИ ЖЕ', 'O', 'VEYA'),
(31, '', '', 'login_with_facebook', 'Login with Facebook', 'تسجيل الدخول باستخدام الفيسبوك', 'Inloggen met Facebook', 'Se connecter avec Facebook', 'Mit Facebook einloggen', 'Войти с Facebook', 'Iniciar sesión con Facebook', 'Facebook ile giriş'),
(32, '', '', 'login_with_twitter', 'Login with Twitter', 'تسجيل الدخول مع التغريد', 'Inloggen met Twitter', 'Se connecter avec Twitter', 'Mit Twitter anmelden', 'Войти через Twitter', 'Inicia sesión con Twitter', 'Twitter ile giriş yap'),
(33, '', '', 'login_with_google', 'Login with Google', 'تسجيل الدخول مع جوجل', 'Inloggen met Google', 'Connectez-vous avec Google', 'Mit Google anmelden', 'Войти через Google', 'Inicia sesión con Google', 'Google ile giriş yap'),
(34, '', '', 'login_with_vk', 'Login with VK', 'تسجيل الدخول مع VK', 'Inloggen met VK', 'Se connecter avec VK', 'Loggen Sie sich mit VK ein', 'Войти через ВКонтакте', 'Iniciar sesión con VK', 'VK ile giriş yap'),
(35, '', '', 'this_e-mail_is_already_taken', 'This e-mail is already taken', 'الايميل أخذ مسبقا', 'Dit e-mailadres is al in gebruik', 'Cet e-mail est déjà pris', 'Diese E-Mail ist schon vergeben', 'Это электронная почта уже используется', 'este correo electrónico ya está en uso', 'Bu e-posta zaten alınmış'),
(36, '', '', 'incorrect_username_or_password', 'Incorrect username or password', 'اسم المستخدم أو كلمة المرور غير صحيحة', 'foute gebruikersnaam of wachtwoord', 'identifiant ou mot de passe incorrect', 'Falscher Benutzername oder Passwort', 'Неверное имя пользователя или пароль', 'Nombre de usuario o contraseña incorrecta', 'Yanlış kullanıcı adı ya da parola'),
(37, '', '', 'registration_successful__we_have_sent_you_an_email__please_check_your_inbox_spam_to_verify_your_account.', 'Registration successful! We have sent you an email, Please check your inbox/spam to verify your account.', 'تم التسجيل بنجاح لقد أرسلنا لك رسالة بريد إلكتروني ، يرجى التحقق من البريد الوارد / البريد العشوائي للتحقق من حسابك.', 'Registratie gelukt! We hebben je een e-mail gestuurd. Controleer je inbox / spam om je account te verifiëren.', 'Inscription réussi! Nous vous avons envoyé un courrier électronique. Veuillez vérifier votre boîte de réception / spam pour vérifier votre compte.', 'Registrierung erfolgreich! Wir haben Ihnen eine E-Mail gesendet. Bitte überprüfen Sie Ihren Posteingang / Spam, um Ihr Konto zu bestätigen.', 'Регистрация прошла успешно! Мы отправили вам письмо, пожалуйста, проверьте свой почтовый ящик / спам, чтобы подтвердить свой аккаунт.', '¡Registro exitoso! Le hemos enviado un correo electrónico. Verifique su bandeja de entrada / correo no deseado para verificar su cuenta.', 'Kayıt başarılı! Size bir e-posta gönderdik, hesabınızı doğrulamak için lütfen gelen kutunuzu / spam’nizi kontrol edin.'),
(38, '', '', 'this_username_is_already_taken', 'This username is already taken', 'أسم المستخدم مأخوذ مسبقا', 'Deze gebruikersnaam is al in gebruik', 'Ce nom d\'utilisateur est déjà pris', 'Dieser Benutzername ist bereits vergeben', 'Это имя пользователя уже занято', 'Este nombre de usuario ya está en uso', 'Bu kullanıcı adı zaten alınmış'),
(39, '', '', 'your_account_is_not_activated_yet__please_check_your_inbox_for_the_activation_link', 'Your account is not activated yet, please check your inbox for the activation link', 'لم يتم تنشيط حسابك بعد ، يرجى التحقق من صندوق الوارد الخاص بك لمعرفة رابط التنشيط', 'Uw account is nog niet geactiveerd, controleer uw inbox voor de activatielink', 'Votre compte n\'est pas encore activé, veuillez vérifier dans votre boîte de réception le lien d\'activation.', 'Ihr Konto ist noch nicht aktiviert. Bitte überprüfen Sie Ihren Posteingang auf den Aktivierungslink', 'Ваша учетная запись еще не активирована, пожалуйста, проверьте свой почтовый ящик на ссылку активации', 'Su cuenta aún no está activada, por favor revise su bandeja de entrada para el enlace de activación', 'Hesabınız henüz aktif değil, lütfen aktivasyon bağlantısı için gelen kutunuzu kontrol edin.'),
(40, '', '', 'enter_your_email_to_get_password_reset_link.', 'Enter your email to get password reset link.', 'أدخل بريدك الإلكتروني للحصول على رابط إعادة تعيين كلمة المرور.', 'Voer je e-mailadres in om de link voor het opnieuw instellen van je wachtwoord te krijgen', 'Entrez votre email pour obtenir le lien de réinitialisation de mot de passe.', 'Geben Sie Ihre E-Mail-Adresse ein, um den Link zum Zurücksetzen des Passworts zu erhalten.', 'Введите адрес электронной почты, чтобы получить ссылку для сброса пароля.', 'Ingrese su correo electrónico para obtener el enlace de restablecimiento de contraseña.', 'Parola sıfırlama bağlantısını almak için e-postanızı girin.'),
(41, '', '', 'send_link', 'Send Link', 'أرسل الرابط', 'Stuur link', 'Envoyer un lien', 'Link senden', 'Отправить ссылку', 'Enviar un enlace', 'Link gönder'),
(42, '', '', 'this_e-mail_not_found', 'This E-mail not found', 'هذا البريد الإلكتروني غير موجود', 'Deze e-mail is niet gevonden', 'Cet e-mail introuvable', 'Diese E-Mail wurde nicht gefunden', 'Этот E-mail не найден', 'Este correo electrónico no encontrado', 'Bu E-posta bulunamadı'),
(43, '', '', 'this_e-mail_is_not_found', 'This e-mail is not found', 'لم يتم العثور على هذا البريد الإلكتروني', 'Deze e-mail kan niet gevonden worden', 'Cet e-mail est introuvable', 'Diese E-Mail wird nicht gefunden', 'Этот e-mail не найден', 'Este correo electrónico no se encuentra', 'Bu e-posta bulunamadı'),
(44, '', '', 'reset_password', 'Reset Password', 'إعادة تعيين كلمة المرور', 'Reset wachtwoord', 'réinitialiser le mot de passe', 'Passwort zurücksetzen', 'Сброс пароля', 'Restablecer la contraseña', 'Şifreyi yenile'),
(45, '', '', 'error_found_while_sending_the_reset_link__please_try_again_later.', 'Error found while sending the reset link, please try again later.', 'تم العثور على خطأ أثناء إرسال رابط إعادة التعيين ، يرجى المحاولة مرة أخرى لاحقًا.', 'Fout gevonden tijdens het verzenden van de reset-link. Probeer het later opnieuw.', 'Erreur détectée lors de l\'envoi du lien de réinitialisation, veuillez réessayer ultérieurement.', 'Beim Senden des Reset-Links wurde ein Fehler gefunden. Bitte versuchen Sie es später erneut.', 'Обнаружена ошибка при отправке ссылки на сброс, повторите попытку позже.', 'Se ha encontrado un error al enviar el enlace de restablecimiento. Inténtalo de nuevo más tarde.', 'Sıfırlama bağlantısını gönderirken hata bulundu, lütfen daha sonra tekrar deneyin.'),
(46, '', '', 'please_check_your_inbox___spam_folder_for_the_reset_email.', 'Please check your inbox / spam folder for the reset email.', 'يرجى التحقق من مجلد البريد الوارد / البريد العشوائي للحصول على إعادة تعيين البريد الإلكتروني.', 'Controleer alstublieft uw inbox / spam-map voor het e-mailadres voor het opnieuw instellen.', 'Veuillez vérifier votre boîte de réception / courrier indésirable pour le courrier électronique de réinitialisation.', 'Bitte überprüfen Sie Ihren Posteingang / Spam-Ordner auf die zurückgesetzte E-Mail.', 'Пожалуйста, проверьте папку «Входящие» / «Спам», чтобы узнать адрес электронной почты для сброса.', 'Por favor, revise su carpeta de bandeja de entrada / correo no deseado para el correo electrónico de restablecimiento.', 'Sıfırlama e-postası için lütfen gelen kutunuzu / spam klasörünüzü kontrol edin.'),
(47, '', '', 'please_check_your_details', 'Please check your details', 'يرجى التأكد من تفاصيل معلوماتك', 'Kijk alsjeblieft je gegevens na', 'S\'il vous plaît vérifier vos informations', 'Bitte überprüfe deine Details', 'Пожалуйста, проверьте ваши данные', 'Por favor comprueba tus detalles', 'Lütfen bilgilerinizi kontrol edin'),
(48, '', '', 'reset_your_password', 'Reset your password', 'اعد ضبط كلمه السر', 'Stel je wachtwoord opnieuw in', 'réinitialisez votre mot de passe', 'Setze dein Passwort zurück', 'Сбросить пароль', 'Restablecer su contraseña', 'Şifrenizi sıfırlayın'),
(49, '', '', 'enter_new_password_to_proceed.', 'Enter new password to proceed.', 'أدخل كلمة مرور جديدة للمتابعة.', 'Voer een nieuw wachtwoord in om door te gaan.', 'Entrez un nouveau mot de passe pour continuer.', 'Geben Sie ein neues Passwort ein, um fortzufahren.', 'Введите новый пароль, чтобы продолжить.', 'Introduzca la nueva contraseña para continuar.', 'Devam etmek için yeni şifre girin.'),
(50, '', '', 'new_password', 'New Password', 'كلمة السر الجديدة', 'nieuw paswoord', 'nouveau mot de passe', 'Neues Kennwort', 'новый пароль', 'Nueva contraseña', 'Yeni Şifre'),
(51, '', '', 'reset', 'Reset', 'إعادة تعيين', 'Reset', 'Réinitialiser', 'Zurücksetzen', 'Сброс', 'Reiniciar', 'Reset'),
(52, '', '', 'passwords_don_t_match', 'Passwords don&#039;t match', 'كلمات المرور غير متطابقة', 'Wachtwoorden komen niet overeen', 'Les mots de passe ne correspondent pas', 'Passwörter stimmen nicht überein', 'Пароли не совпадают', 'Las contraseñas no coinciden', 'Şifreler uyuşmuyor'),
(58, '', '', 'about_us', 'About Us', 'معلومات عنا', 'Over ons', 'À propos de nous', 'Über uns', 'Насчет нас', 'Sobre nosotros', 'Hakkımızda'),
(59, '', '', 'contact', 'Contact', 'اتصل', 'Contact', 'Contact', 'Kontakt', 'контакт', 'Contacto', 'Temas'),
(60, '', '', 'copyright_____date___name_.', 'Copyright © |DATE| |NAME|.', 'حقوق النشر © |DATE| |NAME|.', 'Copyright © |DATE| |NAME|.', 'Copyright © |DATE| |NAME|.', 'Copyright © |DATE| |NAME|.', 'Copyright © |DATE| |NAME|.', 'Copyright © |DATE| |NAME|.', 'Telif Hakkı © |DATE| |NAME|.'),
(61, '', '', 'contact_us', 'Contact Us', 'اتصل بنا', 'Neem contact met ons op', 'Contactez nous', 'Kontaktiere uns', 'Связаться с нами', 'Contáctenos', 'Bizimle iletişime geçin'),
(62, '', '', 'let_us_help_you.', 'Let us help you.', 'دعنا نساعدك.', 'Laat ons je helpen.', 'Laissez-nous vous aider.', 'Lass uns dir helfen.', 'Позвольте нам помочь вам.', 'Dejanos ayudarte.', 'Sana yardım edelim.'),
(63, '', '', 'write_here_your_message', 'Write here your message', 'أكتب هنا رسالتك', 'Schrijf hier je bericht', 'Ecrivez ici votre message', 'Schreiben Sie hier Ihre Nachricht', 'Напишите здесь ваше сообщение', 'Escribe aquí tu mensaje', 'Mesajını buraya yaz'),
(64, '', '', 'send', 'Send', 'إرسال', 'Sturen', 'Envoyer', 'Senden', 'послать', 'Enviar', 'göndermek'),
(65, '', '', 'e-mail_sent_successfully', 'E-mail sent successfully', 'تم إرسال البريد الإلكتروني بنجاح', 'E-mail succesvol verzonden', 'E-mail envoyé avec succès', 'Email wurde erfolgreich Versendet', 'Письмо успешно отправлено', 'E-mail enviado correctamente', 'E-posta başarıyla gönderildi'),
(66, '', '', 'followers', 'Followers', 'متابعون', 'Volgers', 'Suiveurs', 'Anhänger', 'Читают', 'Seguidores', 'İzleyiciler'),
(67, '', '', 'following', 'Following', 'التالية', 'Volgend op', 'Suivant', 'Folgenden', 'Следующий', 'Siguiendo', 'Takip etme'),
(68, '', '', 'all', 'All', 'الكل', 'Allemaal', 'Tout', 'Alles', 'Все', 'Todos', 'Herşey'),
(69, '', '', 'songs', 'Songs', 'الأغاني', 'songs', 'Chansons', 'Lieder', 'песни', 'Canciones', 'Şarkılar'),
(70, '', '', 'albums', 'Albums', 'ألبومات', 'albums', 'Albums', 'Alben', 'Альбомы', 'Los álbumes', 'albümler'),
(71, '', '', 'playlists', 'Playlists', 'قوائم التشغيل', 'afspeellijsten', 'Playlists', 'Wiedergabelisten', 'Плейлисты', 'Listas de reproducción', 'Çalma listeleri'),
(72, '', '', 'follow', 'Follow', 'إتبع', 'Volgen', 'Suivre', 'Folgen', 'следить', 'Seguir', 'Takip et'),
(73, '', '', 'edit_profile', 'Edit Profile', 'تعديل الملف الشخصي', 'Bewerk profiel', 'Editer le profil', 'Profil bearbeiten', 'Редактировать профиль', 'Editar perfil', 'Profili Düzenle'),
(74, '', '', 'confirm_your_account', 'Confirm your account', 'اكد حسابك', 'Bevestig je account', 'Confirmez votre compte', 'Bestätigen Sie ihr Konto', 'Подтвердите свой аккаунт', 'Confirme su cuenta', 'Hesabını onayla'),
(75, '', '', 'general_settings', 'General Settings', 'الاعدادات العامة', 'Algemene instellingen', 'réglages généraux', 'Allgemeine Einstellungen', 'общие настройки', 'Configuración general', 'Genel Ayarlar'),
(76, '', '', 'email', 'Email', 'البريد الإلكتروني', 'E-mail', 'Email', 'Email', 'Эл. адрес', 'Email', 'E-posta'),
(77, '', '', 'country', 'Country', 'بلد', 'land', 'Pays', 'Land', 'Страна', 'País', 'ülke'),
(78, '', '', 'age', 'Age', 'عمر', 'Leeftijd', 'Âge', 'Alter', 'Возраст', 'Años', 'Yaş'),
(79, '', '', 'gender', 'Gender', 'جنس', 'Geslacht', 'Le sexe', 'Geschlecht', 'Пол', 'Género', 'Cinsiyet'),
(80, '', '', 'save', 'Save', 'حفظ', 'Opslaan', 'sauvegarder', 'sparen', 'Сохранить', 'Salvar', 'Kayıt etmek'),
(81, '', '', 'delete_account', 'Delete Account', 'حذف الحساب', 'Account verwijderen', 'Supprimer le compte', 'Konto löschen', 'Удалить аккаунт', 'Borrar cuenta', 'Hesabı sil'),
(82, '', '', 'are_you_sure_you_want_to_delete_your_account__all_content_including_published_songs__will_be_permanetly_removed_', 'Are you sure you want to delete your account? All content including published songs, will be permanetly removed!', 'هل انت متأكد انك تريد حذف حسابك؟ ستتم إزالة جميع المحتويات بما في ذلك الأغاني المنشورة نهائيًا!', 'Weet je zeker dat je je account wilt verwijderen? Alle inhoud, inclusief gepubliceerde nummers, wordt permanent verwijderd!', 'Êtes-vous sûr de vouloir supprimer votre compte? Tout le contenu, y compris les chansons publiées, sera définitivement supprimé!', 'Möchten Sie Ihr Konto wirklich löschen? Alle Inhalte einschließlich der veröffentlichten Songs werden dauerhaft entfernt!', 'Вы уверены, что хотите удалить свой аккаунт? Весь контент, включая опубликованные песни, будет окончательно удален!', '¿Estás seguro de que quieres eliminar tu cuenta? Todo el contenido, incluidas las canciones publicadas, se eliminará permanentemente!', 'Hesabınızı silmek istediğinizden emin misiniz? Yayınlanan şarkılar dahil tüm içerikler kalıcı olarak kaldırılacak!'),
(83, '', '', 'current_password', 'Current Password', 'كلمة المرور الحالي', 'huidig ​​wachtwoord', 'Mot de passe actuel', 'derzeitiges Passwort', 'Текущий пароль', 'contraseña actual', 'Şimdiki Şifre'),
(84, '', '', 'delete', 'Delete', 'حذف', 'Verwijder', 'Effacer', 'Löschen', 'удалять', 'Borrar', 'silmek'),
(85, '', '', 'change_password', 'Change Password', 'غير كلمة السر', 'Wachtwoord wijzigen', 'Changer le mot de passe', 'Ändere das Passwort', 'Изменить пароль', 'Cambia la contraseña', 'Şifre değiştir'),
(86, '', '', 'repeat_new_password', 'Repeat New Password', 'كرر كلمة المرور الجديدة', 'Herhaal nieuw wachtwoord', 'Répété le nouveau mot de passe', 'Wiederhole das neue Passwort', 'Повторите новый пароль', 'Repita la nueva contraseña', 'Yeni şifreyi tekrar girin'),
(87, '', '', 'change', 'Change', 'يتغيرون', 'Verandering', 'Changement', 'Veränderung', '+ Изменить', 'Cambio', 'Değişiklik'),
(88, '', '', 'profile_settings', 'Profile Settings', 'إعدادات الملف الشخصي', 'Profielinstellingen', 'Paramètres de profil', 'Profileinstellungen', 'Настройки профиля', 'Configuración de perfil', 'Profil ayarları'),
(89, '', '', 'about_me', 'About Me', 'عني', 'Over mij', 'À propos de moi', 'Über mich', 'Обо мне', 'Sobre mi', 'Benim hakkımda'),
(90, '', '', 'facebook_username', 'Facebook Username', 'اسم مستخدم Facebook', 'Facebook gebruikersnaam', 'Nom d\'utilisateur Facebook', 'Facebook-Benutzername', 'Facebook Имя пользователя', 'Nombre de usuario de Facebook', 'Facebook Kullanıcı Adı'),
(91, '', '', 'website', 'Website', 'موقع الكتروني', 'Website', 'Site Internet', 'Webseite', 'Веб-сайт', 'Sitio web', 'Web sitesi'),
(94, '', '', 'male', 'Male', 'الذكر', 'Mannetje', 'Mâle', 'Männlich', 'мужчина', 'Masculino', 'Erkek'),
(95, '', '', 'female', 'Female', 'إناثا', 'Vrouw', 'Femelle', 'Weiblich', 'женский', 'Hembra', 'Kadın'),
(96, '', '', 'settings_successfully_updated_', 'Settings successfully updated!', 'تم تحديث الإعدادات بنجاح!', 'Instellingen succesvol bijgewerkt!', 'Paramètres mis à jour avec succès!', 'Einstellungen erfolgreich aktualisiert!', 'Настройки успешно обновлены!', 'Configuraciones exitosamente actualizadas!', 'Ayarlar başarıyla güncellendi!'),
(97, '', '', 'no_notifications_found', 'No notifications found', 'لا توجد إخطارات', 'Geen meldingen gevonden', 'Aucune notification trouvée', 'Keine Benachrichtigungen gefunden', 'Уведомления не найдены', 'No se encontraron notificaciones', 'Bildirim bulunamadı'),
(98, '', '', 'year', 'year', 'عام', 'jaar', 'année', 'Jahr', 'год', 'año', 'yıl'),
(99, '', '', 'month', 'month', 'شهر', 'maand', 'mois', 'Monat', 'месяц', 'mes', 'ay'),
(100, '', '', 'day', 'day', 'يوم', 'dag', 'journée', 'Tag', 'день', 'día', 'gün'),
(101, '', '', 'hour', 'hour', 'ساعة', 'uur', 'heure', 'Stunde', 'час', 'hora', 'saat'),
(102, '', '', 'minute', 'minute', 'اللحظة', 'minuut', 'minute', 'Minute', 'минут', 'minuto', 'dakika'),
(103, '', '', 'second', 'second', 'ثانيا', 'tweede', 'seconde', 'zweite', 'второй', 'segundo', 'ikinci'),
(104, '', '', 'years', 'years', 'سنوات', 'jaar', 'années', 'Jahre', 'лет', 'años', 'yıl'),
(105, '', '', 'months', 'months', 'الشهور', 'maanden', 'mois', 'Monate', 'месяцы', 'meses', 'ay'),
(106, '', '', 'days', 'days', 'أيام', 'dagen', 'journées', 'Tage', 'дней', 'dias', 'günler'),
(107, '', '', 'hours', 'hours', 'ساعات', 'uur', 'heures', 'Std', 'часов', 'horas', 'saatler'),
(108, '', '', 'minutes', 'minutes', 'الدقائق', 'notulen', 'minutes', 'Protokoll', 'минут', 'minutos', 'dakika'),
(109, '', '', 'seconds', 'seconds', 'ثواني', 'seconden', 'secondes', 'Sekunden', 'секунд', 'segundos', 'saniye'),
(110, '', '', 'ago', 'ago', 'منذ', 'geleden', 'depuis', 'vor', 'тому назад', 'hace', 'önce'),
(111, '', '', 'started_following_you.', 'started following you.', 'بدات الاحقك.', 'begon jou te volgen.', 'commencé à te suivre.', 'Begann dir zu folgen.', 'начал следить за тобой.', 'empecé a seguirte.', 'seni takip etmeye başladım.'),
(112, '', '', 'profile_successfully_updated_', 'Profile successfully updated!', 'تم تحديث الملف الشخصي بنجاح!', 'Profiel met succes bijgewerkt!', 'Profil mis à jour avec succès!', 'Profil erfolgreich aktualisiert!', 'Профиль успешно обновлен!', 'Perfil actualizado con éxito!', 'Profil başarıyla güncellendi!'),
(113, '', '', 'invalid_website_url__format_allowed__http_s_____.___', 'Invalid website url, format allowed: http(s)://*.*/*', 'عنوان URL غير صالح لموقع الويب ، التنسيق المسموح به: http (s): //*.*/*', 'Ongeldige website-URL, toegestane indeling: http (s): //*.*/*', 'URL de site Web non valide, format autorisé: http (s): //*.*/*', 'Ungültige Website-URL, Format zulässig: http (s): //*.*/*', 'Неверный URL сайта, допустимый формат: http (s): //*.*/*', 'URL del sitio web no válida, formato permitido: http (s): //*.*/*', 'Geçersiz web sitesi URL\'si, biçime izin verilir: http: s: //*.*/*'),
(114, '', '', 'invalid_facebook_username__urls_are_not_allowed', 'Invalid facebook username, urls are not allowed', 'اسم مستخدم facebook غير صالح ، غير مسموح باستخدام عناوين url', 'Ongeldige facebook-gebruikersnaam, URL\'s zijn niet toegestaan', 'Nom d\'utilisateur facebook non valide, les URL ne sont pas autorisées', 'Ungültiger Facebook-Benutzername, URLs sind nicht zulässig', 'Неверное имя пользователя в Facebook, URL не разрешены', 'Nombre de usuario de Facebook no válido, las URL no están permitidas', 'Geçersiz facebook kullanıcı adı, URL\'lere izin verilmiyor'),
(115, '', '', 'new_password_is_too_short', 'New password is too short', 'كلمة المرور الجديدة قصيرة جدًا', 'Nieuw wachtwoord is te kort', 'Le nouveau mot de passe est trop court', 'Neues Passwort ist zu kurz', 'Новый пароль слишком короткий', 'La nueva contraseña es demasiado corta', 'Yeni şifre çok kısa'),
(116, '', '', 'your_current_password_is_invalid', 'Your current password is invalid', 'كلمة المرور الحالية غير صالحة', 'Uw huidige wachtwoord is ongeldig', 'Votre mot de passe actuel est invalide', 'Ihr aktuelles Passwort ist ungültig', 'Ваш текущий пароль недействителен', 'Tu contraseña actual no es válida', 'Mevcut şifreniz geçersiz'),
(117, '', '', 'your_password_was_successfully_updated_', 'Your password was successfully updated!', 'تم تحديث كلمة مرورك بنجاح!', 'Uw wachtwoord is succesvol bijgewerkt!', 'Votre mot de passe a été mis à jour avec succès!', 'Ihr Passwort wurde erfolgreich aktualisiert!', 'Ваш пароль был успешно обновлен!', 'Su contraseña fue actualizada con éxito!', 'Şifreniz başarıyla güncellendi!'),
(118, '', '', 'your_account_was_successfully_deleted__please_wait..', 'Your account was successfully deleted, please wait..', 'تم حذف حسابك بنجاح ، يرجى الانتظار ..', 'Uw account is succesvol verwijderd, even geduld aub ..', 'Votre compte a bien été supprimé, veuillez patienter ..', 'Ihr Konto wurde erfolgreich gelöscht. Bitte warten Sie ..', 'Ваша учетная запись была успешно удалена, пожалуйста, подождите ..', 'Su cuenta fue eliminada exitosamente, por favor espere ..', 'Hesabınız başarıyla silindi, lütfen bekleyin ..'),
(119, '', '', 'select_files_to_upload', 'Select files to upload', 'حدد الملفات المراد تحميلها', 'Selecteer bestanden om te uploaden', 'Sélectionnez les fichiers à télécharger', 'Wählen Sie die Dateien zum Hochladen aus', 'Выберите файлы для загрузки', 'Seleccionar archivos para subir', 'Yüklenecek dosyaları seçin'),
(120, '', '', 'or_drag___drop_files_here', 'or drag &amp; drop files here', 'أو اسحب &amp; قم بوضع الملفات هنا', 'of sleep &amp; zet hier bestanden neer', 'ou faites glisser &amp; Déposez les fichiers ici', 'oder ziehen Sie &amp; Dateien hier ablegen', 'или перетащите &amp; перетащите файлы сюда', 'o arrastrar &amp; soltar archivos aquí', 'veya sürükle &amp; dosyaları buraya bırak'),
(121, '', '', 'title', 'Title', 'عنوان', 'Titel', 'Titre', 'Titel', 'заглавие', 'Título', 'Başlık'),
(122, '', '', 'your_song_title__2_-_55_characters', 'Your song title, 2 - 55 characters', 'عنوان أغنيتك ، 2 - 55 حرفًا', 'De titel van je nummer, 2 - 55 tekens', 'Le titre de votre chanson, 2 - 55 caractères', 'Ihr Songtitel, 2 - 55 Zeichen', 'Название вашей песни, 2 - 55 символов', 'El título de tu canción, 2 - 55 caracteres.', 'Şarkının adı, 2 - 55 karakter'),
(123, '', '', 'description', 'Description', 'وصف', 'Omschrijving', 'La description', 'Beschreibung', 'Описание', 'Descripción', 'Açıklama'),
(124, '', '', 'tags', 'Tags', 'الكلمات', 'Tags', 'Mots clés', 'Stichworte', 'Теги', 'Etiquetas', 'Etiketler'),
(125, '', '', 'add_tags_to_describe_more_about_your_track', 'Add tags to describe more about your track', 'أضف علامات لوصف المزيد عن المسار الخاص بك', 'Voeg tags toe om meer over je nummer te beschrijven', 'Ajoutez des tags pour décrire plus en détail votre piste', 'Fügen Sie Tags hinzu, um mehr über Ihren Track zu beschreiben', 'Добавьте теги, чтобы описать больше о вашем треке', 'Agrega etiquetas para describir más sobre tu pista', 'Parçanız hakkında daha fazla açıklama yapmak için etiketler ekleyin'),
(126, '', '', 'genre', 'Genre', 'نوع أدبي', 'Genre', 'Genre', 'Genre', 'Жанр', 'Género', 'Tür'),
(127, '', '', 'availability', 'Availability', 'توفر', 'Beschikbaarheid', 'Disponibilité', 'Verfügbarkeit', 'Доступность', 'Disponibilidad', 'Kullanılabilirlik'),
(128, '', '', 'public', 'Public', 'عامة', 'Openbaar', 'Publique', 'Öffentlichkeit', 'общественного', 'Público', 'halka açık'),
(129, '', '', 'private', 'Private', 'نشر', 'Privaat', 'Privé', 'Privatgelände', 'Частный', 'Privado', 'Özel'),
(130, '', '', 'age_restriction', 'Age Restriction', 'شرط العمر أو السن', 'Leeftijdsbeperking', 'Restriction d\'âge', 'Altersbeschränkung', 'Ограничение по возрасту', 'Restricción de edad', 'Yaş kısıtlaması'),
(131, '', '', 'all_ages_can_listen_this_song', 'All ages can listen this song', 'يمكن لجميع الأعمار الاستماع إلى هذه الأغنية', 'Alle leeftijden kunnen dit liedje beluisteren', 'Tous les âges peuvent écouter cette chanson', 'Alle Altersgruppen können dieses Lied hören', 'Все возрасты могут слушать эту песню', 'Todas las edades pueden escuchar esta canción.', 'Her şarkı bu şarkıyı dinleyebilir'),
(132, '', '', 'only__18', 'Only +18', 'فقط +18', 'Alleen +18', 'Seulement +18', 'Nur +18', 'Только +18', 'Solo +18', 'Sadece +18'),
(133, '', '', 'price', 'Price', 'السعر', 'Prijs', 'Prix', 'Preis', 'Цена', 'Precio', 'Fiyat'),
(134, '', '', 'publish', 'Publish', 'نشر', 'Publiceren', 'Publier', 'Veröffentlichen', 'Публиковать', 'Publicar', 'Yayınla'),
(135, '', '', 'audio_file_not_found__please_refresh_the_page_and_try_again.', 'Audio file not found, please refresh the page and try again.', 'لم يتم العثور على الملف الصوتي ، يرجى تحديث الصفحة والمحاولة مرة أخرى.', 'Audiobestand niet gevonden, vernieuw de pagina en probeer het opnieuw.', 'Fichier audio non trouvé, veuillez actualiser la page et réessayer.', 'Audiodatei nicht gefunden. Aktualisieren Sie die Seite und versuchen Sie es erneut.', 'Аудио файл не найден, обновите страницу и попробуйте снова.', 'No se encontró el archivo de audio, por favor actualice la página y vuelva a intentarlo.', 'Ses dosyası bulunamadı, lütfen sayfayı yenileyin ve tekrar deneyin.'),
(136, '', '', 'something_went_wrong_please_try_again_later_', 'Something went wrong Please try again later!', 'هناك شئ خاطئ، يرجى المحاولة فى وقت لاحق!', 'Er is iets misgegaan Probeer het later opnieuw!', 'Quelque chose c\'est mal passé. Merci d\'essayer plus tard!', 'Etwas ist schief gelaufen. Bitte versuchen Sie es später noch einmal!', 'Что-то пошло не так. Пожалуйста, повторите попытку позже!', 'Algo salió mal Por favor, intente de nuevo más tarde!', 'Bir şeyler yanlış oldu. Lütfen sonra tekrar deneyiniz!'),
(138, '', '', 'please_wait__your_track_is_being_coverted_to_mp3_audio_file._this_might_take_a_few_minutes.', 'Please wait, your track is being coverted to mp3 audio file. This might take a few minutes.', 'الرجاء الانتظار ، يتم إخفاء المسار الخاص بك إلى ملف صوتي MP3. وهذا قد يستغرق بضع دقائق.', 'Een ogenblik geduld, je nummer wordt omspoeld naar mp3-audiobestand. Dit kan een paar minuten duren.', 'Veuillez patienter, votre piste est convertie en fichier audio mp3. Ceci pourrait prendre quelques minutes.', 'Bitte warten Sie, Ihr Track wird in eine MP3-Audiodatei umgewandelt. Dies könnte ein paar Minuten dauern.', 'Пожалуйста, подождите, ваш трек записывается в mp3-файл. Это может занять несколько минут.', 'Por favor espere, su pista está siendo cubierta a un archivo de audio mp3. Esto puede tardar unos minutos.', 'Lütfen bekleyin, parçanız mp3 ses dosyasına dönüştürülüyor. Bu, birkaç dakika sürebilir.'),
(139, '', '', 'invalid_file_format__only_mp3_is_allowed', 'Invalid file format, only mp3 is allowed', 'تنسيق ملف غير صالح ، يُسمح بتنسيق mp3 فقط', 'Ongeldige bestandsindeling, alleen mp3 is toegestaan', 'Format de fichier invalide, seul le format mp3 est autorisé', 'Ungültiges Dateiformat, nur MP3 ist zulässig', 'Неверный формат файла, разрешен только mp3', 'Formato de archivo inválido, solo se permite mp3', 'Geçersiz dosya formatı, sadece mp3 izin verilir'),
(140, '', '', 'invalid_file_format__only_jpg__jpeg__png_are_allowed', 'Invalid file format, only jpg, jpeg, png are allowed', 'تنسيق ملف غير صالح ، يُسمح فقط بتنسيق jpg و jpeg و png', 'Ongeldige bestandsindeling, alleen jpg, jpeg, png zijn toegestaan', 'Format de fichier invalide, seuls les formats jpg, jpeg, png sont autorisés', 'Ungültiges Dateiformat, nur jpg, jpeg, png sind zulässig', 'Неверный формат файла, разрешены только jpg, jpeg, png', 'Formato de archivo no válido, solo se permiten jpg, jpeg, png', 'Geçersiz dosya formatı, sadece jpg, jpeg, png izin verilir'),
(141, '', '', 'error_found_while_uploading_your_image__please_try_again_later.', 'Error found while uploading your image, please try again later.', 'تم العثور على خطأ أثناء تحميل صورتك ، يرجى إعادة المحاولة لاحقًا.', 'Fout gevonden tijdens het uploaden van uw afbeelding. Probeer het later opnieuw.', 'Une erreur a été détectée lors du téléchargement de votre image. Veuillez réessayer ultérieurement.', 'Beim Hochladen des Bildes wurde ein Fehler gefunden. Bitte versuchen Sie es später erneut.', 'При загрузке изображения обнаружена ошибка. Повторите попытку позже.', 'Se ha encontrado un error al cargar tu imagen, inténtalo de nuevo más tarde.', 'Resminizi yüklerken hata bulundu, lütfen daha sonra tekrar deneyin.'),
(142, '', '', 'error_found_while_uploading_your_track__please_try_again_later.', 'Error found while uploading your track, please try again later.', 'تم العثور على خطأ أثناء تحميل المسار ، يرجى المحاولة مرة أخرى لاحقًا.', 'Fout gevonden tijdens het uploaden van je nummer, probeer het later opnieuw.', 'Une erreur a été détectée lors du téléchargement de votre piste. Veuillez réessayer ultérieurement.', 'Fehler beim Hochladen Ihres Tracks. Bitte versuchen Sie es später erneut.', 'При загрузке трека обнаружена ошибка. Повторите попытку позже.', 'Se ha encontrado un error al cargar la pista. Inténtalo de nuevo más tarde.', 'Parça yüklenirken hata bulundu, lütfen daha sonra tekrar deneyin.'),
(143, '', '', 'invalid_file_format__only_mp3__ogg__wav__and_mpeg_is_allowed', 'Invalid file format, only mp3, ogg, wav, and mpeg is allowed', 'يُسمح بتنسيق ملف غير صالح وملفات mp3 و ogg و wav و mpeg فقط', 'Ongeldige bestandsindeling, alleen mp3, ogg, wav en mpeg is toegestaan', 'Format de fichier invalide, seuls les fichiers mp3, ogg, wav et mpeg sont autorisés', 'Ungültiges Dateiformat, nur mp3, ogg, wav und mpeg ist zulässig', 'Неверный формат файла, разрешены только mp3, ogg, wav и mpeg', 'Formato de archivo no válido, solo se permite mp3, ogg, wav y mpeg', 'Geçersiz dosya formatı, yalnızca mp3, ogg, wav ve mpeg dosyalarına izin verilir'),
(144, '', '', 'sorry__page_not_found_', 'Sorry, page not found!', 'عذرا، لم يتم العثور على الصفحة!', 'Sorry, pagina niet gevonden!', 'Désolé, page non trouvée!', 'Entschuldigung, Seite nicht gefunden!', 'Извините, страница не найдена!', 'Lo sentimos, la página no se encuentra!', 'Üzgünüz, sayfa bulunamadı!'),
(145, '', '', 'the_page_you_are_looking_for_could_not_be_found._please_check_the_link_you_followed_to_get_here_and_try_again.', 'The page you are looking for could not be found. Please check the link you followed to get here and try again.', 'لا يمكن العثور على الصفحة التي تبحث عنها. يرجى التحقق من الرابط الذي اتبعته للوصول إلى هنا والمحاولة مرة أخرى.', 'De pagina waarnaar u op zoek bent, kon niet worden gevonden. Controleer de link die je hebt gevolgd om hier te komen en probeer het opnieuw.', 'La page que vous recherchez n\'a pu être trouvée. Veuillez vérifier le lien que vous avez suivi pour arriver ici et réessayer.', 'Die von Ihnen gesuchte Seite wurde nicht gefunden. Bitte überprüfen Sie den Link, den Sie folgten, um hierher zu gelangen und es erneut zu versuchen.', 'Страница, которую вы ищете, не может быть найдена. Пожалуйста, проверьте ссылку, по которой вы перешли, и попробуйте снова.', 'La página que estás buscando no se pudo encontrar. Por favor revise el enlace que siguió para llegar aquí e intente nuevamente.', 'Aradığınız sayfa bulunamadı. Lütfen buraya gelip tekrar denediğiniz bağlantıyı kontrol edin.'),
(146, '', '', 'home', 'Home', 'الصفحة الرئيسية', 'Huis', 'Accueil', 'Zuhause', 'Главная', 'Casa', 'Ev'),
(147, '', '', 'become_an_artist', 'Become an artist', 'تصبح فنانا', 'Word een artiest', 'Devenir artiste', 'Künstler werden', 'Стать художником', 'Conviértete en un artista', 'Bir sanatçı ol'),
(148, '', '', 'info', 'Info', 'معلومات', 'info', 'Info', 'Info', 'Информация', 'Información', 'Bilgi'),
(149, '', '', 'located_in', 'Located in', 'يقع في', 'Gevestigd in', 'Situé dans', 'Gelegen in', 'Находится в', 'Situado en', 'Konumlanmış'),
(150, '', '', 'bio', 'Bio', 'السيرة الذاتية', 'Bio', 'Bio', 'Bio', 'Bio', 'Bio', 'biyo'),
(151, '', '', 'social_links', 'Social Links', 'روابط اجتماعية', 'Sociale links', 'Liens sociaux', 'Soziale Links', 'Социальные ссылки', 'vínculos sociales', 'Sosyal bağlantılar'),
(152, '', '', '__user_gender', '{{USER gender}}', '{{USER gender}}', '{{USER gender}}', '{{USER gender}}', '{{USER gender}}', '{{USER gender}}', '{{USER gender}}', '{{USER gender}}'),
(153, '', '', 'release_date', 'Release date', 'يوم الاصدار', 'Datum van publicatie', 'Date de sortie', 'Veröffentlichungsdatum', 'Дата выхода', 'Fecha de lanzamiento', 'Yayın tarihi'),
(154, '', '', 'uploaded_new_song', 'Uploaded new song', 'تم تحميل أغنية جديدة', 'Nieuw liedje geüpload', 'Nouvelle chanson téléchargée', 'Neues Lied hochgeladen', 'Загрузил новую песню', 'Subido nueva canción', 'Yeni şarkı yüklendi'),
(155, '', '', 'report', 'Report', 'أبلغ عن', 'Verslag doen van', 'rapport', 'Bericht', 'отчет', 'Informe', 'Rapor'),
(156, '', '', 'delete_track', 'Delete Track', 'حذف المسار', 'Track verwijderen', 'Supprimer la piste', 'Track löschen', 'Удалить трек', 'Eliminar pista', 'Parçayı Sil'),
(157, '', '', 'edit_info', 'Edit Info', 'تحرير المعلومات', 'Bewerk informatie', 'Modifier les informations', 'Bearbeitungs Information', 'Изменить информацию', 'Editar información', 'Bilgi düzenle'),
(158, '', '', 'pin', 'Pin', 'دبوس', 'Pin', 'Épingle', 'Stift', 'Штырь', 'Alfiler', 'Toplu iğne'),
(159, '', '', 'no_tracks_found', 'No tracks found', 'لم يتم العثور على المسارات', 'Geen nummers gevonden', 'Aucune piste trouvée', 'Keine Tracks gefunden', 'Треки не найдены', 'No se encontraron pistas', 'Hiçbir parça bulunamadı'),
(160, '', '', 'load_more', 'Load More', 'تحميل المزيد', 'Meer laden', 'Charger plus', 'Mehr laden', 'Загрузи больше', 'Carga más', 'Daha fazla yükle'),
(161, '', '', 'no_more_tracks_found', 'No more tracks found', 'لم يتم العثور على المزيد من المسارات', 'Geen nummers meer gevonden', 'Pas plus de pistes trouvées', 'Keine weiteren Titel gefunden', 'Больше треков не найдено', 'No se encontraron más pistas', 'Başka parça bulunamadı'),
(162, '', '', 'like', 'Like', 'مثل', 'Net zoals', 'Comme', 'Mögen', 'подобно', 'Me gusta', 'Sevmek'),
(163, '', '', 'share', 'Share', 'شارك', 'Delen', 'Partager', 'Aktie', 'Поделиться', 'Compartir', 'Pay'),
(164, '', '', 'more', 'More', 'أكثر من', 'Meer', 'Plus', 'Mehr', 'Больше', 'Más', 'Daha'),
(165, '', '', 'add_to_playlist', 'Add to Playlist', 'أضف إلى قائمة التشغيل', 'Toevoegen aan afspeellijst', 'Ajouter à la playlist', 'Zur Titelliste hinzufügen', 'Добавить в плейлист', 'Agregar a la lista de reproducción', 'Oynatma listesine ekle'),
(166, '', '', 'add_to_queue', 'Add to Queue', 'إضافة إلى قائمة الانتظار', 'Toevoegen aan wachtrij', 'Ajouter à la liste', 'Zur Warteschlange hinzufügen', 'Добавить в очередь', 'Añadir a la cola', 'Sıraya ekle'),
(167, '', '', 'edit', 'Edit', 'تصحيح', 'Bewerk', 'modifier', 'Bearbeiten', 'редактировать', 'Editar', 'Düzenle'),
(168, '', '', 'download', 'Download', 'تحميل', 'Download', 'Télécharger', 'Herunterladen', 'Скачать', 'Descargar', 'İndir'),
(169, '', '', 'purchase', 'Purchase', 'شراء', 'Aankoop', 'achat', 'Kauf', 'покупка', 'Compra', 'Satın alma'),
(170, '', '', 'save_track', 'Save Track', 'حفظ المسار', 'Track opslaan', 'Enregistrer la piste', 'Track speichern', 'Сохранить трек', 'Guardar track', 'Parçayı Kaydet'),
(171, '', '', 'the_new_track_details_are_updated__please_wait..', 'The new track details are updated, please wait..', 'يتم تحديث تفاصيل المسار الجديد ، يرجى الانتظار ..', 'De nieuwe trackdetails zijn bijgewerkt, even geduld aub ..', 'Les détails de la nouvelle piste sont mis à jour, veuillez patienter ..', 'Die neuen Track-Details werden aktualisiert, bitte warten ..', 'Обновлены подробности нового трека, пожалуйста, подождите ..', 'Los nuevos detalles de la pista se actualizan, por favor espere ...', 'Yeni parça detayları güncellendi, lütfen bekleyin ..'),
(172, '', '', 'liked_your_song.', 'liked your song.', 'اعجبتك اغنيتك', 'vond je liedje leuk.', 'aimé votre chanson.', 'mochte dein Lied', 'понравилась твоя песня', 'Me gustó tu canción.', 'şarkını beğendim'),
(173, '', '', 'liked', 'Liked', 'احب', 'vond', 'Aimé', 'Gefallen', 'Понравилось', 'Gustó', 'sevilen'),
(174, '', '', 'write_a_comment_and_press_enter', 'Write a comment and press enter', 'اكتب تعليقًا واضغط على إدخال', 'Schrijf een opmerking en druk op Enter', 'Écrivez un commentaire et appuyez sur Entrée', 'Schreiben Sie einen Kommentar und drücken Sie die Eingabetaste', 'Напишите комментарий и нажмите ввод', 'Escribe un comentario y presiona enter', 'Bir yorum yaz ve enter tuşuna basın'),
(175, '', '', 'delete_your_track', 'Delete your track', 'حذف المسار الخاص بك', 'Wis je nummer', 'Supprimer votre piste', 'Löschen Sie Ihre Spur', 'Удалить свой трек', 'Borra tu pista', 'Parçanı sil'),
(176, '', '', 'are_you_sure_you_want_to_delete_this_track_', 'Are you sure you want to delete this track?', 'هل تريد بالتأكيد حذف هذا المسار؟', 'Weet je zeker dat je deze track wilt verwijderen?', 'Êtes-vous sûr de vouloir supprimer cette piste?', 'Möchten Sie diesen Titel wirklich löschen?', 'Вы уверены, что хотите удалить этот трек?', '¿Seguro que quieres borrar esta pista?', 'Bu parçayı silmek istediğinize emin misiniz?'),
(177, '', '', 'cancel', 'Cancel', 'إلغاء', 'annuleren', 'Annuler', 'Stornieren', 'отменить', 'Cancelar', 'İptal etmek'),
(178, '', '', 'share_this_song', 'Share this Song', 'شارك هذه الأغنية', 'Deel deze song', 'Partager cette chanson', 'Teile diesen Song', 'Поделитесь этой песней', 'Comparte esta canción', 'Bu Şarkıyı Paylaş'),
(179, '', '', 'close', 'Close', 'قريب', 'Dichtbij', 'Fermer', 'Schließen', 'близко', 'Cerrar', 'Kapat'),
(180, '', '', 'tracks', 'Tracks', 'المسارات', 'Sporen', 'Des pistes', 'Spuren', 'Дорожки', 'Pistas', 'raylar'),
(181, '', '', 'recently_played_music', 'Recently Played Music', 'الموسيقى التي تم تشغيلها مؤخرًا', 'Onlangs gespeelde muziek', 'Musique récemment jouée', 'Kürzlich gespielte Musik', 'Недавно сыгранная музыка', 'Música recientemente reproducida', 'Son Çalınan Müzik'),
(182, '', '', 'no_tracks_found__try_to_listen_more____', 'No tracks found, try to listen more? ;)', 'لم يتم العثور على مقطوعات ، حاول الاستماع أكثر؟ ؛)', 'Geen nummers gevonden, probeer je meer te luisteren? ;)', 'Aucune piste trouvée, essayez d\'écouter plus? ;)', 'Keine Titel gefunden, versuchen Sie mehr zu hören? ;)', 'Треков не найдено, попробуйте послушать больше? ;)', 'No se han encontrado pistas, intenta escuchar más? ;)', 'Hiçbir parça bulunamadı, daha fazla dinlemeye çalışın mı? ;)'),
(183, '', '', 'repeat', 'Repeat', 'كرر', 'Herhaling', 'Répéter', 'Wiederholen', 'Повторение', 'Repetir', 'Tekrar et'),
(184, '', '', 'shuffle', 'Shuffle', 'خلط', 'schuifelen', 'Mélanger', 'Mischen', 'шарканье', 'Barajar', 'Karıştır'),
(185, '', '', 'queue', 'Queue', 'طابور', 'Wachtrij', 'Queue', 'Warteschlange', 'Очередь', 'Cola', 'kuyruk'),
(186, '', '', 'clear', 'Clear', 'واضح', 'Duidelijk', 'Clair', 'klar', 'Очистить', 'Claro', 'Açık'),
(187, '', '', 'just_now', 'Just now', 'في هذة اللحظة', 'Net nu', 'Juste maintenant', 'Gerade jetzt', 'Прямо сейчас', 'Justo ahora', 'Şu anda'),
(188, '', '', 'no_comments_found', 'No comments found', 'لم يتم العثور على تعليقات', 'Geen reacties gevonden', 'Aucun commentaire trouvé', 'Keine Kommentare gefunden', 'Комментариев не найдено', 'No se encontraron comentarios', 'Yorum bulunamadı'),
(189, '', '', 'delete_comment', 'Delete comment', 'حذف تعليق', 'Reactie verwijderen', 'Supprimer le commentaire', 'Kommentar löschen', 'Удалить комментарий', 'Eliminar comentario', 'Yorumu sil'),
(190, '', '', 'are_you_sure_you_want_to_delete_this_comment_', 'Are you sure you want to delete this comment?', 'هل أنت متأكد أنك تريد حذف هذا التعليق؟', 'Weet je zeker dat je deze reactie wilt verwijderen?', 'êtes-vous sûr de vouloir supprimer ce commentaire?', 'Möchten Sie diesen Kommentar wirklich löschen?', 'Вы уверенны, что хотите удалить этот комментарий?', '¿Estás seguro de que quieres eliminar este comentario?', 'Bu yorumu silmek istediğinize emin misiniz?'),
(191, '', '', 'report_comment', 'Report Comment', 'تقرير التعليق', 'Reactie melden', 'Signaler un commentaire', 'Kommentar melden', 'Пожаловаться на комментарий', 'Reportar comentario', 'Yorum Bildir'),
(192, '', '', 'no_more_comments_found', 'No more comments found', 'لم يتم العثور على المزيد من التعليقات', 'Geen reacties meer gevonden', 'Aucun autre commentaire trouvé', 'Keine weiteren Kommentare gefunden', 'Больше комментариев не найдено', 'No se han encontrado más comentarios.', 'Başka yorum bulunamadı'),
(213, '', '', 'cateogry_1', 'Other', 'Other', 'Other', 'Other', 'Other', 'Other', 'Other', 'Other'),
(215, '', '', 'in', 'in', 'في', 'in', 'dans', 'im', 'в', 'en', 'içinde'),
(216, '', '', 'other', 'Other', 'آخر', 'anders', 'Autre', 'Andere', 'Другой', 'Otro', 'Diğer'),
(217, '', '', 'more_tracks', 'More Tracks', 'المزيد من المسارات', 'Meer nummers', 'Plus de pistes', 'Weitere Tracks', 'Больше треков', 'Más pistas', 'Daha Fazla Parça'),
(218, '', '', 'purchase_track', 'Purchase track', 'شراء المسار', 'Aankoop track', 'Piste d\'achat', 'Kaufspur', 'Трек покупки', 'Pista de compra', 'Satın alma parça'),
(219, '', '', 'error_found_while_creating_the_payment__please_try_again_later.', 'Error found while creating the payment, please try again later.', 'تم العثور على خطأ أثناء إنشاء الدفع ، يرجى إعادة المحاولة لاحقًا.', 'Fout gevonden tijdens het maken van de betaling. Probeer het later opnieuw.', 'Erreur trouvée lors de la création du paiement, veuillez réessayer ultérieurement.', 'Beim Erstellen der Zahlung wurde ein Fehler gefunden. Bitte versuchen Sie es später erneut.', 'При создании платежа обнаружена ошибка. Повторите попытку позже.', 'Se ha encontrado un error al crear el pago, inténtalo de nuevo más tarde.', 'Ödeme oluşturulurken hata bulundu, lütfen daha sonra tekrar deneyin.'),
(220, '', '', 'purchase_required', 'Purchase Required', 'شراء المطلوبة', 'Aankoop vereist', 'Achat requis', 'Kauf erforderlich', 'Требуется покупка', 'Compra Requerida', 'Satın Alma Gerekli'),
(221, '', '', 'to_continue_listening_to_this_track__you_need_to_purchase_the_song.', 'To continue listening to this track, you need to purchase the song.', 'لمتابعة الاستماع إلى هذا المسار ، تحتاج إلى شراء الأغنية.', 'Als je naar deze track wilt blijven luisteren, moet je het nummer kopen.', 'Pour continuer à écouter cette piste, vous devez acheter la chanson.', 'Um diesen Titel weiter anzuhören, müssen Sie den Song kaufen.', 'Чтобы продолжить прослушивание этого трека, вам необходимо приобрести песню.', 'Para continuar escuchando esta pista, necesitas comprar la canción.', 'Bu parçayı dinlemeye devam etmek için, şarkıyı satın almanız gerekir.'),
(222, '', '', 'purchased', 'Purchased', 'اشترى', 'Gekocht', 'Acheté', 'Gekauft', 'купленный', 'Comprado', 'satın alındı'),
(223, '', '', 'no_purchased_tracks_found', 'No purchased tracks found', 'لم يتم العثور على المسارات المشتراة', 'Geen gekochte nummers gevonden', 'Aucune piste achetée trouvée', 'Keine gekauften Titel gefunden', 'Купленные треки не найдены', 'No se encontraron pistas compradas', 'Satın alınan hiçbir parça bulunamadı'),
(224, '', '', 'purchased_songs', 'Purchased Songs', 'أغاني تم شراؤها', 'Gekochte liedjes', 'Chansons achetées', 'Gekaufte Lieder', 'Купленные песни', 'Canciones compradas', 'Satın Alınan Şarkılar'),
(225, '', '', 'my_purchases', 'My Purchases', 'مشترياتي', 'Mijn aankopen', 'Mes achats', 'Meine Einkäufe', 'Мои покупки', 'Mis compras', 'Satın alımlarım'),
(226, '', '', 'purchased_on', 'Purchased on', 'تم شراؤها على', 'Gekocht op', 'Acheté le', 'Gekauft am', 'Куплен на', 'Comprado en', 'Tarihinde satın alındı'),
(227, '', '', 'purchased_your_song.', 'purchased your song.', 'اشتريت أغنيتك.', 'heb je nummer gekocht.', 'acheté votre chanson.', 'kaufte dein Lied.', 'купил вашу песню.', 'compré tu canción.', 'şarkını satın aldı.'),
(229, '', '', 'go_pro_to_download', 'Go PRO To Download', 'الذهاب للمحترفين للتحميل', 'Ga PRO om te downloaden', 'Go PRO Pour Télécharger', 'Zum Herunterladen gehen Sie auf PRO', 'Go PRO скачать', 'Ir PRO para descargar', 'Indirmek için PRO git'),
(230, '', '', 'generating_waves..', 'Generating waves..', 'توليد الأمواج ..', 'Golven genereren ..', 'Générer des vagues ..', 'Wellen erzeugen ..', 'Генерация волн ..', 'Generando olas ..', 'Dalgalar üretiliyor ..'),
(231, '', '', 'name', 'Name', 'اسم', 'Naam', 'prénom', 'Name', 'название', 'Nombre', 'isim'),
(232, '', '', 'your_full_name_as_showing_on_your_id', 'Your full name as showing on your ID', 'اسمك الكامل كما هو موضح على هويتك', 'Uw volledige naam wordt weergegeven op uw ID', 'Votre nom complet comme indiqué sur votre identifiant', 'Ihr vollständiger Name, wie auf Ihrer ID angegeben', 'Ваше полное имя показывается на вашем удостоверении личности', 'Su nombre completo como se muestra en su identificación', 'Kimliğinizde gösterildiği gibi tam adınız'),
(233, '', '', 'upload_documents', 'Upload documents', 'تحميل الوثائق', 'Upload documenten', 'Télécharger des documents', 'Dokumente hochladen', 'Загрузить документы', 'Subir documentos', 'Belgeleri Yükle');
INSERT INTO `langs` (`id`, `ref`, `options`, `lang_key`, `english`, `arabic`, `dutch`, `french`, `german`, `russian`, `spanish`, `turkish`) VALUES
(234, '', '', 'please_upload_a_photo_with_your_passport___id___your_distinct_photo.', 'Please upload a photo with your passport / ID &amp; your distinct photo.', 'يرجى تحميل صورة مع جواز سفرك / معرفك &amp; ؛ صورتك المميزة.', 'Upload een foto met uw paspoort / ID &amp; jouw duidelijke foto.', 'Veuillez télécharger une photo avec votre passeport / ID &amp; votre photo distincte.', 'Bitte laden Sie ein Foto mit Ihrem Pass / ID &amp; Ihr unterschiedliches Foto.', 'Пожалуйста, загрузите фотографию с вашим паспортом / ID &amp; твое отличное фото.', 'Suba una foto con su pasaporte / ID &amp; tu foto distinta', 'Lütfen pasaportunuz / kimliğinizle bir fotoğraf yükleyin &amp; senin farklı fotoğrafın.'),
(235, '', '', 'your_personal_photo', 'Your Personal Photo', 'صورتك الشخصية', 'Uw persoonlijke foto', 'Votre photo personnelle', 'Ihr persönliches Foto', 'Ваше личное фото', 'Tu foto personal', 'Kişisel Fotoğrafın'),
(236, '', '', 'passport___id_card', 'Passport / ID card', 'جواز السفر / بطاقة الهوية', 'Paspoort / ID-kaart', 'Passeport / carte d\'identité', 'Reisepass / ID-Karte', 'Паспорт / удостоверение личности', 'Pasaporte / DNI', 'Pasaport / kimlik kartı'),
(237, '', '', 'additional_details', 'Additional details', 'تفاصيل اضافية', 'Aanvullende details', 'Détails supplémentaires', 'Weitere Details', 'Дополнительные детали', 'Detalles adicionales', 'Ek detaylar'),
(238, '', '', 'we_will_review_your_request_within_24_hours__you_ll_be_informed_shourtly.', 'We will review your request within 24 hours, you&#039;ll be informed shourtly.', 'سنقوم بمراجعة طلبك خلال 24 ساعة ، وسيتم إبلاغك فورًا.', 'We zullen uw verzoek binnen 24 uur beoordelen, u wordt op de hoogte gebracht.', 'Nous examinerons votre demande dans les 24 heures, vous en serez informé.', 'Wir werden Ihre Anfrage innerhalb von 24 Stunden prüfen. Sie werden schnell informiert.', 'Мы рассмотрим ваш запрос в течение 24 часов, вы будете проинформированы.', 'Revisaremos su solicitud dentro de las 24 horas, se le informará brevemente.', 'İsteğinizi 24 saat içinde inceleyeceğiz, titizlikle bilgilendirileceksiniz.'),
(240, '', '', 'additional_details_about_your_self__optinal_', 'Additional details about your self (Optinal)', 'تفاصيل إضافية عن نفسك (Optinal)', 'Aanvullende informatie over jezelf (Optinal)', 'Détails supplémentaires sur vous-même (Optinal)', 'Zusätzliche Angaben zu Ihrer Person (Optinal)', 'Дополнительная информация о себе (Optinal)', 'Detalles adicionales sobre tu auto (Optinal)', 'Kendin hakkında ek ayrıntılar (Optinal)'),
(241, '', '', 'website__optional_', 'Website (Optional)', 'صفحة انترنت (اختياري)', 'Website (optioneel)', 'Site Internet (optionnel)', 'Webseite (optional)', 'Веб-сайт (необязательно)', 'Sitio web (opcional)', 'Web sitesi (İsteğe bağlı)'),
(242, '', '', 'thank_you._your_request_has_been_sent__we_will_get_back_to_you_shourtly.', 'Thank you. Your request has been sent, we will get back to you shourtly.', 'شكرا لكم. تم إرسال طلبك ، وسنقوم بالرد عليك بسرعة.', 'Dank je. Uw verzoek is verzonden, wij nemen u zo snel mogelijk terug.', 'Je vous remercie. Votre demande a été envoyée, nous vous répondrons dans les meilleurs délais.', 'Vielen Dank. Ihre Anfrage wurde gesendet, wir melden uns umgehend bei Ihnen.', 'Спасибо. Ваша заявка отправлена, мы ответим вам срочно.', 'Gracias. Su solicitud ha sido enviada, nos pondremos en contacto con usted en breve.', 'Teşekkür ederim. İsteğiniz gönderildi, size sert bir şekilde geri döneceğiz.'),
(243, '', '', 'error_found_while_processing_your_request__please_try_again_later.', 'Error found while processing your request, please try again later.', 'تم العثور على خطأ أثناء معالجة طلبك ، يرجى المحاولة مرة أخرى لاحقًا.', 'Fout gevonden tijdens het verwerken van uw verzoek. Probeer het later opnieuw.', 'Une erreur a été détectée lors du traitement de votre demande. Veuillez réessayer ultérieurement.', 'Fehler beim Verarbeiten Ihrer Anfrage, bitte versuchen Sie es später erneut.', 'При обработке вашего запроса обнаружена ошибка. Повторите попытку позже.', 'Se ha encontrado un error al procesar su solicitud, inténtelo de nuevo más tarde.', 'İsteğiniz işlenirken hata oluştu, lütfen daha sonra tekrar deneyin.'),
(244, '', '', 'your_request_has_been_already_sent__we_will_get_back_to_you_shourtly.', 'Your request has been already sent, we will get back to you shourtly.', 'تم إرسال طلبك بالفعل ، وسنقوم بالرد عليك بسرعة.', 'Uw verzoek is al verzonden, we nemen u zo snel mogelijk terug.', 'Votre demande a déjà été envoyée, nous vous recontacterons dans les meilleurs délais.', 'Ihre Anfrage wurde bereits gesendet, wir melden uns umgehend bei Ihnen.', 'Ваша заявка уже отправлена, мы ответим вам срочно.', 'Su solicitud ya ha sido enviada, nos pondremos en contacto con usted en breve.', 'İsteğiniz zaten gönderildi, size sert bir şekilde geri döneceğiz.'),
(245, '', '', 'get_verified__upload_more_songs__get_more_space__sell_your_songs__get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Get verified, upload more songs, get more space, sell your songs, get a special looking profile and get famous on our platform!', 'الحصول على التحقق ، تحميل المزيد من الأغاني ، الحصول على المزيد من المساحة ، بيع أغانيك ، الحصول على ملف تعريف ذو مظهر خاص والحصول على شهرة على نظامنا الأساسي!', 'Verifieer, upload meer nummers, krijg meer ruimte, verkoop je liedjes, krijg een speciaal uitziend profiel en word beroemd op ons platform!', 'Faites-vous vérifier, téléchargez plus de chansons, gagnez de l\'espace, vendez vos chansons, obtenez un profil spécial et devenez célèbre sur notre plateforme!', 'Lassen Sie sich verifizieren, laden Sie mehr Songs hoch, erhalten Sie mehr Platz, verkaufen Sie Ihre Songs, erhalten Sie ein spezielles Profil und werden Sie auf unserer Plattform berühmt!', 'Пройдите проверку, загрузите больше песен, получите больше места, продайте свои песни, получите специальный профиль и станьте известным на нашей платформе!', '¡Verifíquese, cargue más canciones, obtenga más espacio, venda sus canciones, obtenga un perfil de aspecto especial y sea famoso en nuestra plataforma!', 'Doğrulayın, daha fazla şarkı yükleyin, daha fazla alan kazanın, şarkılarınızı satın, özel bir görünüm elde edin ve platformumuzda ünlü olun!'),
(246, '', '', 'play_all', 'Play All', 'لعب كل', 'Speel alles', 'Jouer à tous', 'Alle wiedergeben', 'Играть все', 'Jugar todo', 'Hepsini Oynat'),
(247, '', '', 'latest_songs', 'Latest Songs', 'أحدث الأغاني', 'Laatste liedjes', 'Dernières chansons', 'Neueste Songs', 'Последние песни', 'Canciones más recientes', 'Son Şarkılar'),
(248, '', '', 'special_songs', 'Special Songs', 'أغاني خاصة', 'Speciale liedjes', 'Chansons Spéciales', 'Spezielle Lieder', 'Специальные песни', 'Canciones especiales', 'Özel Şarkılar'),
(249, '', '', 'top_songs', 'Top Songs', 'أفضل أغاني', 'Topnummers', 'Top chansons', 'Top-Songs', 'Лучшие песни', 'Mejores canciones', 'En Çok Okunan Şarkı Sözleri'),
(250, '', '', 'similar_artists', 'Similar Artists', 'فنانون متشابهون', 'Gelijkaardige kunstenaars', 'Artistes similaires', 'ähnliche Künstler', 'Похожие исполнители', 'Artistas similares', 'benzer sanatçılar'),
(251, '', '', 'artists', 'artists', 'الفنانين', 'kunstenaars', 'artistes', 'Künstler', 'художники', 'artistas', 'sanatçılar'),
(252, '', '', 'artist', 'artist', 'فنان', 'artiest', 'artiste', 'Künstler', 'художник', 'artista', 'sanatçı'),
(253, '', '', 'store', 'Store', 'متجر', 'Op te slaan', 'le magasin', 'Geschäft', 'хранить', 'Almacenar', 'mağaza'),
(254, '', '', 'congratulations__your_request_to_become_an_artist_was_approved.', 'Congratulations! Your request to become an artist was approved.', 'تهانينا! تمت الموافقة على طلبك لتصبح فنانا.', 'Gefeliciteerd! Uw verzoek om artiest te worden is goedgekeurd.', 'Toutes nos félicitations! Votre demande de devenir artiste a été approuvée.', 'Herzliche Glückwünsche! Ihre Anfrage, Künstler zu werden, wurde genehmigt.', 'Поздравляем! Ваш запрос стать художником был одобрен.', '¡Felicidades! Su solicitud para convertirse en un artista fue aprobada.', 'Tebrikler! Sanatçı olma isteğiniz onaylandı.'),
(255, '', '', 'sadly__your_request_to_become_an_artist_was_declined.', 'Sadly, Your request to become an artist was declined.', 'للأسف ، تم رفض طلبك لتصبح فنانا.', 'Helaas is je verzoek om artiest te worden afgewezen.', 'Malheureusement, votre demande de devenir artiste a été refusée.', 'Leider wurde Ihre Bitte, Künstler zu werden, abgelehnt.', 'К сожалению, ваша просьба стать художником была отклонена.', 'Lamentablemente, su solicitud para convertirse en un artista fue rechazada.', 'Ne yazık ki, sanatçı olma isteğiniz reddedildi.'),
(256, '', '', 'activities', 'Activities', 'أنشطة', 'Activiteiten', 'Activités', 'Aktivitäten', 'мероприятия', 'Ocupaciones', 'faaliyetler'),
(259, '', '', 're_post', 'Re Post', 'أعادة ارسال', 'opnieuw posten', 'republier', 'Umbuchen', 'репост', 'volver a publicar', 'Yeniden Gönder'),
(260, '', '', 'the_song_was_successfully_shared_on_your_timeline.', 'The song was successfully shared on your timeline.', 'تمت مشاركة الأغنية بنجاح على الجدول الزمني الخاص بك.', 'Het nummer is met succes gedeeld op je tijdlijn.', 'La chanson a été partagée avec succès sur votre timeline.', 'Der Song wurde erfolgreich auf Ihrer Timeline geteilt.', 'Песня была успешно опубликована на вашей временной шкале.', 'La canción fue compartida con éxito en su línea de tiempo.', 'Şarkı zaman çizelgenizde başarıyla paylaşıldı.'),
(261, '', '', 'no_activties_found', 'No activties found', 'لم يتم العثور على الأنشطة', 'Geen activiteiten gevonden', 'Aucune activité trouvée', 'Keine Aktivitäten gefunden', 'Активности не найдены', 'No se han encontrado actividades.', 'Etkinlik bulunamadı'),
(272, '', '', 'delete_post', 'Delete Post', 'حذف آخر', 'Verwijder gepost bericht', 'Supprimer le message', 'Beitrag löschen', 'Удалить сообщение', 'Eliminar mensaje', 'Gönderiyi Sil'),
(273, '', '', 'no_more_activities_found', 'No more activities found', 'لم يتم العثور على المزيد من الأنشطة', 'Geen activiteiten meer gevonden', 'Aucune autre activité trouvée', 'Keine weiteren Aktivitäten gefunden', 'Больше никаких действий не найдено', 'No se han encontrado más actividades.', 'Başka etkinlik bulunamadı'),
(274, '', '', 'weekly_top_tracks', 'Weekly Top Tracks', 'المسارات الأسبوعية الأعلى', 'Wekelijkse toptracks', 'Top titres hebdomadaires', 'Wöchentliche Top-Tracks', 'Лучшие треки за неделю', 'Top pistas semanales', 'Haftalık En Çok İzlenen Parçalar'),
(275, '', '', 'delete_your_post', 'Delete your post', 'حذف مشاركتك', 'Verwijder je bericht', 'Supprimer votre post', 'Löschen Sie Ihren Beitrag', 'Удалить свой пост', 'Borra tu publicación', 'Yayınınızı silin'),
(276, '', '', 'are_you_sure_you_want_to_delete_this_post_', 'Are you sure you want to delete this post?', 'هل أنت متأكد أنك تريد حذف هذه المشاركة؟', 'Weet je zeker dat je dit bericht wilt verwijderen?', 'Es-tu sur de vouloir supprimer cette annonce?', 'Möchten Sie diesen Beitrag wirklich löschen?', 'Вы уверены, что хотите удалить эту запись?', '¿Estás seguro de que quieres eliminar esta publicación?', 'Bu yayını silmek istediğinize emin misiniz?'),
(277, '', '', 'uploaded_a_new_song.', 'Uploaded a new song.', 'تم تحميل أغنية جديدة.', 'Een nieuw nummer ge-upload.', 'Téléchargé une nouvelle chanson.', 'Einen neuen Song hochgeladen.', 'Загрузил новую песню.', 'Subido una nueva canción.', 'Yeni bir şarkı yükledim.'),
(278, '', '', 'artists_to_follow', 'Artists to Follow', 'الفنانين لمتابعة', 'Kunstenaars om te volgen', 'Artistes à suivre', 'Künstler zu folgen', 'Исполнители, чтобы следовать', 'Artistas a seguir', 'İzlenecek Sanatçılar'),
(279, '', '', 'likes', 'Likes', 'الإعجابات', 'sympathieën', 'Aime', 'Likes', 'Нравится', 'Gustos', 'Seviyor'),
(280, '', '', 'plays', 'Plays', 'يلعب', 'Plays', 'Pièces', 'Theaterstücke', 'Пьесы', 'Obras de teatro', 'oynatır'),
(281, '', '', 'no_favourite_tracks_found', 'No favourite tracks found', 'لم يتم العثور على المسارات المفضلة', 'Geen favoriete nummers gevonden', 'Aucune piste favorite trouvée', 'Keine Lieblingstitel gefunden', 'Любимые треки не найдены', 'No se encontraron pistas favoritas', 'Favori parça bulunamadı'),
(282, '', '', 'my_favourites', 'My Favourites', 'المفضلة', 'Mijn favorieten', 'Mes préférés', 'Meine Favoriten', 'Мои любимые', 'Mis favoritos', 'Favorilerim'),
(283, '', '', 'you_currently_have__c__favourite_songs', 'You currently have |c| favourite songs', 'لديك حاليًا |c| الأغاني المفضلة', 'Je hebt momenteel |c| favoriete liedjes', 'Vous avez actuellement |c| chansons préférées', 'Sie haben derzeit |c| Lieblingslieder', 'У вас есть |c| любимые песни', 'Actualmente tienes |c| canciones favoritas', 'Şu anda |c| favori şarkılar'),
(285, '', '', 'you_currently_have__c__playlists.', 'You currently have |c| playlists.', 'لديك حاليًا |c| قوائم التشغيل.', 'Je hebt momenteel |c| afspeellijsten.', 'Vous avez actuellement |c| listes de lecture.', 'Sie haben derzeit |c| Wiedergabelisten.', 'У вас есть |c| плейлисты.', 'Actualmente tienes |c| playlists', 'Şu anda |c| çalma listeleri.'),
(286, '', '', 'create', 'Create', 'خلق', 'creëren', 'Créer', 'Erstellen', 'Создайте', 'Crear', 'yaratmak'),
(287, '', '', 'create_playlist', 'Create Playlist', 'إنشاء قائمة التشغيل', 'Maak afspeellijst', 'Créer une playlist', 'Wiedergabeliste erstellen', 'Создать плейлист', 'Crear lista de reproducción', 'Oynatma listesi yarat'),
(288, '', '', 'playlist_name', 'Playlist name', 'اسم قائمة التشغيل', 'Naam afspeellijst', 'Nom de la playlist', 'Playlistenname', 'Название плейлиста', 'Nombre de la lista de reproducción', 'Oynatma listesi adı'),
(289, '', '', 'error_found_while_uploading_the_playlist_avatar__please_try_again_later.', 'Error found while uploading the playlist avatar, Please try again later.', 'تم العثور على خطأ أثناء تحميل الصورة الرمزية لقائمة التشغيل ، يرجى المحاولة مرة أخرى لاحقًا.', 'Fout gevonden tijdens het uploaden van de avatar van de afspeellijst. Probeer het later opnieuw.', 'Une erreur a été détectée lors du téléchargement de l’avatar de la liste de lecture. Veuillez réessayer ultérieurement.', 'Fehler beim Hochladen des Wiedergabelisten-Avatars gefunden. Bitte versuchen Sie es später erneut.', 'При загрузке аватара плейлиста обнаружена ошибка. Повторите попытку позже.', 'Se ha encontrado un error al cargar el avatar de la lista de reproducción. Inténtalo de nuevo más tarde.', 'Oynatma listesi avatarını yüklerken hata bulundu, Lütfen daha sonra tekrar deneyin.'),
(291, '', '', 'edit_playlist', 'Edit Playlist', 'تحرير قائمة التشغيل', 'Bewerk afspeellijst', 'Editer la playlist', 'Wiedergabeliste bearbeiten', 'Изменить плейлист', 'Editar lista de reproducción', 'Oynatma Listesini Düzenle'),
(292, '', '', 'delete_playlist', 'Delete Playlist', 'حذف قائمة التشغيل', 'Verwijder afspeellijst', 'Supprimer la playlist', 'Playlist löschen', 'Удалить плейлист', 'Eliminar lista de reproducción', 'Oynatma Listesini Sil'),
(293, '', '', 'delete_your_playlist', 'Delete your playlist', 'حذف قائمة التشغيل الخاصة بك', 'Verwijder je afspeellijst', 'Supprimer votre playlist', 'Löschen Sie Ihre Playlist', 'Удалить свой плейлист', 'Eliminar tu lista de reproducción', 'Oynatma listenizi silin'),
(294, '', '', 'are_you_sure_you_want_to_delete_this_playlist_', 'Are you sure you want to delete this playlist?', 'هل أنت متأكد من أنك تريد حذف قائمة التشغيل هذه؟', 'Weet je zeker dat je deze afspeellijst wilt verwijderen?', 'Êtes-vous sûr de vouloir supprimer cette playlist?', 'Möchten Sie diese Playlist wirklich löschen?', 'Вы уверены, что хотите удалить этот плейлист?', '¿Estás seguro de que deseas eliminar esta lista de reproducción?', 'Bu oynatma listesini silmek istediğinize emin misiniz?'),
(295, '', '', 'share_this_playlist', 'Share this Playlist', 'مشاركة قائمة التشغيل هذه', 'Deel deze afspeellijst', 'Partager cette playlist', 'Teilen Sie diese Playlist', 'Поделиться этим плейлистом', 'Comparte esta lista de reproducción', 'Bu Oynatma Listesini paylaş'),
(296, '', '', 'play', 'Play', 'لعب', 'Spelen', 'Jouer', 'abspielen', 'Играть', 'Jugar', 'Oyun'),
(297, '', '', 'no_songs_on_this_playlist.', 'No songs on this playlist.', 'لا توجد أغاني في قائمة التشغيل هذه.', 'Geen nummers in deze afspeellijst.', 'Aucune chanson sur cette playlist.', 'Keine Songs auf dieser Playlist.', 'В этом плейлисте нет песен.', 'No hay canciones en esta lista de reproducción.', 'Bu çalma listesinde şarkı yok.'),
(298, '', '', 'no_songs_on_this_playlist_yet.', 'No songs on this playlist yet.', 'لا توجد أغاني في قائمة التشغيل هذه حتى الآن.', 'Nog geen nummers in deze afspeellijst.', 'Aucune chanson sur cette playlist pour le moment.', 'Keine Songs in dieser Playlist.', 'В этом плейлисте еще нет песен.', 'No hay canciones en esta lista de reproducción todavía.', 'Bu çalma listesinde henüz şarkı yok.'),
(299, '', '', 'select_playlists', 'Select playlists', 'حدد قوائم التشغيل', 'Selecteer afspeellijsten', 'Sélectionner des playlists', 'Wiedergabelisten auswählen', 'Выберите плейлисты', 'Seleccionar listas de reproducción', 'Oynatma listelerini seç'),
(300, '', '', 'add', 'Add', 'إضافة', 'Toevoegen', 'Ajouter', 'Hinzufügen', 'добавлять', 'Añadir', 'Eklemek'),
(301, '', '', 'please_select_which_playlist_you_want_to_add_this_song_to.', 'Please select which playlist you want to add this song to.', 'الرجاء تحديد قائمة التشغيل التي تريد إضافة هذه الأغنية إليها.', 'Selecteer alstublieft aan welke afspeellijst u deze song wilt toevoegen.', 'Veuillez sélectionner la playlist à laquelle vous souhaitez ajouter cette chanson.', 'Bitte wählen Sie die Playlist aus, zu der Sie diesen Song hinzufügen möchten.', 'Пожалуйста, выберите, в какой плейлист вы хотите добавить эту песню.', 'Seleccione la lista de reproducción a la que desea agregar esta canción.', 'Lütfen bu şarkıyı hangi şarkıya eklemek istediğinizi seçin.'),
(302, '', '', 'no_playlists_found', 'No playlists found', 'لم يتم العثور على قوائم التشغيل', 'Geen afspeellijsten gevonden', 'Aucune liste de lecture trouvée', 'Keine Wiedergabelisten gefunden', 'Плейлисты не найдены', 'No se encontraron listas de reproducción', 'Oynatma listesi bulunamadı'),
(303, '', '', 'new', 'New', 'الجديد', 'nieuwe', 'Nouveau', 'Neu', 'новый', 'Nuevo', 'Yeni'),
(304, '', '', 'no_more_playlists_found', 'No more playlists found', 'لم يتم العثور على المزيد من قوائم التشغيل', 'Geen afspeellijsten meer gevonden', 'Plus de playlists trouvées', 'Keine weiteren Wiedergabelisten gefunden', 'Больше не найдено плейлистов', 'No se encontraron más listas de reproducción', 'Daha fazla oynatma listesi bulunamadı'),
(305, '', '', 'discover', 'Discover', 'اكتشف', 'Ontdekken', 'Découvrir', 'Entdecken', 'Обнаружить', 'Descubrir', 'keşfedin'),
(306, '', '', 'show_all', 'Show All', 'عرض الكل', 'Toon alles', 'Montre tout', 'Zeige alles', 'Показать все', 'Mostrar todo', 'Hepsini Göster ↓'),
(307, '', '', 'new_releases', 'New Releases', 'الإصدارات الجديدة', 'Nieuwe uitgaven', 'Nouvelles versions', 'Neue Veröffentlichungen', 'Новые релизы', 'Nuevos lanzamientos', 'Yeni sürümler'),
(308, '', '', 'most_popular_this_week', 'Most Popular This Week', 'الأكثر شعبية هذا الأسبوع', 'Meest populair deze week', 'Le plus populaire cette semaine', 'Am beliebtesten diese Woche', 'Самые популярные на этой неделе', 'Más popular esta semana', 'Bu Hafta En Popüler'),
(309, '', '', 'most_recommended', 'Most Recommended', 'الأكثر الموصى بها', 'Meest aanbevolen', 'Le plus recommandé', 'Am meisten empfohlen', 'Самые рекомендуемые', 'Más recomendado', 'En çok önerilen'),
(310, '', '', 'recommended', 'Recommended', 'موصى به', 'Aanbevolen', 'conseillé', 'Empfohlen', 'рекомендуемые', 'Recomendado', 'Tavsiye edilen'),
(311, '', '', 'new_music', 'New Music', 'موسيقى جديدة', 'Nieuwe muziek', 'Nouvelle musique', 'Neue Musik', 'Новая музыка', 'Música nueva', 'Yeni müzik'),
(312, '', '', 'best_new_releases', 'Best New Releases', 'أفضل الإصدارات الجديدة', 'Beste nieuwe releases', 'Meilleures nouveautés', 'Beste Neuerscheinungen', 'Лучшие новые релизы', 'Mejores lanzamientos nuevos', 'En İyi Yeni Çıkanlar'),
(313, '', '', 'latest_music', 'Latest Music', 'أحدث الموسيقى', 'Nieuwste muziek', 'Dernières musiques', 'Neueste Musik', 'Последняя музыка', 'La ultima musica', 'En Son Müzik'),
(315, '', '', 'top_music', 'Top Music', 'الموسيقى كبار', 'Topmuziek', 'Top musique', 'Top-Musik', 'Топ Музыка', 'La mejor música', 'En İyi Müzik'),
(316, '', '', 'see_all', 'See All', 'اظهار الكل', 'Alles zien', 'Voir tout', 'Alles sehen', 'Увидеть все', 'Ver todo', 'Tümünü Gör'),
(317, '', '', 'top_albums', 'Top Albums', 'أفضل الألبومات', 'Topalbums', 'Top Albums', 'Top-Alben', 'Лучшие альбомы', 'Top albumes', 'En İyi Albümler'),
(318, '', '', 'top', 'Top', 'أعلى', 'Top', 'Haut', 'oben', 'верхний', 'Parte superior', 'Üst'),
(319, '', '', 'top_50', 'Top 50', 'أعلى 50', 'Top 50', 'Top 50', 'Top 50', '50 лучших', 'Top 50', 'En iyi 50'),
(320, '', '', 'browse_music', 'Browse Music', 'استعراض الموسيقي', 'Muziek doorzoeken', 'Parcourir la musique', 'Musik durchsuchen', 'Просмотр музыки', 'Buscar música', 'Müziğe Göz At'),
(321, '', '', 'genres', 'Genres', 'الجنس الأدبي', 'Genres', 'Les genres', 'Genres', 'Жанры', 'Géneros', 'Türler'),
(322, '', '', 'your_music', 'Your Music', 'الموسيقى الخاصة بك', 'Jouw muziek', 'Ta musique', 'Deine Musik', 'Ваша музыка', 'Tu musica', 'Senin müziğin'),
(323, '', '', 'latest_songs_in', 'Latest Songs In', 'أحدث الأغاني في', 'Laatste liedjes in', 'Dernières chansons en', 'Neueste Songs in', 'Последние песни в', 'Últimas canciones en', 'Son Şarkılar'),
(324, '', '', 'age_restricted_track', 'Age restricted track', 'العمر مقيد المسار', 'Leeftijd beperkte track', 'Piste d\'âge restreint', 'Altersbeschränkte Spur', 'Возрастная дорожка', 'Pista de edad restringida', 'Yaş kısıtlamalı parkur'),
(325, '', '', 'this_track_is_age_restricted_for_viewers_under__18', 'This track is age restricted for viewers under +18', 'هذا المسار مقيد بحسب العمر للمشاهدين الذين تقل أعمارهم عن 18 عامًا', 'Deze track is leeftijdsbeperkend voor kijkers onder +18', 'Ce titre est réservé aux moins de 18 ans.', 'Dieser Titel ist für Zuschauer unter 18 Jahren altersbeschränkt', 'Этот трек ограничен по возрасту для зрителей до +18', 'Esta pista tiene restricciones de edad para los espectadores menores de 18 años.', 'Bu parça +18 yaşın altındaki izleyiciler için yaş sınırlaması var'),
(326, '', '', 'create_an_account_or_login_to_confirm_your_age.', 'Create an account or login to confirm your age.', 'إنشاء حساب أو تسجيل الدخول لتأكيد عمرك.', 'Maak een account aan of log in om uw leeftijd te bevestigen.', 'Créez un compte ou connectez-vous pour confirmer votre âge.', 'Erstellen Sie ein Konto oder melden Sie sich an, um Ihr Alter zu bestätigen.', 'Создайте аккаунт или войдите, чтобы подтвердить свой возраст.', 'Crea una cuenta o inicia sesión para confirmar tu edad.', 'Yaşınızı onaylamak için bir hesap oluşturun veya giriş yapın.'),
(327, '', '', 'this_track_is_age_restricted_for_viewers_under_18', 'This track is age restricted for viewers under 18', 'هذا المسار مقيد بحسب العمر للمشاهدين دون سن 18 عامًا', 'Deze track is leeftijdsbeperkend voor kijkers onder de 18 jaar', 'Ce titre est réservé aux moins de 18 ans.', 'Dieser Titel ist für Zuschauer unter 18 Jahren altersbeschränkt', 'Этот трек ограничен по возрасту для зрителей младше 18 лет', 'Esta pista tiene restricciones de edad para los espectadores menores de 18 años.', 'Bu parça 18 yaşın altındaki izleyiciler için yaş sınırlıdır'),
(328, '', '', 'upgrade_to_pro', 'Upgrade To PRO', 'التطور للاحترافية', 'Upgraden naar Pro', 'Passer à Pro', 'Upgrade auf PRO', 'Обновление до PRO', 'Actualizar a PRO', 'Pro\'ya yükselt'),
(329, '', '', 'go_pro_', 'Go Pro!', 'الذهاب برو!', 'Ga Pro!', 'Go Pro!', 'Gehen Sie Pro!', 'Go Pro!', '¡Vaya Pro!', 'Git Pro!'),
(330, '', '', 'discover_more_features_with_our_premium_package_', 'Discover more features with our Premium package!', 'اكتشف المزيد من الميزات مع باقة Premium الخاصة بنا!', 'Ontdek meer functies met ons Premium-pakket!', 'Découvrez plus de fonctionnalités avec notre forfait Premium!', 'Entdecken Sie weitere Funktionen mit unserem Premium-Paket!', 'Откройте для себя больше возможностей с нашим пакетом Premium!', '¡Descubre más características con nuestro paquete Premium!', 'Premium paketimizle daha fazla özellik keşfedin!'),
(331, '', '', 'free_plan', 'Free Plan', 'خطة مجانية', 'Gratis abonnement', 'Plan gratuit', 'Kostenloser Plan', 'Бесплатный план', 'Plan gratis', 'Ücretsiz Plan'),
(332, '', '', 'upload_songs_up_to', 'Upload songs up to', 'تحميل الأغاني تصل إلى', 'Upload nummers tot', 'Télécharger des chansons jusqu\'à', 'Songs hochladen bis', 'Загрузить песни до', 'Sube canciones hasta', 'Şarkıları en fazla yükle'),
(333, '', '', 'pro_badge', 'Pro badge', 'شارة الموالية', 'Pro-badge', 'Badge pro', 'Pro Abzeichen', 'Про значок', 'Insignia pro', 'Pro rozeti'),
(334, '', '', 'download_songs', 'Download songs', 'تحميل اغاني', 'Liedjes downloaden', 'Télécharger des chansons', 'Songs herunterladen', 'Скачать песни', 'Descargar canciones', 'Şarkı indir'),
(335, '', '', 'turn_off_comments_download', 'Turn off comments/download', 'قم بإيقاف تشغيل التعليقات / التنزيل', 'Schakel opmerkingen / downloaden uit', 'Désactiver les commentaires / télécharger', 'Kommentare deaktivieren / herunterladen', 'Отключить комментарии / скачать', 'Desactivar comentarios / descargar', 'Yorumları kapat / indir'),
(336, '', '', 'current_plan', 'Current Plan', 'الخطه الحاليه', 'Huidige plan', 'Plan actuel', 'Derzeitiger Plan', 'Текущий план', 'Plan actual', 'Mevcut Plan'),
(337, '', '', 'pro_plan', 'Pro Plan', 'خطة الموالية', 'Pro Plan', 'Plan Pro', 'Pro Plan', 'Pro План', 'Pro Plan', 'Pro Plan'),
(338, '', '', 'per_month', 'per month', 'كل شهر', 'per maand', 'par mois', 'pro Monat', 'в месяц', 'por mes', 'her ay'),
(339, '', '', 'p_month', 'p/month', 'ص / شهر', 'p / maand', 'p / mois', 'p / Monat', 'р / месяц', 'p / mes', 'p / ay'),
(340, '', '', 'monthly', 'monthly', 'شهريا', 'maandelijks', 'mensuel', 'monatlich', 'ежемесячно', 'mensual', 'aylık'),
(341, '', '', 'upload_unlimited_songs', 'Upload unlimited songs', 'تحميل الأغاني غير محدودة', 'Upload onbeperkte nummers', 'Télécharger des chansons illimitées', 'Lade unbegrenzt Songs hoch', 'Загрузить неограниченное количество песен', 'Sube canciones ilimitadas', 'Sınırsız şarkı yükle'),
(342, '', '', 'upgrade', 'Upgrade', 'تطوير', 'Upgrade', 'Améliorer', 'Aktualisierung', 'Обновить', 'Mejorar', 'Yükselt'),
(343, '', '', 'secured_payment_transaction', 'Secured payment transaction', 'معاملة الدفع المضمونة', 'Beveiligde betalingstransactie', 'Opération de paiement sécurisée', 'Gesicherte Zahlungstransaktion', 'Защищенная платежная операция', 'Transacción de pago garantizado', 'Güvenli ödeme işlemi'),
(344, '', '', 'redirecting..', 'Redirecting..', 'إعادة توجيه..', 'Wordt omgeleid ..', 'Redirection ..', 'Umleiten ..', 'Перенаправление ..', 'Redireccionando ..', 'Yönlendiriliyor ..'),
(345, '', '', 'oops__an_error_found.', 'Oops, an error found.', 'عفوًا ، تم العثور على خطأ.', 'Oeps, er is een fout gevonden.', 'Oups, une erreur trouvée.', 'Ups, ein Fehler wurde gefunden.', 'К сожалению, ошибка найдена.', 'Vaya, se ha encontrado un error.', 'Hata! Bir hata bulundu.'),
(346, '', '', 'you_are_a_pro_', 'You are a pro!', 'أنت محترف!', 'Je bent een pro!', 'Tu es un pro!', 'Du bist ein Profi!', 'Вы профессионал!', 'Eres un profesional!', 'Sen bir profesyonelsin!'),
(347, '', '', 'unexpected_error_found_while_processing_your_payment__please_try_again_later.', 'Unexpected error found while processing your payment, please try again later.', 'تم العثور على خطأ غير متوقع أثناء معالجة دفعتك ، يرجى إعادة المحاولة لاحقًا.', 'Onverwachte fout gevonden tijdens het verwerken van uw betaling, probeer het later opnieuw.', 'Une erreur inattendue a été détectée lors du traitement de votre paiement. Veuillez réessayer ultérieurement.', 'Bei der Verarbeitung Ihrer Zahlung wurde ein unerwarteter Fehler gefunden. Bitte versuchen Sie es später erneut.', 'При обработке вашего платежа обнаружена непредвиденная ошибка, повторите попытку позже.', 'Se ha encontrado un error inesperado al procesar su pago. Inténtelo de nuevo más tarde.', 'Ödemeniz işlenirken beklenmeyen bir hata bulundu, lütfen daha sonra tekrar deneyin.'),
(348, '', '', 'payment_error', 'Payment Error', 'خطأ الدفع', 'Betalingsfout', 'Erreur de paiement', 'Zahlungsfehler', 'Ошибка платежа', 'Error en el pago', 'Ödeme hatası'),
(353, '', '', 'you_have_reached_your_upload_limit___link__to_upload_unlimited_songs.', 'You have reached your upload limit, |link| to upload unlimited songs.', 'لقد وصلت إلى الحد الأقصى للتحميل ، |link| لتحميل الأغاني غير محدودة.', 'Je hebt je uploadlimiet bereikt, |link| om onbeperkte nummers te uploaden.', 'Vous avez atteint votre limite de téléchargement, |link| télécharger des chansons illimitées.', 'Sie haben Ihr Upload-Limit erreicht, |link| unbegrenzt Songs hochladen.', 'Вы достигли лимита загрузки, |link| загружать неограниченное количество песен.', 'Has alcanzado tu límite de subida, |link| para subir canciones ilimitadas.', 'Yükleme sınırınıza ulaştınız, |link| sınırsız şarkı yüklemek için.'),
(354, '', '', 'get_verified__sell_your_songs__get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Get verified, sell your songs, get a special looking profile and get famous on our platform!', 'الحصول على التحقق ، بيع أغانيك ، والحصول على ملف تعريف خاص المظهر والحصول على شهرة على منصة لدينا!', 'Wordt geverifieerd, verkoop je liedjes, krijg een speciaal uitziend profiel en word beroemd op ons platform!', 'Faites-vous vérifier, vendez vos chansons, obtenez un profil spécial et devenez célèbre sur notre plateforme!', 'Lassen Sie sich verifizieren, verkaufen Sie Ihre Songs, erhalten Sie ein speziell aussehendes Profil und werden Sie auf unserer Plattform berühmt!', 'Пройдите проверку, продайте свои песни, получите специальный профиль и станьте известным на нашей платформе!', '¡Verifíquese, venda sus canciones, obtenga un perfil especial y vuélvase famoso en nuestra plataforma!', 'Doğrulayın, şarkılarınızı satın, özel bir görünüme sahip olun ve platformumuzda ünlü olun!'),
(355, '', '', 'pro_memeber', 'PRO Member', 'عضو محترف', 'PRO-lid', 'Membre PRO', 'PRO Mitglied', 'PRO Член', 'Miembro PRO', 'PRO Üyesi'),
(356, '', '', 'manage_my_songs', 'Manage My Songs', 'إدارة أغانيي', 'Beheer mijn liedjes', 'Gérer mes chansons', 'Meine Songs verwalten', 'Управляй моими песнями', 'Manejar mis canciones', 'Şarkılarımı Yönet'),
(357, '', '', 'published', 'Published', 'نشرت', 'Gepubliceerd', 'Publié', 'Veröffentlicht', 'опубликованный', 'Publicado', 'Yayınlanan'),
(358, '', '', 'total_songs', 'Total Songs', 'مجموع الأغاني', 'Totaal aantal nummers', 'Nombre total de chansons', 'Songs insgesamt', 'Всего песен', 'Canciones totales', 'Toplam şarkı'),
(359, '', '', 'total_plays', 'Total Plays', 'مجموع المسرحيات', 'Totale spelen', 'Nombre total de lectures', 'Gesamtanzahl der Spiele', 'Всего игр', 'Jugadas totales', 'Toplam oyun'),
(360, '', '', 'total_downloads', 'Total Downloads', 'إجمالي التنزيلات', 'Totaal aantal downloads', 'Total des téléchargements', 'Downloads insgesamt', 'Всего загрузок', 'Descargas totales', 'Toplam indirme'),
(361, '', '', 'total_sales', 'Total Sales', 'إجمالي المبيعات', 'Totale verkoop', 'Ventes totales', 'Gesamtumsatz', 'Тотальная распродажа', 'Ventas totales', 'Toplam satış'),
(362, '', '', 'total_sales_this_month', 'Total Sales This Month', 'إجمالي المبيعات هذا الشهر', 'Totale omzet deze maand', 'Total des ventes ce mois-ci', 'Gesamtumsatz in diesem Monat', 'Всего продаж в этом месяце', 'Total de ventas este mes', 'Bu Ayın Toplam Satışı'),
(363, '', '', 'total_sales_this_today', 'Total Sales This Today', 'إجمالي المبيعات هذا اليوم', 'Totale omzet dit vandaag', 'Total des ventes aujourd\'hui', 'Gesamtumsatz heute', 'Общий объем продаж сегодня', 'Ventas Totales Este Hoy', 'Bugünün Toplam Satışı'),
(364, '', '', 'total_sales_today', 'Total Sales Today', 'إجمالي المبيعات اليوم', 'Totale verkoop vandaag', 'Total des ventes aujourd\'hui', 'Gesamtumsatz heute', 'Всего продаж сегодня', 'Ventas totales hoy', 'Toplam Satış Bugün'),
(365, '', '', 'downloads', 'Downloads', 'التنزيلات', 'downloads', 'Téléchargements', 'Downloads', 'Загрузки', 'Descargas', 'İndirilenler'),
(366, '', '', 'sales', 'Sales', 'مبيعات', 'verkoop', 'Ventes', 'Der Umsatz', 'Продажи', 'Ventas', 'Satış'),
(367, '', '', 'most_played_songs', 'Most played songs', 'معظم الأغاني لعبت', 'Meest gespeelde nummers', 'Les chansons les plus jouées', 'Meist gespielte Lieder', 'Самые популярные песни', 'Las canciones más jugadas', 'En çok çalınan şarkılar'),
(368, '', '', 'no_songs_found', 'No songs found', 'لا توجد أغاني', 'Geen nummers gevonden', 'Aucune chanson trouvée', 'Keine Songs gefunden', 'Песни не найдены', 'No se encontraron canciones', 'Şarkı bulunamadı'),
(369, '', '', 'most_commented_songs', 'Most commented songs', 'معظم الأغاني علق', 'Meest besproken nummers', 'La plupart des chansons commentées', 'Meist kommentierte Songs', 'Самые комментируемые песни', 'Canciones más comentadas', 'En çok yorum yapılan şarkılar'),
(370, '', '', 'most_liked_songs', 'Most liked songs', 'معظم الأغاني يحب', 'De meesten vonden nummers leuk', 'Les chansons les plus aimées', 'Meistgeliebte Lieder', 'Самые любимые песни', 'Las canciones mas gustadas', 'En çok sevilen şarkılar'),
(371, '', '', 'most_downloaded_songs', 'Most downloaded songs', 'معظم الأغاني التي تم تنزيلها', 'Meest gedownloade nummers', 'Les chansons les plus téléchargées', 'Die meisten heruntergeladenen Songs', 'Самые скачиваемые песни', 'Las canciones más descargadas', 'En çok indirilen şarkılar'),
(372, '', '', 'recent_sales', 'Recent sales', 'المبيعات الأخيرة', 'Recente verkopen', 'Ventes récentes', 'Letzte Verkäufe', 'Недавние продажи', 'Ventas recientes', 'Son satışlar'),
(373, '', '', 'no_sales_found', 'No sales found', 'لا توجد مبيعات', 'Geen verkopen gevonden', 'Aucune vente trouvée', 'Kein Umsatz gefunden', 'Продажи не найдены', 'No se encontraron ventas', 'Satış bulunamadı'),
(374, '', '', 'listened_by', 'Listened by', 'استمع بواسطة', 'Geluisterd door', 'Écouté par', 'Gehört von', 'Слушал', 'Escuchado por', 'Tarafından dinlendi'),
(375, '', '', 'recently_listened_by', 'Recently Listened by', 'استمع مؤخرا من قبل', 'Onlangs beluisterd door', 'Récemment écouté par', 'Kürzlich gehört von', 'Недавно прослушал', 'Recientemente escuchado por', 'Son Dinleyenler'),
(376, '', '', 'songs_i_liked', 'Songs I Liked', 'أغاني أعجبتني', 'Liedjes die ik leuk vond', 'Chansons que j\'ai aimé', 'Songs, die ich mochte', 'Песни, которые мне понравились', 'Canciones que me gustaron', 'Sevdiğim Şarkılar'),
(377, '', '', 'block', 'Block', 'منع', 'Blok', 'Bloc', 'Block', 'блок', 'Bloquear', 'Blok'),
(378, '', '', 'are_you_sure_you_want_to_block_this_user', 'Are you sure you want to block this user', 'هل أنت متأكد أنك تريد حظر هذا المستخدم', 'Weet je zeker dat je deze gebruiker wilt blokkeren?', 'Êtes vous sûr de vouloir bloquer cet utilisateur', 'Möchten Sie diesen Benutzer wirklich blockieren?', 'Вы уверены, что хотите заблокировать этого пользователя', 'Estás seguro de que quieres bloquear a este usuario', 'Bu kullanıcıyı engellemek istediğinize emin misiniz?'),
(379, '', '', 'unblock', 'Unblock', 'رفع الحظر', 'deblokkeren', 'Débloquer', 'Blockierung aufheben', 'открыть', 'Desatascar', 'engeli kaldırmak'),
(380, '', '', 'blocked_users', 'Blocked Users', 'مستخدمين محجوبين', 'Geblokkeerde gebruikers', 'Utilisateurs bloqués', 'Blockierte Benutzer', 'Заблокированные пользователи', 'Usuarios bloqueados', 'Engellenmiş kullanıcılar'),
(381, '', '', 'no_blocked_users_found', 'No blocked users found', 'لم يتم العثور على المستخدمين المحظورين', 'Geen geblokkeerde gebruikers gevonden', 'Aucun utilisateur bloqué trouvé', 'Keine blockierten Benutzer gefunden', 'Заблокированные пользователи не найдены', 'No se encontraron usuarios bloqueados', 'Engellenen kullanıcı bulunamadı'),
(382, '', '', 'album_title', 'Album Title', 'عنوان الألبوم', 'Album titel', 'Titre de l\'album', 'Albumtitel', 'Название альбома', 'Título del álbum', 'Albüm başlığı'),
(383, '', '', 'your_album_title__2_-_55_characters', 'Your album title, 2 - 55 characters', 'عنوان الألبوم الخاص بك ، 2 - 55 حرفا', 'De titel van je album, 2 - 55 tekens', 'Le titre de votre album, 2 - 55 caractères', 'Ihr Albumtitel, 2 - 55 Zeichen', 'Название вашего альбома, 2 - 55 символов', 'El título de tu álbum, 2 - 55 caracteres.', 'Albümünüzün başlığı, 2 - 55 karakter'),
(384, '', '', 'album_description', 'Album Description', 'وصف الألبوم', 'Albumbeschrijving', 'Description de l\'album', 'Beschreibung des Albums', 'Описание альбома', 'Descripción del Album', 'Albüm Açıklaması'),
(385, '', '', 'album_price', 'Album Price', 'سعر الألبوم', 'Albumprijs', 'Prix ​​de l\'album', 'Albumpreis', 'Цена альбома', 'Precio del album', 'Albüm Fiyatı'),
(386, '', '', 'add_song', 'Add Song', 'أضف أغنية', 'Voeg lied toe', 'Ajouter une chanson', 'Song hinzufügen', 'Добавить песню', 'Añadir canción', 'Şarkı ekle'),
(387, '', '', 'successfully_uploaded', 'Successfully uploaded', 'تم الرفع بنجاح', 'Succesvol geüpload', 'Téléchargé avec succès', 'Erfolgreich hochgeladen', 'Успешно загружено', 'Cargado con éxito', 'Başarıyla yüklendi'),
(388, '', '', 'album_thumbnail_is_required.', 'Album thumbnail is required.', 'مطلوب صورة الألبوم.', 'Er is een miniatuur van het album vereist.', 'La vignette de l\'album est requise.', 'Album-Miniaturansicht ist erforderlich.', 'Требуется миниатюра альбома.', 'Se requiere la miniatura del álbum.', 'Albüm küçük resmi gereklidir.'),
(389, '', '', 'your_album_was_successfully_created__please_wait..', 'Your album was successfully created, please wait..', 'تم إنشاء ألبومك بنجاح ، يرجى الانتظار ..', 'Je album is succesvol gemaakt, even geduld aub ..', 'Votre album a été créé avec succès, veuillez patienter ..', 'Ihr Album wurde erfolgreich erstellt. Bitte warten Sie ..', 'Ваш альбом был успешно создан, пожалуйста, подождите ..', 'Su álbum fue creado exitosamente, por favor espere ..', 'Albümünüz başarıyla oluşturuldu, lütfen bekleyin ..'),
(390, '', '', 'album_title_is_required.', 'Album title is required.', 'عنوان الألبوم مطلوب.', 'Albumtitel is verplicht.', 'Le titre de l\'album est requis.', 'Albumtitel sind erforderlich.', 'Название альбома обязательно.', 'El título del álbum es obligatorio.', 'Albüm başlığı gerekli.'),
(391, '', '', 'album_description_is_required.', 'Album description is required.', 'وصف الألبوم مطلوب.', 'Albumbeschrijving is verplicht.', 'La description de l\'album est obligatoire.', 'Die Beschreibung des Albums ist erforderlich.', 'Требуется описание альбома.', 'Se requiere la descripción del álbum.', 'Albüm açıklaması gerekli.'),
(392, '', '', 'are_you_sure_you_want_to_delete_this_song_', 'Are you sure you want to delete this song?', 'هل أنت متأكد أنك تريد حذف هذه الأغنية؟', 'Weet je zeker dat je dit nummer wilt verwijderen?', 'Êtes-vous sûr de vouloir supprimer cette chanson?', 'Möchten Sie diesen Song wirklich löschen?', 'Вы уверены, что хотите удалить эту песню?', '¿Seguro que quieres borrar esta canción?', 'Bu şarkıyı silmek istediğinize emin misiniz?'),
(393, '', '', 'top_50_albums', 'Top 50 Albums', 'أفضل 50 ألبومات', 'Top 50 albums', 'Top 50 albums', 'Top 50 Alben', '50 лучших альбомов', 'Top 50 de los álbumes', 'En İyi 50 Albüm'),
(394, '', '', 'no_songs_on_this_album_yet.', 'No songs on this album yet.', 'لا توجد أغاني في هذا الألبوم حتى الآن.', 'Nog geen nummers op dit album.', 'Aucune chanson sur cet album pour le moment.', 'Noch keine Songs auf diesem Album.', 'В этом альбоме пока нет песен.', 'No hay canciones en este álbum todavía.', 'Bu albümde henüz şarkı yok.'),
(395, '', '', 'you_may_also_like', 'You may also like', 'ربما يعجبك أيضا', 'Dit vind je misschien ook leuk', 'Tu pourrais aussi aimer', 'Sie können auch mögen', 'Вам также может понравиться', 'También te puede interesar', 'Şunlar da hoşunuza gidebilir'),
(396, '', '', 'your_album_was_successfully_updated__please_wait..', 'Your album was successfully updated, please wait..', 'تم تحديث ألبومك بنجاح ، يرجى الانتظار ..', 'Uw album is succesvol bijgewerkt, even geduld aub ..', 'Votre album a été mis à jour avec succès, veuillez patienter ..', 'Ihr Album wurde erfolgreich aktualisiert. Bitte warten Sie ..', 'Ваш альбом был успешно обновлен, пожалуйста, подождите ..', 'Su álbum fue actualizado con éxito, por favor espere ...', 'Albümünüz başarıyla güncellendi, lütfen bekleyin ..'),
(397, '', '', 'in_album_', 'in album:', 'في الألبوم:', 'in album:', 'dans l\'album:', 'in album:', 'в альбоме:', 'en el álbum:', 'albümde:'),
(398, '', '', 'delete_your_album', 'Delete your album', 'احذف ألبومك', 'Verwijder je album', 'Supprimer votre album', 'Löschen Sie Ihr Album', 'Удалить свой альбом', 'Borra tu álbum', 'Albümünü sil'),
(399, '', '', 'are_you_sure_you_want_to_delete_this_album_', 'Are you sure you want to delete this album?', 'هل أنت متأكد أنك تريد حذف هذا الألبوم؟', 'Weet je zeker dat je dit album wilt verwijderen?', 'Êtes-vous sûr de vouloir supprimer cet album?', 'Möchten Sie dieses Album wirklich löschen?', 'Вы уверены, что хотите удалить этот альбом?', '¿Estás seguro de que quieres eliminar este álbum?', 'Bu albümü silmek istediğinden emin misin?'),
(400, '', '', 'yes__but_keep_the_songs', 'Yes, But Keep The Songs', 'نعم ، لكن احتفظ بالأغاني', 'Ja, maar houd de liedjes', 'Oui, mais gardez les chansons', 'Ja, aber behalten Sie die Lieder', 'Да, но сохраняй песни', 'Sí, pero conserva las canciones', 'Evet, Ama Şarkıları Sakla'),
(401, '', '', 'yes__delete_everything', 'Yes, Delete Everything', 'نعم ، احذف كل شيء', 'Ja, verwijder alles', 'Oui, tout effacer', 'Ja, alles löschen', 'Да, Удалить все', 'Si, eliminar todo', 'Evet, Her Şeyi Sil'),
(402, '', '', 'my_songs', 'My Songs', 'أغنيتي', 'Mijn liedjes', 'Mes chansons', 'Meine Lieder', 'Мои песни', 'Mis canciones', 'Benim Şarkılarım'),
(403, '', '', 'my_albums', 'My Albums', 'ألبوماتي', 'Mijn albums', 'Mes albums', 'Meine Alben', 'Мои альбомы', 'Mis albumes', 'Albümlerim'),
(404, '', '', 'create_copyright_dmca_take_down_notice', 'Create copyright DMCA take down notice', 'إنشاء حقوق الطبع والنشر DMCA إنزال إشعار', 'Maak een auteursrechtvermelding voor DMCA-verwijderingen', 'Créer un copyright DMCA prendre note', 'Erstellen Sie Copyright-DMCA-Hinweisbenachrichtigung', 'Создать авторское право DMCA снять уведомление', 'Crear derechos de autor DMCA quitar aviso', 'Telif hakkı DMCA oluştur aşağı bildirimde bulunmak'),
(405, '', '', 'report_coopyright', 'Report Copyright', 'تقرير حقوق الطبع والنشر', 'Meld Copyright', 'Signaler le droit d\'auteur', 'Copyright melden', 'Сообщить об авторском праве', 'Informe de derechos de autor', 'Rapor Telif Hakkı'),
(406, '', '', 'report_copyright', 'Report Copyright', 'تقرير حقوق الطبع والنشر', 'Meld Copyright', 'Signaler le droit d\'auteur', 'Copyright melden', 'Сообщить об авторском праве', 'Informe de derechos de autor', 'Rapor Telif Hakkı'),
(407, '', '', 'create_dmca_take_down_notice', 'Create DMCA take down notice', 'إنشاء DMCA إنزال إشعار', 'Maak een DMCA-kennisgeving voor verwijdering', 'Créer un avis de DMCA', 'Erstellen Sie eine DMCA-Benachrichtigung', 'Создать DMCA снять уведомление', 'Crear aviso de eliminación de DMCA', 'DMCA oluşturma bildirimi al'),
(408, '', '', 'i_have_a_good_faith_belief_that_use_of_the_copyrighted_work_described_above_is_not_authorized_by_the_copyright_owner__its_agent_or_the_law', 'I have a good faith belief that use of the copyrighted work described above is not authorized by the copyright owner, its agent or the law', 'لدي اعتقاد حسن النية بأن استخدام العمل المحمي بحقوق الطبع والنشر الموضح أعلاه غير مصرح به من قبل مالك حقوق الطبع والنشر أو وكيله أو القانون', 'Ik ben er te goeder trouw van overtuigd dat het gebruik van het auteursrechtelijk beschermde werk zoals hierboven beschreven niet is geautoriseerd door de eigenaar van het auteursrecht, diens vertegenwoordiger of de wet', 'J\'ai la conviction de bonne foi que l\'utilisation de l\'œuvre protégée par le droit d\'auteur décrite ci-dessus n\'est pas autorisée par le titulaire du droit d\'auteur, son agent ou la loi', 'Ich bin fest davon überzeugt, dass die Verwendung des oben beschriebenen urheberrechtlich geschützten Werks nicht vom Inhaber des Urheberrechts, seinem Vertreter oder dem Gesetz genehmigt wird', 'Я добросовестно полагаю, что использование авторских прав, описанных выше, не разрешено владельцем авторских прав, его агентом или законом', 'Creo de buena fe que el uso del trabajo con derechos de autor descrito anteriormente no está autorizado por el propietario de los derechos de autor, su agente o la ley', 'Yukarıda açıklanan telif hakkı alınmış çalışmanın kullanımının telif hakkı sahibi, vekili veya yasa tarafından yetkilendirilmediğine dair iyi bir inancım var.'),
(409, '', '', 'i_confirm_that_i_am_the_copyright_owner_or_am_authorised_to_act_on_behalf_of_the_owner_of_an_exclusive_right_that_is_allegedly_infringed.', 'I confirm that I am the copyright owner or am authorised to act on behalf of the owner of an exclusive right that is allegedly infringed.', 'أؤكد أنني مالك حقوق الطبع والنشر أو مفوض بالتصرف نيابة عن مالك الحق الحصري المزعوم انتهاكه.', 'Ik bevestig dat ik de eigenaar van het auteursrecht ben of bevoegd ben om namens de eigenaar van een exclusief recht te handelen dat mogelijk is geschonden.', 'Je confirme que je suis le titulaire du droit d\'auteur ou que je suis autorisé à agir pour le compte du titulaire d\'un droit exclusif prétendument violé.', 'Ich bestätige, dass ich der Inhaber des Urheberrechts bin oder befugt bin, im Namen des Inhabers eines ausschließlichen Rechts zu handeln, das angeblich verletzt wird.', 'Я подтверждаю, что являюсь владельцем авторских прав или уполномочен действовать от имени владельца исключительного права, которое предположительно нарушено.', 'Confirmo que soy el propietario de los derechos de autor o que estoy autorizado para actuar en nombre del propietario de un derecho exclusivo que presuntamente se ha infringido.', 'Telif hakkı sahibinin ya da ihlal edildiği iddia edilen münhasır bir hak sahibi adına hareket etmeye yetkili olduğumu onaylarım.'),
(410, '', '', 'submit', 'Submit', 'خضع', 'voorleggen', 'Soumettre', 'einreichen', 'Отправить', 'Enviar', 'Gönder'),
(411, '', '', 'please_describe_your_request_carefully_and_as_much_as_you_can__note_that_false_dmca_requests_can_lead_to_account_termination.', 'Please describe your request carefully and as much as you can, note that false DMCA requests can lead to account termination.', 'يرجى وصف طلبك بعناية وبقدر ما تستطيع ، لاحظ أن طلبات قانون الألفية الجديدة لحقوق طبع ونشر المواد الرقمية يمكن أن تؤدي إلى إنهاء الحساب.', 'Beschrijf uw verzoek zorgvuldig en voorzover mogelijk, kunnen valse DMCA-verzoeken leiden tot beëindiging van uw account.', 'Veuillez décrire votre demande avec soin et, dans la mesure du possible, notez que les fausses demandes DMCA peuvent entraîner la fermeture du compte.', 'Bitte beschreiben Sie Ihre Anfrage sorgfältig und so oft Sie können. Beachten Sie, dass falsche DMCA-Anfragen zur Kontoauflösung führen können.', 'Пожалуйста, опишите ваш запрос внимательно и как можно больше, обратите внимание, что ложные запросы DMCA могут привести к удалению аккаунта.', 'Describa su solicitud con cuidado y tanto como pueda, tenga en cuenta que las solicitudes falsas de DMCA pueden llevar a la cancelación de la cuenta.', 'Lütfen isteğinizi dikkatlice ve mümkün olduğunca açıklayın, yanlış DMCA taleplerinin hesabınızın feshine yol açabileceğini unutmayın.'),
(412, '', '', 'please_describe_your_request.', 'Please describe your request.', 'يرجى وصف طلبك.', 'Beschrijf alstublieft uw verzoek.', 'S\'il vous plaît décrire votre demande.', 'Bitte beschreiben Sie Ihre Anfrage.', 'Пожалуйста, опишите ваш запрос.', 'Por favor describa su solicitud.', 'Lütfen isteğinizi tanımlayın.'),
(413, '', '', 'please_select_the_checkboxs_below_if_you_own_the_copyright.', 'Please select the checkboxs below if you own the copyright.', 'يرجى تحديد مربعات الاختيار أدناه إذا كنت تملك حقوق الطبع والنشر.', 'Selecteer de selectievakjes hieronder als u de eigenaar bent van het auteursrecht.', 'Veuillez cocher les cases ci-dessous si vous possédez le droit d\'auteur.', 'Bitte wählen Sie die folgenden Kontrollkästchen aus, wenn Sie das Urheberrecht besitzen.', 'Пожалуйста, установите флажки ниже, если вы являетесь владельцем авторских прав.', 'Por favor, seleccione las casillas de verificación a continuación si posee los derechos de autor.', 'Telif hakkına sahipseniz lütfen aşağıdaki onay kutularını seçin.'),
(414, '', '', 'spotlight', 'Spotlight', 'ضوء كشاف', 'Spotlight', 'Projecteur', 'Scheinwerfer', 'Прожектор', 'Destacar', 'spot'),
(415, '', '', 'no_spotlight_tracks_found', 'No spotlight tracks found', 'لم يتم العثور على مسارات الأضواء', 'Geen spotlighttracks gevonden', 'Aucune piste de projecteur trouvée', 'Keine Spotlight-Spuren gefunden', 'Не найдено ни одного трека', 'No se encontraron pistas de foco', 'Spot ışığı bulunamadı'),
(416, '', '', 'spotlight_your_songs', 'Spotlight your songs', 'تسليط الضوء على أغانيك', 'Breng je nummers in de schijnwerpers', 'Mettez en lumière vos chansons', 'Bringen Sie Ihre Songs zum Leuchten', 'Подчеркните свои песни', 'Destaca tus canciones', 'Şarkılarınızı gösterin'),
(417, '', '', 'spotlight_your_songs__feature_', 'Spotlight your songs (feature)', 'تسليط الضوء على أغانيك (ميزة)', 'Breng uw liedjes onder de aandacht (functie)', 'Mettez en lumière vos chansons (fonctionnalité)', 'Spotlight deine Songs (Funktion)', 'Подчеркните свои песни (функция)', 'Destacar tus canciones (característica)', 'Şarkılarınızı vurgulayın (özellik)'),
(418, '', '', 'spotlight_your_songs__featured_', 'Spotlight your songs (featured)', 'تسليط الضوء على أغانيك (مميزة)', 'Breng je liedjes onder de aandacht (aanbevolen)', 'Mettez en lumière vos chansons (en vedette)', 'Bringen Sie Ihre Songs zum Vorschein (vorgestellt)', 'Подчеркните свои песни (лучшее)', 'Destaca tus canciones (destacadas)', 'Şarkılarınızı gösterin (özellikli)'),
(419, '', '', 'embed', 'Embed', 'تضمين', 'insluiten', 'Intégrer', 'Einbetten', 'встраивать', 'Empotrar', 'Göm');
INSERT INTO `langs` (`id`, `ref`, `options`, `lang_key`, `english`, `arabic`, `dutch`, `french`, `german`, `russian`, `spanish`, `turkish`) VALUES
(420, '', '', 'browse', 'Browse', 'تصفح', 'Blader', 'Feuilleter', 'Durchsuche', 'Просматривать', 'Vistazo', 'Araştır'),
(421, '', '', 'no_songs_found_on_this_store.', 'No songs found on this store.', 'لم يتم العثور على أغاني في هذا المتجر.', 'Geen nummers gevonden in deze winkel.', 'Aucune chanson trouvée sur ce magasin.', 'Keine Songs in diesem Shop gefunden.', 'В этом магазине не найдено ни одной песни.', 'No se encontraron canciones en esta tienda.', 'Bu mağazada şarkı bulunamadı.'),
(422, '', '', 'top_seller', 'Top Seller', 'أعلى بائع', 'Top verkoper', 'Meilleur vendeur', 'Topseller', 'Лучший продавец', 'Mejor vendedor', 'En çok satan'),
(423, '', '', 'no_more_followers_found', 'No more followers found', 'لم يتم العثور على المزيد من المتابعين', 'Geen volgers meer gevonden', 'Aucun autre abonné trouvé', 'Keine weiteren Anhänger gefunden', 'Подписчики больше не найдены', 'No se han encontrado más seguidores.', 'Başka takipçi bulunamadı'),
(424, '', '', 'no_followers_found', 'No followers found', 'لم يتم العثور على متابعين', 'Geen volgers gevonden', 'Aucun abonné trouvé', 'Keine Follower gefunden', 'Подписчики не найдены', 'No se encontraron seguidores', 'Takipçi bulunamadı'),
(425, '', '', 'no_more_following_found', 'No more following found', 'لا مزيد من التالية وجدت', 'Niet meer volgen gevonden', 'Aucun autre suivi trouvé', 'Keine weiteren gefunden', 'Следующие не найдены', 'No se han encontrado más seguidores.', 'Başka takip bulunamadı'),
(426, '', '', 'no_following_found', 'No following found', 'لم يتم العثور على التالي', 'Geen volgend gevonden', 'Aucune suite trouvée', 'Kein folgendes gefunden', 'Следующие не найдены', 'No se han encontrado los siguientes', 'Hiçbir takip bulunamadı'),
(427, '', '', 'is_pro_user', 'Is Pro user', 'هو برو المستخدم', 'Is Pro-gebruiker', 'Est l\'utilisateur Pro', 'Ist Pro-Benutzer', 'Про профессионал', 'Es usuario pro', 'Pro kullanıcısı'),
(428, '', '', 'verified', 'Verified', 'التحقق', 'geverifieerd', 'Vérifié', 'Verifiziert', 'проверенный', 'Verificado', 'Doğrulanmış'),
(429, '', '', 'pro_user', 'Pro user', 'برو المستخدم', 'Pro gebruiker', 'Utilisateur pro', 'Pro Benutzer', 'Про пользователь', 'Usuario pro', 'Pro kullanıcı'),
(430, '', '', 'normal_user', 'Normal user', 'مستخدم عادي', 'Normale gebruiker', 'Utilisateur normal', 'Normaler Benutzer', 'Обычный пользователь', 'Usuario normal', 'Normal kullanıcı'),
(431, '', '', 'unverified', 'Unverified', 'غير مثبت عليه', 'geverifieerde', 'Non vérifié', 'Nicht verifiziert', 'непроверенный', 'Inconfirmado', 'doğrulanmamış'),
(432, '', '', 'featured', 'Featured', 'متميز', 'Uitgelicht', 'En vedette', 'Vorgestellt', 'Рекомендуемые', 'Destacados', 'Öne çıkan'),
(433, '', '', 'yes', 'Yes', 'نعم فعلا', 'Ja', 'Oui', 'Ja', 'да', 'Sí', 'Evet'),
(434, '', '', 'no', 'No', 'لا', 'Nee', 'Non', 'Nein', 'нет', 'No', 'Yok hayır'),
(435, '', '', 'like_comment', 'Like Comment', 'مثل تعليق', 'Vind ik leuk Reactie', 'Comme commentaire', 'Wie Kommentar', 'Лайкнуть комментарий', 'Me gusta comentar', 'Beğen Yorum'),
(436, '', '', 'liked_your_comment.', 'liked your comment.', 'أعجبك تعليقك.', 'vond je reactie leuk', 'aimé votre commentaire.', 'mochte dein Kommentar.', 'понравился твой комментарий.', 'Me gustó tu comentario.', 'yorumunu beğendim.'),
(437, '', '', 'unlike_comment', 'UnLike Comment', 'على عكس التعليق', 'UnLike commentaar', 'UnLike Comment', 'Unähnlicher Kommentar', 'UnLike Комментарий', 'No te gusta comentar', 'Yorum beğenmek'),
(438, '', '', 'report_comment.', 'Report comment.', 'الإبلاغ عن تعليق.', 'Rapporteer commentaar.', 'Signaler un commentaire.', 'Kommentar melden', 'Пожаловаться на комментарий.', 'Reportar comentario.', 'Yorum bildir.'),
(439, '', '', 'please_describe_whey_you_want_to_report_this_comment.', 'Please describe whey you want to report this comment.', 'يرجى وصف مصل اللبن الذي تريد الإبلاغ عن هذا التعليق.', 'Geef een beschrijving van wei waarin u deze opmerking wilt melden.', 'Veuillez décrire le lactosérum pour lequel vous souhaitez signaler ce commentaire.', 'Bitte beschreiben Sie, wann Sie diesen Kommentar melden möchten.', 'Пожалуйста, опишите, почему вы хотите сообщить об этом комментарии.', 'Por favor describa si quiere reportar este comentario.', 'Lütfen bu yorumu bildirmek istediğiniz peynir altı suyunu açıklayın.'),
(440, '', '', 'unreport_comment', 'UnReport Comment', 'إلغاء تقرير التعليق', 'Reactie annuleren', 'Unporter un commentaire', 'Kommentar nicht melden', 'Отменить комментарий', 'UnReport Comment', 'Yorumun Raporunu Kaldır'),
(441, '', '', 'the_comment_report_was_successfully_deleted.', 'The comment report was successfully deleted.', 'تم حذف تقرير التعليق بنجاح.', 'Het commentaarrapport is succesvol verwijderd.', 'Le rapport de commentaire a été supprimé avec succès.', 'Der Kommentarbericht wurde erfolgreich gelöscht.', 'Сообщение о комментарии успешно удалено.', 'El informe de comentarios fue eliminado con éxito.', 'Yorum raporu başarıyla silindi.'),
(442, '', '', 'unreport', 'Un Report', 'تقرير الامم المتحدة', 'VN rapport', 'Un rapport', 'Un-Bericht', 'отчитаться', 'Un informe', 'Raporun Kaldırılması'),
(443, '', '', 'the_track_report_was_successfully_deleted.', 'The track report was successfully deleted.', 'تم حذف تقرير المسار بنجاح.', 'Het trackrapport is succesvol verwijderd.', 'Le rapport de suivi a été supprimé avec succès.', 'Der Streckenbericht wurde erfolgreich gelöscht.', 'Отчет о треке был успешно удален.', 'El informe de seguimiento se ha eliminado correctamente.', 'Parça raporu başarıyla silindi.'),
(444, '', '', 'track_comment.', 'Track comment.', 'تتبع التعليق.', 'Volg commentaar.', 'Suivre le commentaire.', 'Kommentar verfolgen', 'Отслеживать комментарии.', 'Seguir el comentario.', 'Yorum takip et.'),
(445, '', '', 'please_describe_whey_you_want_to_report_this_track.', 'Please describe whey you want to report this track.', 'يرجى وصف مصل اللبن الذي تريد الإبلاغ عن هذا المسار.', 'Beschrijf aub de wei die u deze track wilt melden.', 'Veuillez décrire le lactosérum que vous souhaitez signaler sur cette piste.', 'Bitte beschreiben Sie, wann Sie diesen Track melden möchten.', 'Пожалуйста, опишите, почему вы хотите сообщить об этом треке.', 'Describa por qué quiere informar de esta pista.', 'Lütfen bu parçayı bildirmek istediğiniz peynir altı suyunu açıklayın.'),
(446, '', '', 'report_track.', 'Report track.', 'تقرير المسار.', 'Verslag bijhouden.', 'Rapport de piste.', 'Bericht verfolgen', 'Сообщить трек.', 'Informe de seguimiento.', 'İzi rapor et.'),
(447, '', '', 'results_for_', 'results for:', 'نتائج لـ:', 'resultaten voor:', 'résultats pour:', 'Ergebnisse für:', 'результаты для:', 'resultados para:', 'için sonuçlar:'),
(448, '', '', 'what_are_looking_for__', 'What are looking for ?', 'عن ماذا تبحث ؟', 'Waar ben je naar opzoek ?', 'Que recherchons-nous?', 'Was suchst du ?', 'Что ищете?', 'Lo que están buscando ?', 'Neye bakıyorsunuz ?'),
(449, '', '', 'no_more_artists_found', 'No more artists found', 'لم يتم العثور على المزيد من الفنانين', 'Geen artiesten meer gevonden', 'Pas plus d\'artistes trouvés', 'Keine weiteren Künstler gefunden', 'Художники больше не найдены', 'No se han encontrado más artistas.', 'Başka sanatçı bulunamadı'),
(450, '', '', 'no_more_albums_found', 'No more albums found', 'لم يتم العثور على المزيد من الألبومات', 'Geen albums meer gevonden', 'Aucun autre album trouvé', 'Keine weiteren Alben gefunden', 'Больше не найдено альбомов', 'No se han encontrado más álbumes.', 'Başka albüm bulunamadı'),
(452, '', '', 'please_wait', 'please_wait', 'ارجوك انتظر', 'even geduld aub', 'S\'il vous plaît, attendez', 'Warten Sie mal', 'подождите пожалуйста', 'por favor espera', 'lütfen bekle'),
(454, '', '', 'admin_panel', 'Admin Panel', 'لوحة الادارة', 'Administratie Paneel', 'panneau d\'administration', 'Administrationsmenü', 'Панель администратора', 'Panel de administrador', 'Admin Paneli'),
(455, '', '', 'no_messages_found_channel', 'no messages found channel', 'لم يتم العثور على رسائل قناة', 'geen berichten gevonden kanaal', 'aucun message trouvé channel', 'Keine Nachrichten gefunden Kanal', 'Канал сообщений не найден', 'canal no encontrado', 'kanal bulunamadı'),
(456, '', '', 'search', 'search', 'بحث', 'zoeken', 'chercher', 'Suche', 'поиск', 'buscar', 'arama'),
(457, '', '', 'write_message', 'Write message', 'اكتب رسالة', 'Schrijf een bericht', 'Écrire un message', 'Nachricht schreiben', 'Напиши сообщение', 'Escribe un mensaje', 'Mesaj Yaz'),
(458, '', '', 'are_you_sure_you_want_delete_chat', 'Are you sure you want delete chat', 'هل أنت متأكد أنك تريد حذف الدردشة', 'Weet je zeker dat je de chat wilt verwijderen?', 'Êtes-vous sûr de vouloir supprimer le chat?', 'Möchten Sie den Chat wirklich löschen?', 'Вы уверены, что хотите удалить чат', '¿Estás seguro de que quieres eliminar el chat?', 'Sohbeti silmek istediğinize emin misiniz?'),
(459, '', '', 'messages', 'messages', 'رسائل', 'berichten', 'messages', 'Mitteilungen', 'Сообщения', 'mensajes', 'mesajları'),
(460, '', '', 'no_messages_were_found__please_choose_a_channel_to_chat.', 'No messages were found, please choose a channel to chat.', 'لم يتم العثور على رسائل ، يرجى اختيار قناة للدردشة.', 'Er zijn geen berichten gevonden. Kies een kanaal om te chatten.', 'Aucun message n\'a été trouvé, veuillez choisir un canal pour discuter.', 'Es wurden keine Nachrichten gefunden. Bitte wählen Sie einen Kanal zum Chatten.', 'Сообщения не найдены, пожалуйста, выберите канал для чата.', 'No se encontraron mensajes, por favor elige un canal para chatear.', 'Mesaj bulunamadı, lütfen sohbet etmek için bir kanal seçin.'),
(461, '', '', 'no_messages_were_found__say_hi_', 'No messages were found, say Hi!', 'لم يتم العثور على رسائل ، قل مرحبا!', 'Er zijn geen berichten gevonden, bijvoorbeeld Hallo!', 'Aucun message n\'a été trouvé, dites bonjour!', 'Es wurden keine Nachrichten gefunden, sagen Sie Hallo!', 'Сообщения не найдены, скажи привет!', 'No se encontraron mensajes, saludos!', 'Mesaj bulunamadı, merhaba deyin!'),
(462, '', '', 'load_more_messages', 'Load more messages', 'تحميل المزيد من الرسائل', 'Laad meer berichten', 'Charger plus de messages', 'Laden Sie weitere Nachrichten', 'Загрузить больше сообщений', 'Cargar mas mensajes', 'Daha fazla mesaj yükle'),
(463, '', '', 'message', 'message', 'رسالة', 'bericht', 'message', 'Botschaft', 'сообщение', 'mensaje', 'mesaj'),
(464, '', '', 'no_match_found', 'No match found', 'لا يوجد تطابق', 'Geen overeenkomst gevonden', 'Pas de résultat trouvé', 'Keine Übereinstimmung gefunden', 'Совпадение не найдено', 'No se encontraron coincidencias', 'Eşleşme bulunamadı'),
(465, '', '', 'buy', 'Buy', 'يشترى', 'Kopen', 'Acheter', 'Kaufen', 'купить', 'Comprar', 'satın almak'),
(466, '', '', 'you_have_bought_this_album.', 'You have bought this album.', 'لقد اشتريت هذا الألبوم.', 'Je hebt dit album gekocht.', 'Vous avez acheté cet album.', 'Du hast dieses Album gekauft.', 'Вы купили этот альбом.', 'Usted ha comprado este álbum.', 'Bu albümü satın aldınız.'),
(467, '', '', 'price_range', 'Price range', 'نطاق السعر', 'Prijsklasse', 'Échelle des prix', 'Preisklasse', 'Ценовой диапазон', 'Rango de precios', 'Fiyat aralığı'),
(468, '', '', 'you_have_bought_this_track.', 'You have bought this track.', 'لقد اشتريت هذا المسار.', 'Je hebt deze track gekocht.', 'Vous avez acheté cette piste.', 'Sie haben diesen Titel gekauft.', 'Вы купили этот трек.', 'Has comprado esta canción.', 'Bu parçayı aldın.'),
(469, '', '', 'no_more_albums', 'No more albums', 'لا مزيد من الألبومات', 'Geen albums meer', 'Plus d\'albums', 'Keine weiteren Alben', 'Больше нет альбомов', 'No mas discos', 'Başka albüm yok'),
(470, '', '', 'no_more_songs', 'No more songs', 'لا مزيد من الأغاني', 'Geen nummers meer', 'Pas plus de chansons', 'Keine Lieder mehr', 'Больше нет песен', 'No mas canciones', 'Başka şarkı yok'),
(471, '', '', 'no_results_found', 'No results found', 'لا توجد نتائج', 'geen resultaten gevonden', 'Aucun résultat trouvé', 'keine Ergebnisse gefunden', 'результаты не найдены', 'No se han encontrado resultados', 'Sonuç bulunamadı'),
(472, '', '', 'no_albums_found', 'No albums found', 'لم يتم العثور على ألبومات', 'Geen albums gevonden', 'Aucun album trouvé', 'Keine Alben gefunden', 'Альбомы не найдены', 'No se encontraron álbumes', 'Albüm bulunamadı'),
(473, '', '', 'send_as_message', 'Send as message', 'إرسال كرسالة', 'Verzend als bericht', 'Envoyer comme message', 'Als Nachricht senden', 'Отправить как сообщение', 'Enviar como mensaje', 'Mesaj olarak gönder'),
(474, '', '', 'add_a_maximum_of_10_friends_and_send_them_this_track', 'Add a maximum of 10 friends and send them this track', 'أضف 10 أصدقاء كحد أقصى وأرسلهم إلى هذا المسار', 'Voeg maximaal 10 vrienden toe en stuur ze deze track', 'Ajoutez un maximum de 10 amis et envoyez-leur ce morceau', 'Füge maximal 10 Freunde hinzu und sende ihnen diesen Track', 'Добавьте максимум 10 друзей и отправьте им этот трек', 'Agrega un máximo de 10 amigos y envíales esta pista', 'En fazla 10 arkadaş ekle ve bu parçayı gönder'),
(475, '', '', 'get_started', 'Get Started', 'البدء', 'Begin', 'Commencer', 'Loslegen', 'Начать', 'Empezar', 'Başlamak'),
(476, '', '', 'message_sent_successfully', 'Message sent successfully', 'تم إرسال الرسالة بنجاح', 'Bericht succesvol verzonden', 'message envoyé avec succès', 'Nachricht erfolgreich gesendet', 'Сообщение успешно отправлено', 'Mensaje enviado con éxito', 'Mesaj başarıyla gönderildi'),
(477, '', '', 'password_is_too_short', 'Password is too short', 'كلمة المرور قصيرة جدا', 'Wachtwoord is te kort', 'Le mot de passe est trop court', 'Das Passwort ist zu kurz', 'Пароль слишком короткий', 'La contraseña es demasiado corta', 'Şifre çok kısa'),
(478, '', '', 'username_length_must_be_between_5___32', 'Username length must be between 5 / 32', 'يجب أن يتراوح طول اسم المستخدم بين 5/32', 'De lengte van de gebruikersnaam moet tussen 5/32 zijn', 'La longueur du nom d\'utilisateur doit être comprise entre 5/32', 'Die Länge des Benutzernamens muss zwischen 5/32 liegen', 'Длина имени пользователя должна быть между 5/32', 'La longitud del nombre de usuario debe estar entre 5/32', 'Kullanıcı adı uzunluğu 5/32 arasında olmalıdır'),
(479, '', '', 'invalid_username_characters', 'Invalid username characters', 'أحرف اسم المستخدم غير صالحة', 'Ongeldige gebruikersnaamtekens', 'Nom d\'utilisateur invalide', 'Ungültige Zeichen für den Benutzernamen', 'Неверные символы имени пользователя', 'Caracteres de usuario inválidos', 'Geçersiz kullanıcı adı karakterleri'),
(480, '', '', 'this_e-mail_is_invalid', 'This e-mail is invalid', 'هذا البريد الإلكتروني غير صالح', 'Deze email is ongeldig', 'Cette adresse email est invalide', 'Diese E-Mail ist nicht gültig', 'Этот адрес электронной почты недействителен', 'Este email es invalido', 'Bu email geçersizdir'),
(481, '', '', 'you_ain_t_logged_in_', 'You ain&#039;t logged in!', 'لم تقم بتسجيل الدخول!', 'U bent niet ingelogd!', 'Vous n\'êtes pas connecté!', 'Du bist nicht eingeloggt!', 'Вы не авторизованы!', 'Usted no ha iniciado sesión!', 'Giriş yapmadınız!'),
(482, '', '', 'invalid_user_id', 'Invalid user ID', 'هوية مستخدم غير صالحه', 'Ongeldige gebruikersnaam', 'Identifiant invalide', 'Ungültige Benutzer-Id', 'Неверный идентификатор пользователя', 'Identificación de usuario inválida', 'Geçersiz kullanıcı kimliği'),
(483, '', '', 'no_new_releases_found', 'No new releases found', 'لم يتم العثور على إصدارات جديدة', 'Geen nieuwe releases gevonden', 'Aucune nouvelle version trouvée', 'Keine neuen Versionen gefunden', 'Новые выпуски не найдены', 'No se han encontrado nuevos lanzamientos.', 'Yeni sürüm bulunamadı'),
(484, '', '', 'light_mode', 'Light mode', 'وضع الضوء', 'Lichtmodus', 'Mode lumière', 'Lichtmodus', 'Легкий режим', 'Modo de luz', 'Işık modu'),
(485, '', '', '____date___name_', '© |DATE| |NAME|', '© |DATE| |NAME|', '© |DATE| |NAME|', '© |DATE| |NAME|', '© |DATE| |NAME|', '© |DATE| |NAME|', '© |DATE| |NAME|', '© |DATE| |NAME|'),
(486, '', '', 'chat', 'Chat', 'دردشة', 'babbelen', 'Bavarder', 'Plaudern', 'чат', 'Charla', 'Sohbet'),
(487, '', '', 'from_now', 'from now', 'من الان', 'vanaf nu', 'à partir de maintenant', 'in', 'отныне', 'desde ahora', 'şu andan itibaren'),
(488, '', '', 'any_moment_now', 'any moment now', 'في اي لحظة الان', 'elk moment nu', 'n\'importe quel moment maintenant', 'jeden Moment jetzt', 'в любой момент', 'cualquier momento ahora', 'her an şimdi'),
(489, '', '', 'about_a_minute_ago', 'about a minute ago', 'منذ دقيقة واحدة', 'ongeveer een minuut geleden', 'Il y a environ une minute', 'vor ungefähr einer Minute', 'около минуты назад', 'hace alrededor de un minuto', 'yaklaşık bir dakika önce'),
(490, '', '', '_d_minutes_ago', '%d minutes ago', 'منذ %d دقائق', '%d minuten geleden', 'il y %d minutes', 'vor %d Minuten', '%d минут назад', 'Hace %d minutos', '%d dakika önce'),
(491, '', '', 'about_an_hour_ago', 'about an hour ago', 'منذ ساعة تقريبا', 'ongeveer een uur geleden', 'il y a à peu près une heure', 'vor ungefähr einer Stunde', 'около часа назад', 'Hace aproximadamente una hora', 'yaklaşık bir saat önce'),
(492, '', '', '_d_hours_ago', '%d hours ago', 'منذ %d ساعة', '%d uur geleden', 'il y a %d heures', 'Vor %d Stunden', '%d часов назад', 'Hace %d horas', '%d saat önce'),
(493, '', '', 'a_day_ago', 'a day ago', 'قبل يوم واحد', 'een dag geleden', 'il y a un jour', 'vor einem Tag', 'день назад', 'Hace un día', 'bir gün önce'),
(494, '', '', '_d_days_ago', '%d days ago', 'منذ %d أيام', '%d dagen geleden', 'il y a %d jours', 'vor %d Tagen', '%d дней назад', 'Hace %d días', '%d gün önce'),
(495, '', '', 'about_a_month_ago', 'about a month ago', 'منذ شهر تقريبا', 'ongeveer een maand geleden', 'il y a environ un mois', 'vor ungefähr einem Monat', 'Около месяца назад', 'Hace más o menos un mes', 'yaklaşık bir ay önce'),
(496, '', '', '_d_months_ago', '%d months ago', 'منذ %d أشهر', '%d maanden geleden', 'il y a %d mois', 'vor %d Monaten', '%d месяцев назад', 'Hace %d meses', '%d ay önce'),
(497, '', '', 'about_a_year_ago', 'about a year ago', 'قبل نحو سنة', 'ongeveer een jaar geleden', 'il y a un an à peu près', 'vor etwa einem Jahr', 'около года назад', 'Hace un año', 'yaklaşık bir yıl önce'),
(498, '', '', '_d_years_ago', '%d years ago', '%d سنوات مضت', '%d jaar geleden', 'il y a %d années', 'Vor %d Jahren', '%d лет назад', 'hace %d años', '%d yıl önce'),
(504, '', '', 'no_data_to_show', 'No data to show', 'لا توجد بيانات لإظهارها', 'Geen gegevens om te tonen', 'Aucune donnée à afficher', 'Keine Daten zum Anzeigen', 'Нет данных для отображения', 'No hay datos para mostrar', 'Gösterilecek veri yok'),
(505, '', '', 'listen_to_songs', 'Listen to Songs', 'اسمع الاغاني', 'Luister naar liedjes', 'Ecouter des chansons', 'Lieder hören', 'Слушать песни', 'Escuchar canciones', 'Şarkıları dinle'),
(506, '', '', 'discover__stream__and_share_a_constantly_expanding_mix_of_music_from_emerging_and_major_artists_around_the_world.', 'Discover, stream, and share a constantly expanding mix of music from emerging and major artists around the world.', 'اكتشف مجموعة من الموسيقى المتنامية باستمرار من فنانين ناشئين وكبار في جميع أنحاء العالم ، وقم بدفقها ومشاركتها.', 'Ontdek, stream en deel een constant groeiende mix van muziek van opkomende en grote artiesten over de hele wereld.', 'Découvrez, diffusez en streaming et partagez un mélange sans cesse croissant de musique d\'artistes émergents et majeurs du monde entier.', 'Entdecken, streamen und teilen Sie eine ständig wachsende Mischung aus Musik von aufstrebenden und bedeutenden Künstlern auf der ganzen Welt.', 'Откройте для себя, транслируйте и делитесь постоянно растущим миксом музыки от начинающих и крупных артистов по всему миру.', 'Descubra, transmita y comparta una mezcla de música en constante expansión de artistas emergentes e importantes de todo el mundo.', 'Dünyadaki yeni ve büyük sanatçıların sürekli genişleyen bir müzik karışımını keşfedin, yayınlayın ve paylaşın.'),
(507, '', '', 'signup_now', 'Signup Now', 'أفتح حساب الأن', 'Registreer nu', 'S\'inscrire maintenant', 'Jetzt registrieren', 'Войти Сейчас', 'Regístrate ahora', 'Şimdi kayıt ol'),
(508, '', '', 'explore', 'Explore', 'يكتشف', 'onderzoeken', 'Explorer', 'Erkunden', 'Проводить исследования', 'Explorar', 'keşfetmek'),
(509, '', '', 'listen_music_everywhere__anytime', 'Listen Music Everywhere, Anytime', 'استمع إلى الموسيقى في كل مكان وفي أي وقت', 'Luister overal en altijd muziek', 'Écouter de la musique partout, à tout moment', 'Hören Sie überall und jederzeit Musik', 'Слушайте музыку везде, в любое время', 'Escuchar música en cualquier lugar, en cualquier momento', 'Her Zaman, Her Yerde Müzik Dinle'),
(510, '', '', 'lorem_ipsum_dolor_sit_amet__consectetur_adipiscing_elit__sed_do_eiusmod_tempor_incididunt_ut_labore_et_dolore_magna_aliqua.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Lorem ipsum dolor sit amet، consectetur adipiscing elit، sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Lorem ipsum dolor sit amet, consectetur elit adipiscing, sed eiusmod tempor incidid ut labore et dolore magna aliqua.', 'Lorem ipsum dolor sitzen amet, consectetur adipiscing elit, sed do eiusmod temporary incididunt ut labore und dolore magna aliqua.', 'Lorem Ipsum Dolor Sit Amet, Concetetur Adipiscing Elit, Sed do EiusMod Tempor Incididunt U Labore et Dolore Magna Aliqua.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Lorem ipsum dolor amet sitet, adipiscing elit elit, sed do eiusmod tempor inci labunt ve dolore magna aliqua incididunt.'),
(511, '', '', 'create_playlists_with_any_song__on-the-go', 'Create Playlists with any song, On-The-Go', 'إنشاء قوائم التشغيل مع أي أغنية ، على الحركة والتنقل', 'Maak afspeellijsten met elk nummer, On-the-Go', 'Créez des listes de lecture avec n\'importe quelle chanson, On-The-Go', 'Erstellen Sie Wiedergabelisten mit einem beliebigen Song, On-The-Go', 'Создание плейлистов с любой песней, On-The-Go', 'Crea listas de reproducción con cualquier canción, On-The-Go', 'On-The-Go ile herhangi bir şarkıyla Çalma listeleri oluşturma'),
(512, '', '', 'top_trending_artists', 'Top Trending Artists', 'كبار الفنانين تتجه', 'Top trending artiesten', 'Artistes les plus en vogue', 'Top Trending Künstler', 'Лучшие тренды художников', 'Los mejores artistas de tendencias', 'En Popüler Sanatçılar'),
(513, '', '', 'calling_all_creators', 'Calling all creators', 'استدعاء جميع المبدعين', 'Oproep aan alle makers', 'Appel à tous les créateurs', 'Alle Schöpfer anrufen', 'Обращение ко всем создателям', 'Llamando a todos los creadores', 'Tüm yaratıcıları aramak'),
(514, '', '', 'get_on__0__to_connect_with_fans__share_your_sounds__and_grow_your_audience.', 'Get on {0} to connect with fans, share your sounds, and grow your audience.', 'احصل على {0} للتواصل مع المعجبين ومشاركة الأصوات وزيادة عدد جمهورك.', 'Ga op {0} om contact te maken met fans, deel uw geluiden en laat uw publiek groeien.', 'Accédez au {0} pour entrer en contact avec les fans, partager vos sons et développer votre audience.', 'Holen Sie sich auf {0}, um sich mit Fans zu verbinden, Ihre Sounds zu teilen und Ihr Publikum zu vergrößern.', 'Зайдите на {0}, чтобы общаться с фанатами, делиться своими звуками и расширять аудиторию.', 'Súbete a {0} para conectarte con los fanáticos, compartir tus sonidos y aumentar tu audiencia.', 'Hayranlarla bağlantı kurmak, seslerinizi paylaşmak ve izleyicilerinizi büyütmek için {0} alın.'),
(515, '', '', 'upload_songs', 'Upload Songs', 'تحميل الأغاني', 'Songs uploaden', 'Télécharger des chansons', 'Songs hochladen', 'Загрузить песни', 'Subir canciones', 'Şarkı Yükle'),
(516, '', '', 'check_stats', 'Check Stats', 'تحقق الإحصائيات', 'Controleer statistieken', 'Vérifier les statistiques', 'Statistiken überprüfen', 'Проверить статистику', 'Comprobar estadísticas', 'İstatistikleri kontrol et'),
(517, '', '', 'ready_to_rock_your_world.', 'Ready to rock your world.', 'على استعداد لهز العالم الذي تعيشون فيه.', 'Klaar om je wereld te rocken.', 'Prêt à basculer votre monde.', 'Bereit, deine Welt zu rocken.', 'Готов раскачивать свой мир.', 'Listo para sacudir tu mundo.', 'Dünyanızı sarsmaya hazır.'),
(518, '', '', 'search_for_artists__tracks', 'Search for artists, tracks', 'البحث عن الفنانين والمسارات', 'Zoeken naar artiesten, nummers', 'Rechercher des artistes, des pistes', 'Suche nach Künstlern, Tracks', 'Поиск артистов, треков', 'Búsqueda de artistas, pistas', 'Sanatçı, parça ara'),
(520, '', '', 'day_mode', 'Day mode', 'وضع اليوم', 'Dagmodus', 'Mode jour', 'Tagesmodus', 'Дневной режим', 'Modo día', 'Gün modu'),
(521, '', '', 'night_mode', 'Night mode', 'وضع الليل', 'Nachtstand', 'Mode nuit', 'Nacht-Modus', 'Ночной режим', 'Modo nocturno', 'Gece modu'),
(522, '', '', 'interest', 'Interest', 'فائدة', 'Interesseren', 'Intérêt', 'Interesse', 'Интерес', 'Interesar', 'Faiz'),
(523, '', '', 'select_your_music_preference', 'Select your music preference', 'حدد تفضيلات الموسيقى الخاصة بك', 'Selecteer je muziekvoorkeur', 'Sélectionnez votre préférence de musique', 'Wählen Sie Ihre Musikeinstellung aus', 'Выберите ваши музыкальные предпочтения', 'Seleccione su preferencia musical', 'Müzik tercihinizi seçin'),
(524, '', '', 'choose_below_to_start', 'Choose below to start', 'اختر أدناه للبدء', 'Kies hieronder om te starten', 'Choisissez ci-dessous pour commencer', 'Wählen Sie unten aus, um zu beginnen', 'Выберите ниже, чтобы начать', 'Elija a continuación para comenzar', 'Başlamak için aşağıdan seçin'),
(525, '', '', 'next', 'Next', 'التالى', 'volgende', 'Suivant', 'Nächster', 'следующий', 'Siguiente', 'Sonraki'),
(526, '', '', 'you_have_to_choose_your_favorites_genres_below', 'You have to choose your favorites genres below', 'يجب عليك اختيار الأنواع المفضلة لديك أدناه', 'Je moet hieronder je favoriete genres kiezen', 'Vous devez choisir vos genres favoris ci-dessous', 'Sie müssen unten Ihre Lieblingsgenres auswählen', 'Вы должны выбрать свои любимые жанры ниже', 'Debes elegir tus géneros favoritos a continuación', 'Sık kullandığınız türleri aşağıda seçmelisiniz'),
(527, '', '', 'maintenance', 'Maintenance', 'اعمال صيانة', 'Onderhoud', 'Entretien', 'Instandhaltung', 'техническое обслуживание', 'Mantenimiento', 'Bakım'),
(528, '', '', 'website_maintenance_mode_is_active__login_for_user_is_forbidden', 'Website maintenance mode is active, Login for user is forbidden', 'وضع صيانة موقع الويب نشط ، ويُحظر تسجيل الدخول للمستخدم', 'Website onderhoudsmodus is actief, Inloggen voor gebruiker is verboden', 'Le mode de maintenance du site Web est actif, la connexion de l\'utilisateur est interdite.', 'Website-Wartungsmodus ist aktiv, Login für Benutzer ist untersagt', 'Режим обслуживания сайта активен, вход для пользователя запрещен', 'El modo de mantenimiento del sitio web está activo, el inicio de sesión para usuarios está prohibido', 'Web sitesi bakım modu etkin, Kullanıcı girişi yapması yasaktır'),
(529, '', '', 'website_maintenance_mode_is_active', 'Website maintenance mode is active', 'وضع صيانة الموقع نشط', 'De onderhoudsmodus voor de website is actief', 'Le mode de maintenance du site est actif', 'Der Website-Wartungsmodus ist aktiv', 'Режим обслуживания сайта активен', 'El modo de mantenimiento del sitio web está activo', 'Web sitesi bakım modu etkin'),
(530, '', '', 'we___ll_be_back_soon_', 'We’ll be back soon!', 'سنعود قريبا!', 'We zullen snel terug zijn!', 'Nous reviendrons bientôt!', 'Wir werden bald zurück sein!', 'Мы скоро вернемся!', '¡Estaremos de vuelta pronto!', 'Yakında döneceğiz!'),
(531, '', '', 'sorry_for_the_inconvenience_but_we_rsquo_re_performing_some_maintenance_at_the_moment._if_you_need_help_you_can_always', 'Sorry for the inconvenience but we&amp;rsquo;re performing some maintenance at the moment. If you need help you can always', 'نأسف للإزعاج لكننا نقوم ببعض الصيانة في الوقت الحالي. إذا كنت بحاجة إلى مساعدة يمكنك دائما', 'Onze excuses voor het ongemak, maar we voeren momenteel wat onderhoud uit. Als je hulp nodig hebt, kan dat altijd', 'Désolé pour le désagrément occasionné mais nous effectuons actuellement des travaux de maintenance. Si vous avez besoin d\'aide, vous pouvez toujours', 'Wir entschuldigen uns für die Unannehmlichkeiten, aber wir führen momentan einige Wartungsarbeiten durch. Wenn Sie Hilfe brauchen, können Sie das immer tun', 'Приносим извинения за неудобства, но в настоящее время мы проводим техническое обслуживание. Если вам нужна помощь, вы всегда можете', 'Disculpe las molestias, pero estamos realizando algunas tareas de mantenimiento en este momento. Si necesitas ayuda siempre puedes', 'Verdiğimiz rahatsızlıktan dolayı üzgünüz, ancak şu anda bazı bakımlar yapıyoruz. Yardıma ihtiyacınız olursa her zaman'),
(532, '', '', 'otherwise_we_rsquo_ll_be_back_online_shortly_', 'otherwise we&amp;rsquo;ll be back online shortly!', 'وإلا سنعود إلى الإنترنت قريبًا!', 'anders zijn we binnenkort terug online!', 'sinon, nous serons de nouveau en ligne sous peu!', 'Andernfalls sind wir in Kürze wieder online!', 'в противном случае мы скоро вернемся в онлайн!', 'De lo contrario, estaremos de nuevo en línea próximamente.', 'Aksi halde, kısa süre sonra tekrar çevrimiçi olacağız!'),
(533, '', '', 'sorry_for_the_inconvenience_but_we_performing_some_maintenance_at_the_moment._if_you_need_help_you_can_always', 'Sorry for the inconvenience but we performing some maintenance at the moment. If you need help you can always', 'نأسف للإزعاج لكننا نقوم ببعض الصيانة في الوقت الحالي. إذا كنت بحاجة إلى مساعدة يمكنك دائما', 'Excuses voor het ongemak maar we voeren momenteel wat onderhoud uit. Als je hulp nodig hebt, kan dat altijd', 'Désolé pour le désagrément, mais nous effectuons quelques travaux de maintenance pour le moment. Si vous avez besoin d\'aide, vous pouvez toujours', 'Wir entschuldigen uns für die Unannehmlichkeiten, aber wir führen momentan einige Wartungsarbeiten durch. Wenn Sie Hilfe brauchen, können Sie das immer tun', 'Приносим извинения за неудобства, но в данный момент мы выполняем техническое обслуживание. Если вам нужна помощь, вы всегда можете', 'Disculpe las molestias pero estamos realizando algunas tareas de mantenimiento en este momento. Si necesitas ayuda siempre puedes', 'Verdiğimiz rahatsızlıktan dolayı üzgünüz, ancak şu anda biraz bakım yapıyoruz. Yardıma ihtiyacınız olursa her zaman'),
(534, '', '', 'otherwise_we_will_be_back_online_shortly_', 'otherwise we will be back online shortly!', 'وإلا فإننا سوف يعود عبر الإنترنت قريبا!', 'anders zijn we binnenkort weer online!', 'sinon nous serons de nouveau en ligne sous peu!', 'Ansonsten sind wir in Kürze wieder online!', 'в противном случае мы вернемся онлайн в ближайшее время!', 'De lo contrario, volveremos a estar en línea pronto.', 'Aksi takdirde kısa süre sonra tekrar çevrimiçi olacağız!'),
(535, '', '', 'views', 'views', 'الآراء', 'keer bekeken', 'vues', 'Ansichten', 'Просмотры', 'puntos de vista', 'görünümler'),
(536, '', '', 'hide', 'hide', 'إخفاء', 'verbergen', 'cacher', 'verbergen', 'скрывать', 'esconder', 'saklamak'),
(538, '', '', 'your_selection_saved_successfully.', 'Your selection has been updated successfully.', 'تم تحديث اختيارك بنجاح.', 'Uw selectie is succesvol bijgewerkt.', 'Votre sélection a été mise à jour avec succès.', 'Ihre Auswahl wurde erfolgreich aktualisiert.', 'Ваш выбор был успешно обновлен.', 'Su selección ha sido actualizada con éxito.', 'Seçiminiz başarıyla güncellendi.'),
(539, '', '', 'please_wait...', 'Please wait...', 'ارجوك انتظر...', 'Even geduld aub...', 'S\'il vous plaît, attendez...', 'Warten Sie mal...', 'Пожалуйста, подождите...', 'Por favor espera...', 'Lütfen bekle...'),
(540, '', '', 'liked__auser__song_', 'liked |auser| song,', 'أعجبني |auser|  أغنية،', 'leuk |auser| lied,', 'a aimé |auser| chanson,', 'mochte |auser| Lied,', 'понравился |auser| песня,', 'gustado |auser| canción,', 'beğendi |auser| şarkı,'),
(541, '', '', 'shared__auser__song_', 'shared |auser| song,', 'مشترك |auser| أغنية', 'gedeeld |auser| lied,', 'partagé |auser| chanson,', 'geteilt |auser| Lied,', 'поделился |auser| песня,', 'compartido |auser| canción,', 'paylaşılan |auser| şarkı,'),
(542, '', '', 'commented_on__auser__song_', 'commented on |auser| song,', 'وعلق على |auser| أغنية،', 'heeft gereageerd op |auser| lied,', 'a commenté sur |auser| chanson,', 'kommentiert |auser| Lied,', 'прокомментировал |auser| песня,', 'ha comentado en |auser| canción,', '|auser| hakkında yorum yaptı şarkı,'),
(543, '', '', 'uploaded_a_new_song_', 'Uploaded a new song,', 'تم تحميل أغنية جديدة ،', 'Een nieuw nummer ge-upload,', 'Téléchargé une nouvelle chanson,', 'Einen neuen Song hochgeladen,', 'Загрузил новую песню,', 'Subido una nueva canción,', 'Yeni bir şarkı yükledi'),
(544, '', '', 'comments', 'comments', 'تعليقات', 'opmerkingen', 'commentaires', 'Bemerkungen', 'Комментарии', 'comentarios', 'yorumlar'),
(545, '', '', 'upload_single_song', 'Upload single song', 'تحميل أغنية واحدة', 'Upload een nummer', 'Télécharger une seule chanson', 'Einzelnes Lied hochladen', 'Загрузить одну песню', 'Subir una sola canción', 'Tek şarkı yükle'),
(546, '', '', 'upload_an_album', 'Upload an album', 'تحميل ألبوم', 'Upload een album', 'Télécharger un album', 'Lade ein Album hoch', 'Загрузить альбом', 'Subir un álbum', 'Bir albüm yükle'),
(547, '', '', 'thanks_for_your_submission__we_will_review_your_request_shortly.', 'Thanks for your submission, we will review your request shortly.', 'شكرًا على إرسالك ، سنراجع طلبك قريبًا.', 'Bedankt voor uw inzending, we zullen uw verzoek binnenkort beoordelen.', 'Merci pour votre soumission, nous examinerons votre demande sous peu.', 'Vielen Dank für Ihre Eingabe. Wir werden Ihre Anfrage in Kürze prüfen.', 'Спасибо за ваше представление, мы вскоре рассмотрим ваш запрос.', 'Gracias por su envío, revisaremos su solicitud en breve.', 'Gönderdiğiniz için teşekkür ederiz, isteğinizi kısa bir süre sonra gözden geçireceğiz.'),
(548, '', '', 'user_type', 'User Type', 'نوع المستخدم', 'Gebruikerstype', 'Type d\'utilisateur', 'Benutzertyp', 'Тип пользователя', 'Tipo de usuario', 'Kullanıcı tipi'),
(549, '', '', 'years_old', 'years old', 'عمر', 'jaar oud', 'ans', 'Jahre alt\n', 'лет', 'años', 'yaşında'),
(550, '', '', 'login_with_wowonder', 'Login with WoWonder', 'تسجيل الدخول مع Wowonder\n', 'Log in met Wowonder\n', 'Se connecter avec Wowonder\n', 'Mit Wowonder anmelden\n', 'Войти с Wowonder\n', 'Iniciar sesión con Wowonder\n', 'Wowonder ile giriş yap\n'),
(551, '', '', 'balance', 'Balance', 'توازن', 'Balans', 'Équilibre', 'Balance', 'Остаток средств', 'Equilibrar', 'Denge'),
(552, '', '', 'available_balance', 'Available balance', 'الرصيد المتوفر', 'Beschikbaar saldo', 'Solde disponible', 'Verfügbares Guthaben', 'Доступные средства', 'Saldo disponible', 'Kalan bakiye'),
(553, '', '', 'withdrawals', 'Withdrawals', 'السحب', 'onttrekkingen', 'Retraits', 'Abhebungen', 'Изъятия', 'Retiros', 'Çekme'),
(554, '', '', 'paypal_e-mail', 'PayPal E-mail', 'بريد باي بال', 'Paypal E-mail', 'Email Paypal', 'Paypal Email', 'PayPal E-mail', 'E-mail de Paypal', 'PayPal E-posta'),
(555, '', '', 'amount', 'Amount', 'كمية', 'Bedrag', 'Montant', 'Menge', 'Количество', 'Cantidad', 'Miktar'),
(556, '', '', 'min', 'Min', 'دقيقة', 'min', 'Min', 'Mindest', 'Min', 'Min', 'Min'),
(557, '', '', 'submit_withdrawal_request', 'Submit withdrawal request', 'تقديم طلب السحب', 'Verzoek tot opname indienen', 'Soumettre une demande de retrait', 'Auszahlungsantrag stellen', 'Отправить запрос на вывод средств', 'Enviar solicitud de retiro', 'Para çekme isteği gönder'),
(558, '', '', 'status', 'Status', 'الحالة', 'staat', 'Statut', 'Status', 'Статус', 'Estado', 'durum'),
(559, '', '', 'your_withdrawal_request_has_been_successfully_sent_', 'Your withdrawal request has been successfully sent!', 'تم إرسال طلب السحب الخاص بك بنجاح!', 'Uw opnameverzoek is met succes verzonden!', 'Votre demande de retrait a été envoyée avec succès!', 'Ihre Auszahlungsanfrage wurde erfolgreich gesendet!', 'Ваш запрос на вывод средств был успешно отправлен!', 'Su solicitud de retiro ha sido enviada con éxito!', 'Para çekme isteğiniz başarıyla gönderildi!'),
(560, '', '', 'requested_on', 'Requested on', 'طلب على', 'Aangevraagd op', 'Demandé le', 'Beantragt am', 'Запрошено на', 'Solicitado en', 'İstenildi'),
(561, '', '', 'accepted', 'Completed', 'منجز', 'Voltooid', 'Terminé', 'Abgeschlossen', 'Завершенный', 'Terminado', 'Tamamlanan'),
(562, '', '', 'rejected', 'Rejected', 'مرفوض', 'Verworpen', 'Rejeté', 'Abgelehnt', 'Отклонено', 'Rechazado', 'Reddedilen'),
(563, '', '', 'pending', 'Pending', 'قيد الانتظار', 'In afwachting', 'en attendant', 'steht aus', 'в ожидании', 'Pendiente', 'kadar'),
(564, '', '', 'the_amount_exceeded_your_current_balance.', 'The amount exceeded your current balance.', 'تجاوز المبلغ رصيدك الحالي.', 'Het bedrag heeft uw huidige saldo overschreden.', 'Le montant a dépassé votre solde actuel.', 'Der Betrag hat Ihr aktuelles Guthaben überschritten.', 'Сумма превысила ваш текущий баланс.', 'La cantidad superó su saldo actual.', 'Tutar, mevcut bakiyenizi aştı.'),
(565, '', '', 'minimum_amount_required_is_50.', 'Minimum amount required is 50.', 'الحد الأدنى للمبلغ المطلوب هو 50.', 'Minimum vereiste hoeveelheid is 50.', 'Le montant minimum requis est de 50.', 'Mindestbetrag ist 50.', 'Минимальная необходимая сумма составляет 50.', 'La cantidad mínima requerida es de 50.', 'Gereken minimum miktar 50.'),
(566, '', '', 'second_lorem_ipsum_dolor_sit_amet__consectetur_adipiscing_elit__sed_do_eiusmod_tempor_incididunt_ut_labore_et_dolore_magna_aliqua__2', 'Second Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua, 2', 'Second Lorem ipsum dolor sit amet، consectetur adipiscing elit، sed do eiusmod tempor incididunt ut labore et dolore magna aliqua، 2', 'Tweede Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua, 2', 'Deuxième Lorem ipsum dolor sit amet, consectetur elit adipiscing, séduit temporairement incididunt ut labore et dolore magna aliqua, 2', 'Zweite Lorem ipsum dolor sitzen amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore und dolore magna aliqua, 2', 'Second Lorem Ipsum Dolor Sit Amet, Concetetur Adipiscing Elit, Sed do EiusMod Tempor Incididunt U Labore et Dolore Magna Aliqua, 2', 'Segundo Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua, 2', 'İkinci Lorem ipsum dolor amet sitet, adipiscing adipiscing elit, sed do eiusmod tempor incidire ve labore ve dolore magna aliqua, 2'),
(567, '', '', 'you_can_not_submit_withdrawal_request_until_the_previous_requests_has_been_approved___rejected', 'You can not submit withdrawal request until the previous requests has been approved / rejected', 'لا يمكنك إرسال طلب السحب حتى تتم الموافقة على / رفض الطلبات السابقة', 'U kunt geen opnameverzoek indienen totdat de vorige verzoeken zijn goedgekeurd / afgewezen', 'Vous ne pouvez pas soumettre de demande de retrait avant que les demandes précédentes aient été approuvées / rejetées', 'Sie können eine Auszahlungsanforderung erst absenden, wenn die vorherigen Anforderungen genehmigt / abgelehnt wurden', 'Вы не можете отправить запрос на снятие средств, пока предыдущие запросы не были одобрены / отклонены', 'No puede enviar una solicitud de retiro hasta que las solicitudes anteriores hayan sido aprobadas / rechazadas', 'Önceki istekler onaylanıp reddedilene kadar para çekme isteği gönderemezsiniz.'),
(568, '', '', 'get_verified__get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Get verified, get a special looking profile and get famous on our platform!', 'الحصول على التحقق ، والحصول على ملف تعريف خاص المظهر والحصول على شهرة على منصة لدينا!', 'Word geverifieerd, krijg een speciaal uitziend profiel en word beroemd op ons platform!', 'Faites-vous vérifier, obtenez un profil spécial et devenez célèbre sur notre plateforme!', 'Lassen Sie sich verifizieren, erhalten Sie ein besonderes Profil und werden Sie auf unserer Plattform berühmt!', 'Пройдите проверку, получите специальный профиль и станьте известным на нашей платформе!', '¡Verifíquese, obtenga un perfil especial y vuélvase famoso en nuestra plataforma!', 'Doğrulayın, özel bir görünüme sahip olun ve platformumuzda ünlü olun!'),
(569, '', '', 'purchases', 'Purchases', 'المشتريات', 'Aankopen', 'Achats', 'Einkäufe', 'Покупки', 'Compras', 'alımları'),
(570, '', '', 'select_payment_method.', 'Select a payment method', 'اختر طريقة الدفع', 'Kies een betalingsmethode', 'Choisissez une méthode de paiement', 'Wählen Sie eine Zahlungsmethode', 'Выберите метод оплаты', 'Seleccione un método de pago', 'Ödeme Yöntemini Seçin'),
(571, '', '', 'choose_a_payment_method.', 'Choose a payment method', 'اختيار طريقة الدفع', 'Kies een betaal methode', 'Choisissez une méthode de paiement', 'Wählen Sie eine Bezahlungsart', 'Выберите способ оплаты', 'Elija un método de pago', 'Bir ödeme yöntemi seçin'),
(572, '', '', 'track_purchase', 'Purchase Track ', 'شراء المسار', 'Kooptraject', 'Piste d\'achat', 'Kauf Track', 'Трек покупки', 'Pista de compra', 'Satın Alma Takibi'),
(573, '', '', 'credit_card', 'Credit Card', 'بطاقة الائتمان', 'Kredietkaart', 'Carte de crédit', 'Kreditkarte', 'Кредитная карта', 'Tarjeta de crédito', 'Kredi kartı'),
(574, '', '', 'bank_transfer', 'Bank Transfer', 'التحويل المصرفي', 'Overschrijving', 'Virement', 'Banküberweisung', 'Банковский перевод', 'Transferencia bancaria', 'Banka transferi'),
(575, '', '', 'note', 'Note', 'ملحوظة', 'Notitie', 'Remarque', 'Hinweis', 'Заметка', 'Nota', 'Not'),
(576, '', '', 'please_transfer_the_amount_of', 'Please transfer the amount of', 'يرجى تحويل كمية', 'Gelieve het bedrag over te maken', 'S\'il vous plaît transférer le montant de', 'Bitte überweisen Sie den Betrag von', 'Пожалуйста, перечислите сумму', 'Por favor transfiera la cantidad de', 'Lütfen tutarını aktarın'),
(577, '', '', 'to_this_bank_account_to_buy', 'to this bank account to buy', 'لهذا الحساب المصرفي للشراء', 'om deze bankrekening te kopen', 'à ce compte bancaire pour acheter', 'auf dieses Bankkonto zu kaufen', 'на этот банковский счет, чтобы купить', 'a esta cuenta bancaria para comprar', 'bu banka hesabına'),
(578, '', '', 'upload_receipt', 'Upload Receipt', 'تحميل الإيصال', 'Upload ontvangst', 'Télécharger le reçu', 'Quittung hochladen', 'Загрузить квитанцию', 'Cargar Recibo', 'Makbuzu Yükle'),
(579, '', '', 'confirm', 'Confirm', 'تؤكد', 'Bevestigen', 'Confirmer', 'Bestätigen', 'подтвердить', 'Confirmar', 'Onaylamak'),
(580, '', '', 'your_receipt_uploaded_successfully.', 'Your receipt  has been uploaded successfully.', 'تم تحميل إيصالك بنجاح.', 'Uw kwitantie is succesvol geüpload.', 'Votre reçu a été téléchargé avec succès.', 'Ihre Quittung wurde erfolgreich hochgeladen.', 'Ваша квитанция была успешно загружена.', 'Su recibo ha sido cargado con éxito.', 'Makbuzunuz başarıyla yüklendi.'),
(581, '', '', 'we_approved_your_bank_transfer_of__d_', 'We approved your bank transfer of %d!', 'لقد وافقنا على تحويلك المصرفي لـ٪ d!', 'We hebben uw overboeking van% d goedgekeurd!', 'Nous avons approuvé votre virement bancaire de% d!', 'Wir haben Ihre Überweisung von% d genehmigt!', 'Мы одобрили ваш банковский перевод% d!', '¡Aprobamos su transferencia bancaria de% d!', '% D banka havalenizi onayladık!'),
(582, '', '', 'we_have_rejected_your_bank_transfer__please_contact_us_for_more_details.', 'We have rejected your bank transfer, please contact us for more details.', 'لقد رفضنا تحويلك المصرفي ، يرجى الاتصال بنا للحصول على مزيد من التفاصيل.', 'We hebben uw overboeking geweigerd. Neem contact met ons op voor meer informatie.', 'Nous avons refusé votre virement bancaire, veuillez nous contacter pour plus de détails.', 'Wir haben Ihre Überweisung abgelehnt. Bitte kontaktieren Sie uns für weitere Informationen.', 'Мы отклонили ваш банковский перевод, пожалуйста, свяжитесь с нами для получения более подробной информации.', 'Hemos rechazado su transferencia bancaria, póngase en contacto con nosotros para obtener más detalles.', 'Banka havalenizi reddettik, daha fazla bilgi için lütfen bizimle iletişime geçin.'),
(583, '', '', 'dislike', 'Dislike', 'لم يعجبنى', 'Afkeer', 'Ne pas aimer', 'Nicht gefallen', 'нелюбовь', 'Disgusto', 'Beğenmemek'),
(584, '', '', 'disliked', 'Disliked', 'لم يعجبني', 'bevallen', 'N\'a pas aimé', 'Unzufrieden mit:', 'Не понравилось', 'No me gustó', 'Beğenmedim'),
(585, '', '', 'paypal', 'PayPal', 'باي بال', 'PayPal', 'Pay Pal', 'PayPal', 'PayPal', 'PayPal', 'PayPal'),
(586, '', '', 'read_more', 'Read more', 'اقرأ أكثر', 'Lees verder', 'Lire la suite', 'Weiterlesen', 'Подробнее', 'Lee mas', 'Daha fazla oku'),
(587, '', '', 'read_less', 'Read less', 'أقرأ أقل', 'Lees minder', 'Lire moins', 'Lese weniger', 'Читать меньше', 'Leer menos', 'Az oku'),
(588, '', '', 'this_username_is_disallowed', 'This username is not allowed', 'اسم المستخدم هذا غير مسموح به', 'Deze gebruikersnaam is niet toegestaan', 'Ce nom d\'utilisateur n\'est pas autorisé', 'Dieser Benutzername ist nicht erlaubt', 'Это имя пользователя не допускается', 'Este nombre de usuario no está permitido', 'Bu kullanıcı adına izin verilmiyor'),
(589, '', '', 'disliked__auser__song_', 'disliked |auser| song,', 'كراهية  |auser| أغنية،', 'niet leuk |auser| lied,', 'n\'a pas aimé |auser| chanson,', 'unzufrieden mit: |auser| Lied,', 'не понравился |auser| песня,', 'no me gustó |auser| canción,', 'beğenmedi |auser| şarkı,'),
(590, '', '', 'statistics', 'Statistics', 'الإحصاء', 'Statistieken', 'Statistiques', 'Statistiken', 'Статистика', 'Estadística', NULL),
(591, '', '', 'total_views', 'Total Views', 'عدد المشاهدات', 'Totaal aantal weergaven', 'Vues totales', 'Gesamtansichten', 'Всего просмотров', 'Vistas totales', 'Toplam görüntülenme'),
(592, '', '', 'total_likes', 'Total Likes', 'إجمالي الإعجابات', 'Totaal houdt van', 'Total de J\'aime', 'Gesamte Likes', 'Всего лайков', 'Me gusta en total', 'Toplam Beğeniler'),
(593, '', '', 'total_dislikes', 'Total Dislikes', 'إجمالي الكراهية', 'Totaal houdt niet van', NULL, 'Total Abneigungen', 'Всего не нравится', 'Aversiones totales', 'Toplam Beğenmedim'),
(594, '', '', 'today', 'Today', 'اليوم', NULL, 'Aujourd\'hui', NULL, 'сегодня', NULL, 'Bugün'),
(595, '', '', 'this_week', 'This week', 'هذا الاسبوع', 'Deze week', 'Cette semaine', 'Diese Woche', 'На этой неделе', 'Esta semana', 'Bu hafta'),
(596, '', '', 'this_month', 'This month', 'هذا الشهر', 'Deze maand', 'Ce mois-ci', 'Diesen Monat', 'Этот месяц', 'Este mes', 'Bu ay'),
(597, '', '', 'dislikes', 'dislikes', 'يكره', 'houdt niet van', 'n\'aime pas', 'Abneigungen', 'Не понравилось:', 'aversiones', 'sevmediği'),
(598, '', '', 'allow_downloads', 'Allow downloads', 'السماح بالتنزيلات', 'Downloads toestaan', NULL, 'Downloads zulassen', 'Разрешить загрузки', NULL, 'İndirmeye izin ver'),
(599, '', '', 'display_embed_code', 'Display embed code', 'عرض رمز التضمين', 'Toon insluitcode', 'Afficher le code intégré', 'Einbettungscode anzeigen', 'Показать код для вставки', 'Mostrar código de inserción', 'Katıştırma kodunu göster'),
(600, '', '', 'lyrics', 'Lyrics', 'كلمات الاغنية', NULL, 'Paroles', 'Text', 'Текст песни', 'Letra', 'Şarkı sözleri'),
(601, '', '', 'show_more', 'Show more', 'أظهر المزيد', 'Laat meer zien', 'Montre plus', 'Zeig mehr', 'Показать больше', 'Mostrar más', 'Daha fazla göster'),
(602, '', '', 'show_less', 'Show less', 'عرض أقل', 'Laat minder zien', 'Montre moins', 'Zeige weniger', 'Показывай меньше', 'Muestra menos', 'Daha az göster'),
(603, '', '', 'no_payment_method_available.', 'No payment method available.', 'لا توجد طريقة الدفع المتاحة.', 'Geen betaalmethode beschikbaar.', 'Aucun mode de paiement disponible.', 'Keine Zahlungsmethode verfügbar.', 'Нет способа оплаты.', 'No hay método de pago disponible.', 'Ödeme yöntemi yok.'),
(604, '', '', 'upload_multiple_songs', 'Upload songs', 'تحميل الأغاني', 'Upload liedjes', 'Télécharger des chansons', 'Songs hochladen', 'Загрузить песни', NULL, 'Şarkı yükle'),
(605, '', '', 'add_multiple_songs', 'Add Songs', 'أضف أغاني', 'Voeg liedjes toe', 'Ajouter des chansons', 'Songs hinzufügen', 'Добавить песни', 'Añadir canciones', 'Şarkı ekle'),
(606, '', '', 'no_users_found', 'No users found', 'لم يتم العثور على المستخدمين', 'Geen gebruikers gevonden', 'Aucun utilisateur trouvé', 'Keine Benutzer gefunden', 'Пользователи не найдены', 'No se encontraron usuarios', 'Kullanıcı bulunamadı'),
(607, '', '', 'show_only_in_track_page', 'Show only in track page', 'تظهر فقط في صفحة المسار', 'Alleen weergeven op trackpagina', 'Afficher uniquement dans la page de piste', 'Nur auf der Titelseite anzeigen', 'Показывать только на странице трека', 'Mostrar solo en la página de seguimiento', 'Sadece parça sayfasında göster'),
(608, '', '', 'show_on_all_pages', 'Show on all pages', 'عرض في كل الصفحات', 'Weergeven op alle pagina\'s', 'Montrer sur toutes les pages', 'Auf allen Seiten anzeigen', 'Показать на всех страницах', 'Mostrar en todas las páginas', 'Tüm sayfalarda göster'),
(609, '', '', 'edit_spotlight', 'Edit Spotlight', 'تحرير الأضواء', 'Spotlight bewerken', 'Modifier Spotlight', 'Spotlight bearbeiten', 'Редактировать Прожектор', 'Editar Spotlight', 'Spot Işığını Düzenle'),
(610, '', '', 'blog', 'Blog', 'مدونة', 'blog', 'Blog', 'Blog', 'Блог', 'Blog', 'Blog'),
(611, 'blog_categories', '', '1309', 'Comedy', 'كوميديا', 'Komedie', 'La comédie', 'Komödie', 'комедия', 'Comedia', 'Komedi'),
(612, 'blog_categories', '', '1310', 'Cars and Vehicles', 'السيارات والمركبات', 'Auto\'s en voertuigen', 'Voitures et véhicules', 'Autos und Fahrzeuge', 'Автомобили и транспортные средства', 'Autos y vehiculos', 'Otomobiller ve Araçlar'),
(613, 'blog_categories', '', '1311', 'Economics and Trade', 'الاقتصاد والتجارة', 'Economie en handel', 'Économie et commerce', 'Wirtschaft und Handel', 'Экономика и торговля', 'Economía y comercio', 'Ekonomi ve Ticaret'),
(614, 'blog_categories', '', '1312', 'Education', 'التعليم', 'Opleiding', 'Éducation', 'Bildung', 'образование', 'Educación', 'Eğitim');
INSERT INTO `langs` (`id`, `ref`, `options`, `lang_key`, `english`, `arabic`, `dutch`, `french`, `german`, `russian`, `spanish`, `turkish`) VALUES
(615, 'blog_categories', '', '1313', 'Entertainment', 'وسائل الترفيه', 'vermaak', 'Divertissement', 'Unterhaltung', 'Развлечения', 'Entretenimiento', 'Eğlence'),
(616, 'blog_categories', '', '1314', 'Movies & Animation', 'أفلام', 'Films', 'Films', 'Filme', 'Кино', 'Películas', 'filmler'),
(617, 'blog_categories', '', '1315', 'Gaming', 'الألعاب', 'gaming', 'Jeu', 'Gaming', 'азартные игры', 'Juego de azar', 'kumar'),
(618, 'blog_categories', '', '1316', 'History and Facts', 'التاريخ والحقائق', 'Geschiedenis en feiten', 'Histoire et faits', 'Geschichte und Fakten', 'История и факты', 'Historia y hechos', 'Tarihçe ve Gerçekler'),
(619, 'blog_categories', '', '1317', 'Live Style', 'نمط الحياة', 'Levensstijl', 'Style de vie', 'Lebensstil', 'Стиль жизни', 'Estilo de vida', 'Yaşam tarzı'),
(620, 'blog_categories', '', '1318', 'Natural', 'طبيعي >> صفة', 'natuurlijk', 'Naturel', 'Natürlich', 'натуральный', 'Natural', 'Doğal'),
(621, 'blog_categories', '', '1319', 'News and Politics', 'الأخبار والسياسة', 'Nieuws en politiek', 'Nouvelles et politique', 'Nachrichten und Politik', 'Новости и Политика', 'Noticias y politica', 'Haberler ve Politika'),
(622, 'blog_categories', '', '1320', 'People and Nations', 'الناس والأمم', 'Mensen en naties', 'Peuples et Nations', 'Menschen und Nationen', 'Люди и народы', 'Pueblos y naciones', 'İnsanlar ve Milletler'),
(623, 'blog_categories', '', '1321', 'Pets and Animals', 'الحيوانات الأليفة والحيوانات', 'Huisdieren en dieren', 'Animaux et Animaux', 'Haustiere und Tiere', 'Домашние животные и животные', 'Mascotas y animales', 'Evcil Hayvanlar ve Hayvanlar'),
(624, 'blog_categories', '', '1322', 'Places and Regions', 'الأماكن والمناطق', 'Plaatsen en regio\'s', 'Lieux et régions', 'Orte und Regionen', 'Места и Регионы', 'Lugares y Regiones', 'Yerler ve Bölgeler'),
(625, 'blog_categories', '', '1323', 'Science and Technology', 'العلوم والتكنولوجيا', 'Wetenschap en technologie', 'Science et technologie', 'Wissenschaft und Technik', 'Наука и технология', 'Ciencia y Tecnología', 'Bilim ve Teknoloji'),
(626, 'blog_categories', '', '1324', 'Sport', 'رياضة', 'Sport', 'Sport', 'Sport', 'Sport', 'Deporte', 'Spor'),
(627, 'blog_categories', '', '1325', 'Travel and Events', 'السفر والأحداث', 'Reizen en evenementen', 'Voyage et événements', 'Reisen und Veranstaltungen', 'Путешествия и События', 'Viajes y eventos', 'Seyahat ve Etkinlikler'),
(628, 'blog_categories', '', '1326', 'Other', 'آخر', 'anders', 'Autre', 'Andere', 'Другие', 'Otro', 'Diğer'),
(629, '', '', 'read_more', 'Read more', 'اقرأ أكثر', 'Lees verder', 'Lire la suite', 'Weiterlesen', 'Подробнее', 'Lee mas', 'Daha fazla oku'),
(630, '', '', 'categories', 'Categories', 'الاقسام', 'Categorieën', 'Les catégories', 'Kategorien', 'категории', 'Categorias', 'Kategoriler'),
(631, '', '', 'no_more_articles_to_show.', 'No more articles to show.', 'لا مزيد من المقالات لإظهارها.', 'Geen artikelen meer om te tonen.', 'Pas plus d\'articles à montrer.', 'Keine weiteren Artikel zum Anzeigen.', 'Нет больше статей, чтобы показать.', 'No hay más artículos para mostrar.', 'Gösterilecek başka makale yok.'),
(632, '', '', 'article', 'Article', 'مقالة - سلعة', 'Artikel', 'Article', 'Artikel', 'Статья', 'Artículo', 'makale'),
(633, '', '', 'share_to', 'Share to', 'حصة ل', 'Delen naar', 'Partager à', 'Teilen mit', 'Поделиться с', 'Compartir a', 'Paylaş'),
(634, '', '', 'blogs', 'Blogs', 'المدونات', 'blogs', 'Blogs', 'Blogs', 'Блоги', 'Blogs', 'Bloglar'),
(635, '', '', 'no_more_articles', 'No more articles to show', 'لا مزيد من المقالات لإظهارها', 'Geen artikelen meer om te tonen', 'Plus d\'articles à afficher', 'Keine weiteren Artikel zum Anzeigen', 'Нет больше статей для показа', 'No hay más artículos para mostrar.', 'Gösterilecek başka makale yok'),
(636, '', '', 'no_more_article_found', 'No more articles found', 'لم يتم العثور على المزيد من المقالات', 'Geen artikelen meer gevonden', 'Pas plus d\'articles trouvés', 'Keine weiteren Artikel gefunden', 'Больше статей не найдено', 'No se encontraron más artículos.', 'Başka makale bulunamadı'),
(637, '', '', 'delete_song', 'Delete Song', 'حذف أغنية', 'Nummer verwijderen', 'Supprimer la chanson', 'Song löschen', 'Удалить песню', 'Eliminar canción', 'Şarkıyı Sil'),
(638, '', '', 'delete_this_song_from_playlist', 'Remove from playlist', 'إزالة من قائمة التشغيل', 'Verwijderen uit afspeellijst', 'Supprimer de la playlist', 'Von der Wiedergabeliste entfernen', 'Удалить из плейлиста', 'Eliminar de la lista de reproducción', 'Oynatma listesinden kaldır'),
(639, '', '', 'please_enter_song_description', 'Please enter the song description', 'الرجاء إدخال وصف الأغنية', 'Voer de nummerbeschrijving in', 'S\'il vous plaît entrer la description de la chanson', 'Bitte geben Sie die Songbeschreibung ein', 'Пожалуйста, введите описание песни', 'Por favor ingrese la descripción de la canción', 'Lütfen şarkı açıklamasını girin'),
(640, '', '', 'please_enter_song_tags', 'Please enter song\'s tags', 'الرجاء إدخال علامات الأغنية', 'Voer de tags van het nummer in', 'S\'il vous plaît entrer les tags de la chanson', 'Bitte geben Sie die Tags des Songs ein', 'Пожалуйста, введите теги песни', 'Por favor, introduzca las etiquetas de la canción', 'Lütfen şarkının etiketlerini giriniz'),
(641, '', '', 'please_upload_song_thumbnail', 'Please upload song thumbnail', 'يرجى تحميل صورة مصغرة للأغنية', 'Upload een miniatuur van het nummer', 'S\'il vous plaît télécharger la vignette de la chanson', 'Bitte laden Sie das Miniaturbild des Songs hoch', 'Пожалуйста, загрузите миниатюру песни', 'Sube la miniatura de la canción', 'Lütfen şarkı küçük resmini yükleyin'),
(642, '', '', 'you_have_reached_your_upload_limit__upgrade_to_upload_unlimited_songs.', 'You have reached your upload limit, upgrade to upload unlimited songs.', 'لقد وصلت إلى الحد الأقصى للتحميل ، قم بالترقية لتحميل الأغاني غير المحدودة.', 'Je hebt je uploadlimiet bereikt, upgrade om onbeperkte nummers te uploaden.', 'Vous avez atteint votre limite de téléchargement, effectuez une mise à niveau pour télécharger des morceaux illimités.', 'Sie haben Ihr Upload-Limit erreicht. Aktualisieren Sie, um unbegrenzt viele Songs hochzuladen.', 'Вы достигли предела загрузки, обновите, чтобы загрузить неограниченное количество песен.', 'Ha alcanzado su límite de carga, actualice para cargar canciones ilimitadas.', 'Yükleme sınırınıza ulaştınız, sınırsız şarkı yüklemek için yükseltme yapın.'),
(643, '', '', 'advertising', 'Advertising', 'إعلان', 'Advertising', 'La publicité', 'Werbung', 'реклама', 'Publicidad', 'reklâm'),
(644, '', '', 'wallet', 'Wallet', 'محفظة نقود', 'Portemonnee', 'Portefeuille', 'Brieftasche', 'Кошелек', 'Billetera', 'Cüzdan'),
(645, '', '', 'create_ad', 'New Campaign', 'حملة جديدة', 'Nieuwe campagne', 'Nouvelle campagne', 'Neue Kampagne', 'Новая кампания', 'Nueva campaña', 'Yeni Kampanya'),
(646, '', '', 'category', 'Category', 'الفئة', 'Categorie', 'Catégorie', 'Kategorie', 'категория', 'Categoría', 'Kategori'),
(647, '', '', 'results', 'Results', 'النتائج', 'resultaten', 'Résultats', 'Ergebnisse', 'Полученные результаты', 'Resultados', 'Sonuçlar'),
(648, '', '', 'spent', 'Spent', 'أنفق', 'besteed', 'Dépensé', 'Verbraucht', 'потраченный', 'Gastado', 'harcanmış'),
(649, '', '', 'action', 'Action', 'عمل', 'Actie', 'Action', 'Aktion', 'действие', 'Acción', 'Aksiyon'),
(650, '', '', '2checkout', '2Checkout', '2Checkout', '2Checkout', '2Checkout', '2Kasse', '2Checkout', '2Caja', '2Checkout'),
(651, '', '', 'address', 'Address', 'عنوان', 'Adres', 'Adresse', 'Adresse', 'Адрес', 'Dirección', 'Adres'),
(652, '', '', 'city', 'City', 'مدينة', 'stad', 'Ville', 'Stadt', 'город', 'Ciudad', 'Kent'),
(653, '', '', 'state', 'State', 'حالة', 'Staat', 'Etat', 'Zustand', 'государственный', 'Estado', 'Belirtmek, bildirmek'),
(654, '', '', 'zip', 'Zip', 'الرمز البريدي', 'ritssluiting', 'Zip *: français', 'Postleitzahl', 'застежка-молния', 'Cremallera', 'Zip'),
(655, '', '', 'phone_number', 'Phone number', 'رقم الهاتف', 'Telefoonnummer', 'Numéro de téléphone', 'Telefonnummer', 'Номер телефона', 'Número de teléfono', 'Telefon numarası'),
(656, '', '', 'card_number', 'Card Number', 'رقم البطاقة', 'Kaartnummer', 'Numéro de carte', 'Kartennummer', 'Номер карты', 'Número de tarjeta', 'Kart numarası'),
(657, '', '', 'pay', 'Pay', 'دفع', 'Betalen', 'Payer', 'Zahlen', 'Оплатить', 'Paga', 'Ödemeli'),
(658, '', '', 'replenish', 'Replenish', 'ملئ', 'Replenish', 'Remplir', 'Ergänzen', 'Восполнение', 'Reponer', 'Yenileyici'),
(659, '', '', 'please_check_the_details', 'Please check the details', 'يرجى التحقق من التفاصيل', 'Controleer de details', 'S\'il vous plaît vérifier les détails', 'Bitte überprüfen Sie die Details', 'Пожалуйста, проверьте детали', 'Por favor revise los detalles', 'Lütfen detayları kontrol et'),
(660, '', '', 'confirmation', 'Confirmation', 'التأكيد', 'Bevestiging', 'Confirmation', 'Bestätigung', 'подтверждение', 'Confirmación', 'Onayla'),
(661, '', '', 'are_you_sure_you_want_to_delete_this_ad_', 'Are you sure you want to delete this campaign?', 'هل أنت متأكد أنك تريد حذف هذه الحملة؟', 'Weet u zeker dat u deze campagne wilt verwijderen?', 'Êtes-vous sûr de vouloir supprimer cette campagne?', 'Möchten Sie diese Kampagne wirklich löschen?', 'Вы уверены, что хотите удалить эту кампанию?', '¿Estás seguro de que deseas eliminar esta campaña?', 'Bu kampanyayı silmek istediğinize emin misiniz?'),
(662, '', '', 'deleted', 'deleted', 'تم الحذف', 'verwijderde', 'supprimé', 'gelöscht', 'Исключен', 'eliminado', 'silindi'),
(663, '', '', 'please_check_details', 'please_check_details', 'please_check_details', 'please_check_details', 'please_check_details', 'please_check_details', 'please_check_details', 'please_check_details', 'please_check_details'),
(664, '', '', 'confirming_your_payment__please_wait..', 'Confirming your payment, please wait..', 'تأكيد الدفع الخاص بك ، يرجى الانتظار ..', 'Bevestiging van uw betaling, even geduld aub ..', 'Confirmant votre paiement, veuillez patienter ..', 'Bitte warten Sie, bis die Zahlung bestätigt wurde.', 'Подтверждение оплаты, пожалуйста, подождите ..', 'Confirmando su pago, por favor espere ...', 'Ödemenizi onaylayın, lütfen bekleyin ..'),
(665, '', '', 'payment_declined__please_try_again_later.', 'Payment declined, please try again later.', 'تم رفض الدفع ، يرجى المحاولة مرة أخرى لاحقًا.', 'Betaling geweigerd, probeer het later opnieuw.', 'Paiement refusé, veuillez réessayer plus tard.', 'Zahlung abgelehnt. Bitte versuchen Sie es später erneut.', 'Платеж отклонен, повторите попытку позже.', 'Pago rechazado, intente nuevamente más tarde.', 'Ödeme reddedildi, lütfen daha sonra tekrar deneyin.'),
(666, '', '', 'my_balance', 'My Balance', 'رصيدي', 'Mijn balans', 'Mon solde', 'Mein Gleichgewicht', 'Мой баланс', 'Mi balance', 'Benim dengem'),
(667, '', '', 'replenish_my_balance', 'Replenish My Balance', 'تجديد رصيدي', 'Vul mijn saldo aan', 'Reconstituer mon équilibre', 'Mein Guthaben auffüllen', 'Пополнить мой баланс', 'Reponer mi saldo', 'Bakiyemi doldur'),
(668, '', '', 'browse_to_upload', 'Browse To Upload', 'استعرض لتحميل', 'Bladeren om te uploaden', 'Parcourir pour télécharger', 'Zum Hochladen durchsuchen', 'Обзор для загрузки', 'Navegar para cargar', 'Yüklemeye Göz At'),
(669, '', '', 'create_advertising', 'New campaign ', 'حملة جديدة', 'Nieuwe campagne', 'Nouvelle campagne', 'Neue Kampagne', 'Новая кампания', 'Nueva campaña', 'Yeni Kampanya'),
(670, '', '', 'create_new_ad', 'Create new campaign', 'إنشاء حملة جديدة', 'Maak een nieuwe campagne', 'Créer une nouvelle campagne', 'Neue Kampagne erstellen', 'Создать новую кампанию', 'Crear nueva campaña', 'Yeni kampanya oluştur'),
(671, '', '', 'your_current_wallet_balance_is__0__please_top_up_your_wallet_to_continue.', 'Your current wallet balance is 0, please top up your wallet to continue.', 'رصيد محفظتك الحالي هو 0 ، يرجى تعبئة محفظتك للمتابعة.', 'Uw huidige portemonnee-saldo is 0, vul uw portemonnee aan om door te gaan.', 'Votre solde de portefeuille actuel est 0, veuillez recharger votre portefeuille pour continuer.', 'Ihr aktueller Brieftaschen-Kontostand ist 0. Bitte füllen Sie Ihre Brieftasche auf, um fortzufahren.', 'Ваш текущий баланс кошелька равен 0, для продолжения пополните свой кошелек.', 'El saldo actual de su billetera es 0, recargue su billetera para continuar.', 'Geçerli cüzdan bakiyeniz 0, lütfen devam etmek için cüzdanınızı doldurun.'),
(672, '', '', 'top_up', 'Top Up', 'فوق حتى', 'Bijvullen', 'Recharger', 'Aufladen', 'Пополнить', 'Completar', 'Top Up'),
(673, '', '', 'url', 'URL', 'URL', 'URL', 'URL', 'URL', 'URL', 'URL', 'URL'),
(674, '', '', 'select_media', 'Select Media', 'اختر الوسائط', 'Selecteer media', 'Sélectionnez le média', 'Wählen Sie Medien', 'Выберите медиа', 'Select Media', 'Medya Seç'),
(675, '', '', 'target_audience', 'Target Audience', 'الجمهور المستهدف', 'Doelgroep', 'Public cible', 'Zielgruppe', 'Целевая аудитория', 'Público objetivo', 'Hedef kitle'),
(676, '', '', 'placement', 'Placement', 'تحديد مستوى', 'Plaatsing', 'Placement', 'Platzierung', 'размещение', 'Colocación', 'Yerleştirme'),
(677, '', '', 'pricing', 'Pricing', 'التسعير', 'pricing', 'Prix', 'Preisgestaltung', 'ценообразование', 'Precios', 'Fiyatlandırma'),
(678, '', '', 'pay_per_click', 'Pay Per Click', 'تدفع عن كل نقرة', 'Betaal per klik', 'Payer avec un clic', 'Pay Per Click', 'Оплата за клик', 'Pago por clic', 'Tıklama başına ödeme'),
(679, '', '', 'pay_per_impression', 'Pay Per Impression', 'الدفع لكل مرة ظهور', 'Betaal per vertoning', 'Pay Per Impression', 'Pay Per Impression', 'Оплата за показ', 'Pago por impresión', 'Gösterim Başına Ödeme'),
(680, '', '', 'spending_limit_per_day', 'Spending limit per day', 'حد الإنفاق في اليوم', 'Bestedingslimiet per dag', 'Limite de dépenses par jour', 'Ausgabenlimit pro Tag', 'Лимит расходов в день', 'Límite de gasto por día', 'Günlük harcama limiti'),
(681, '', '', 'media_file_is_invalid._please_select_a_valid_image', 'Media file is invalid. Please select a valid image', 'ملف الوسائط غير صالح. يرجى اختيار صورة صالحة', 'Mediabestand is ongeldig. Selecteer een geldige afbeelding', 'Le fichier multimédia n\'est pas valide. Veuillez sélectionner une image valide', 'Mediendatei ist ungültig. Bitte wählen Sie ein gültiges Bild', 'Медиа-файл недействителен. Пожалуйста, выберите действительное изображение', 'El archivo multimedia no es válido. Por favor seleccione una imagen válida', 'Medya dosyası geçersiz. Lütfen geçerli bir resim seçin'),
(682, '', '', 'error_', 'Error!', 'خطأ!', 'Fout!', 'Erreur!', 'Error!', 'Ошибка!', 'Error!', 'Hata!'),
(683, '', '', 'file_is_too_big__max_upload_size_is', 'File is too big, Max upload size is', 'الملف كبير جدًا ، الحد الأقصى لحجم التحميل هو', 'Bestand is te groot, maximale uploadgrootte is', 'Le fichier est trop gros, la taille maximale de téléchargement est', 'Datei ist zu groß, maximale Upload-Größe ist', 'Файл слишком большой, максимальный размер загрузки', 'El archivo es demasiado grande, el tamaño máximo de carga es', 'Dosya çok büyük, Maksimum yükleme boyutu'),
(684, '', '', 'media_file_is_invalid._please_select_a_valid_image___video', 'Media file is invalid. Please select a valid image / video', 'ملف الوسائط غير صالح. يرجى اختيار صورة / فيديو صالح', 'Mediabestand is ongeldig. Selecteer een geldige afbeelding / video', 'Le fichier multimédia n\'est pas valide. Veuillez sélectionner une image / vidéo valide', 'Mediendatei ist ungültig. Bitte wählen Sie ein gültiges Bild / Video aus', 'Медиа-файл недействителен. Пожалуйста, выберите действительное изображение / видео', 'El archivo multimedia no es válido. Selecciona una imagen / video válido', 'Medya dosyası geçersiz. Lütfen geçerli bir resim / video seçin'),
(685, '', '', 'your_ad_has_been_published_successfully', 'Your campaign has been published successfully.', 'تم نشر حملتك بنجاح.', 'Uw campagne is succesvol gepubliceerd.', 'Votre campagne a été publiée avec succès.', 'Ihre Kampagne wurde erfolgreich veröffentlicht.', 'Ваша кампания была успешно опубликована.', 'Su campaña ha sido publicada con éxito.', 'Kampanyanız başarıyla yayınlandı.'),
(686, '', '', 'the_url_is_invalid._please_enter_a_valid_url', 'The URL is invalid. Please enter a valid URL.', 'عنوان URL غير صالح. أدخل رابط صحيح من فضلك.', 'De URL is ongeldig. Voer een geldige URL in.', 'L\'URL n\'est pas valide. Veuillez entrer une URL valide.', 'Die URL ist ungültig. Bitte geben Sie eine gültige URL ein.', 'URL недействителен. Пожалуйста, введите корректный адрес.', 'La URL no es válida. Por favor introduzca un URL válido.', 'URL geçersiz. Lütfen geçerli bir adres girin.'),
(687, '', '', 'ad_title_must_be_between_5_100', 'Campaign title must be between 5/100.', 'يجب أن يتراوح عنوان الحملة بين 5/100.', 'Campagnetitel moet tussen 5/100 liggen.', 'Le titre de la campagne doit être compris entre 5/100.', 'Der Kampagnentitel muss zwischen 5/100 liegen.', 'Название кампании должно быть между 5/100.', 'El título de la campaña debe estar entre 5/100.', 'Kampanya başlığı 5/100 arasında olmalıdır.'),
(688, '', '', 'edit_ad', 'Edit campaign', 'تحرير الحملة', 'Campagne bewerken', 'Modifier la campagne', 'Kampagne bearbeiten', 'Изменить кампанию', 'Editar campaña', 'Kampanya düzenle'),
(689, '', '', 'your_changes_to_the_ad_were_successfully_saved', 'Your changes to the campaign were successfully saved.', 'تم حفظ تغييراتك على الحملة بنجاح.', 'Uw wijzigingen in de campagne zijn succesvol opgeslagen.', 'Vos modifications dans la campagne ont été enregistrées avec succès.', 'Ihre Änderungen an der Kampagne wurden erfolgreich gespeichert.', 'Ваши изменения в кампании были успешно сохранены.', 'Sus cambios en la campaña se guardaron correctamente.', 'Kampanyadaki değişiklikleriniz başarıyla kaydedildi.'),
(690, '', '', 'name_must_be_between_5_32', 'Name must be between 5/32', 'يجب أن يكون الاسم بين 5/32', 'Naam moet tussen 5/32 zijn', 'Le nom doit être compris entre 5/32', 'Der Name muss zwischen 5/32 liegen', 'Имя должно быть между 5/32', 'El nombre debe estar entre 5/32', 'İsim 5/32 arasında olmalıdır'),
(691, '', '', 'clicks', 'Clicks', 'نقرات', 'klikken', 'Clics', 'Klicks', 'щелчки', 'Clics', 'Tıklanma'),
(692, '', '', 'ads_analytics', 'Campaign analytics', 'تحليلات الحملة', 'Campagne-analyse', 'Analyse de campagne', 'Kampagnenanalyse', 'Аналитика кампании', 'Análisis de campaña', 'Kampanya analizi'),
(693, '', '', 'this_year', 'This year', 'هذه السنة', 'Dit jaar', 'Cette année', 'Dieses Jahr', 'Этот год', 'Este año', 'Bu yıl'),
(694, '', '', 'view_report', 'View report', 'عرض التقرير', 'Bekijk rapport', 'Voir le rapport', 'Zeige Bericht', 'Посмотреть отчет', 'Vista del informe', 'Raporu görüntüle'),
(695, '', '', 'ad_analytics', 'Campaign analytics', 'تحليلات الحملة', 'Campagne-analyse', 'Analyse de campagne', 'Kampagnenanalyse', 'Аналитика кампании', 'Análisis de campaña', 'Kampanya analizi'),
(696, '', '', 'sponsor_ads', 'SPONSOR', 'كفيل', 'SPONSOR', 'PARRAINER', 'SPONSOR', 'СПОНСОР', 'PATROCINADOR', 'SPONSOR'),
(697, '', '', 'by', 'By', 'بواسطة', 'Door', 'Par', 'Durch', 'По', 'Por', 'Tarafından'),
(698, '', '', 'import', 'Import', 'استيراد', 'Importeren', 'Importation', 'Einführen', 'импорт', 'Importar', 'İthalat'),
(699, '', '', 'import_from_soundcloud.', 'Import From SoundCloud.', 'استيراد من SoundCloud.', 'Importeren vanuit SoundCloud.', 'Importer depuis SoundCloud.', 'Aus SoundCloud importieren.', 'Импорт из SoundCloud.', 'Importar desde SoundCloud.', 'SoundCloud\'dan İçe Aktar.'),
(700, '', '', 'enter_the_soundcloud_track_link_and_click_the_button_below.', 'Paste your SoundCloud URL above.', 'الصق عنوان URL الخاص بـ SoundCloud أعلاه.', 'Plak hierboven uw SoundCloud-URL.', 'Collez votre URL SoundCloud ci-dessus.', 'Fügen Sie oben Ihre SoundCloud-URL ein.', 'Вставьте ваш URL SoundCloud выше.', 'Pegue su URL de SoundCloud arriba.', 'SoundCloud URL\'nizi yukarıya yapıştırın.'),
(701, '', '', 'error_found_while_importing_your_track__please_try_again_later.', 'Error found while importing your track, please try again later.', 'تم العثور على خطأ أثناء استيراد المسار ، يرجى المحاولة مرة أخرى لاحقًا.', 'Er is een fout gevonden tijdens het importeren van uw track. Probeer het later opnieuw.', 'Erreur détectée lors de l\'importation de votre piste, veuillez réessayer ultérieurement.', 'Beim Importieren Ihres Tracks ist ein Fehler aufgetreten. Bitte versuchen Sie es später erneut.', 'При импорте трека обнаружена ошибка. Повторите попытку позже.', 'Se encontró un error al importar su pista. Vuelva a intentarlo más tarde.', 'Parça içe aktarılırken hata bulundu, lütfen daha sonra tekrar deneyin.'),
(702, '', '', 'please_enter_valid_soundcloud_track_url.', 'Please enter a valid SoundCloud track URL.', 'يرجى إدخال عنوان URL صالح لبرنامج SoundCloud.', 'Voer een geldige SoundCloud-track-URL in.', 'Veuillez entrer une URL de piste SoundCloud valide.', 'Bitte geben Sie eine gültige SoundCloud-Track-URL ein.', 'Пожалуйста, введите действительный URL трека SoundCloud.', 'Ingrese una URL de pista válida de SoundCloud.', 'Lütfen geçerli bir SoundCloud parça URL\'si girin.'),
(703, '', '', 'please_enter_soundcloud_track_link_to_import.', 'Please enter SoundCloud track link to import.', 'الرجاء إدخال رابط مسار SoundCloud للاستيراد.', 'Voer de SoundCloud-tracklink in om te importeren.', 'Veuillez entrer le lien de la piste SoundCloud à importer.', 'Bitte geben Sie den zu importierenden SoundCloud-Tracklink ein.', 'Пожалуйста, введите ссылку на трек SoundCloud для импорта.', 'Ingrese el enlace de la pista de SoundCloud para importar.', 'Lütfen içeri aktarmak için SoundCloud track linkini girin.'),
(704, '', '', 'error_found_while_importing_your_track__please_check_soundcloud_client_id.', 'Error found while importing your track, please check SoundCloud client ID.', 'تم العثور على خطأ أثناء استيراد المسار الخاص بك ، يرجى التحقق من معرف عميل SoundCloud.', 'Fout gevonden bij het importeren van uw track, controleer SoundCloud-client-ID.', 'Erreur détectée lors de l\'importation de votre piste, veuillez vérifier l\'ID client SoundCloud.', 'Beim Importieren Ihres Tracks ist ein Fehler aufgetreten. Überprüfen Sie die SoundCloud-Client-ID.', 'При импорте трека обнаружена ошибка, проверьте идентификатор клиента SoundCloud.', 'Se encontró un error al importar su pista, verifique la ID del cliente de SoundCloud.', 'İzinizi içe aktarırken hata bulundu, lütfen SoundCloud müşteri kimliğini kontrol edin.'),
(705, '', '', 'dmca', 'DMCA', 'DMCA', 'DMCA', 'DMCA', 'DMCA', 'DMCA', 'DMCA', 'DMCA'),
(706, '', '', 'login_with_soundcloud', 'Login with SoundCloud', 'تسجيل الدخول مع SoundCloud', 'Inloggen met SoundCloud', 'Connexion avec SoundCloud', 'Loggen Sie sich mit SoundCloud ein', 'Войти через SoundCloud', 'Inicie sesión con SoundCloud', 'SoundCloud ile giriş yap'),
(707, '', '', 'dcma', 'DCMA', 'DCMA', 'DCMA', 'DCMA', 'DCMA', 'DCMA', 'DCMA', 'DCMA'),
(708, '', '', 'move_to_album', 'Move to an album', 'الانتقال إلى ألبوم', 'Ga naar een album', 'Déplacer vers un album', 'Zu einem Album wechseln', 'Переместить в альбом', 'Moverse a un álbum', 'Bir albüme taşı'),
(709, '', '', 'select_albums', 'Select albums', 'اختر ألبومات', 'Selecteer albums', 'Sélectionner des albums', 'Alben auswählen', 'Выберите альбомы', 'Seleccionar álbumes', 'Albüm seç'),
(710, '', '', 'please_select_which_album_you_want_to_add_this_song_to.', 'Please select which album you want to add this song to.', 'الرجاء تحديد الألبوم الذي تريد إضافة هذه الأغنية إليه.', 'Selecteer aan welk album u dit nummer wilt toevoegen.', 'Veuillez sélectionner l\'album auquel vous souhaitez ajouter cette chanson.', 'Bitte wählen Sie das Album aus, zu dem Sie diesen Titel hinzufügen möchten.', 'Пожалуйста, выберите, в какой альбом вы хотите добавить эту песню.', 'Seleccione a qué álbum desea agregar esta canción.', 'Lütfen bu şarkıyı eklemek istediğiniz albümü seçin.'),
(711, '', '', 'review_track', 'Review Track', 'مراجعة المسار', 'Beoordeling bijhouden', 'Piste de révision', 'Track überprüfen', 'Обзор трека', 'Pista de revisión', 'Parçayı İncele'),
(712, '', '', 'review', 'Review', 'إعادة النظر', 'Recensie', 'La revue', 'Rezension', 'Обзор', 'revisión', 'gözden geçirmek'),
(713, '', '', 'review_track.', 'Review track.', 'مراجعة المسار.', 'Review track.', 'Piste de révision.', 'Track überprüfen.', 'Обзор трека.', 'Revisar pista.', 'Parçayı gözden geçir.'),
(714, '', '', 'please_enter_your_review.', 'Please enter your review.', 'الرجاء إدخال رأيك.', 'Voer uw beoordeling in.', 'S\'il vous plaît entrer votre avis.', 'Bitte geben Sie Ihre Bewertung ein.', 'Пожалуйста, введите ваш отзыв.', 'Por favor, introduzca su opinión.', 'Lütfen yorumunuzu giriniz.'),
(715, '', '', 'thanks_for_your_submission.', 'Thanks for your submission.', 'شكرا لتقديمك.', 'Bedankt voor uw inzending.', 'Merci pour votre soumission.', 'Vielen Dank für Ihren Beitrag.', 'Спасибо за заявку.', 'Gracias por tu presentación.', 'Gönderdiğiniz için teşekkürler.'),
(716, '', '', 'reviews', 'Reviews', 'التعليقات', 'beoordelingen', 'Avis', 'Bewertungen', 'Отзывы', 'Comentarios', 'yorumlar'),
(717, '', '', 'no_reviews_on_this_track_yet.', 'No reviews on this track yet.', 'ليست هناك أي تعليقات على هذا المسار بعد.', 'Nog geen beoordelingen op dit nummer.', 'Aucun commentaire sur cette piste pour le moment.', 'Noch keine Reviews auf diesem Track.', 'На этот трек пока нет отзывов.', 'Aún no hay reseñas sobre esta pista.', 'Bu parça hakkında henüz yorum yapılmamış.'),
(718, '', '', 'upload_new_track.', 'uploaded a new track.', 'تحميل مسار جديد.', 'upload nieuw nummer.', 'télécharger une nouvelle piste.', 'lade einen neuen Track hoch.', 'загрузить новый трек.', 'subir nueva pista', 'yeni parça yükle.'),
(719, '', '', 'notification', 'Notification', 'إعلام', 'Kennisgeving', 'Notification', 'Benachrichtigung', 'уведомление', 'Notificación', 'Bildirim'),
(720, '', '', 'notification_settings', 'Notification Settings', 'إعدادات الإشعار', 'Notificatie instellingen', 'Notification Settings', 'Benachrichtigungseinstellungen', 'Настройки уведомлений', 'Configuración de las notificaciones', 'Bildirim ayarları'),
(721, '', '', 'someone_followed_me', 'Someone followed me', 'شخص ما تبعني', 'Iemand volgde mij', 'Quelqu\'un m\'a suivi', 'Jemand folgte mir', 'Кто-то последовал за мной', 'Alguien me siguió', 'Biri beni takip etti'),
(722, '', '', 'someone_liked_one_of_my_tracks', 'Someone liked one of my tracks', 'شخص ما أحب أحد مساراتي', 'Iemand vond een van mijn nummers leuk', 'Quelqu\'un a aimé un de mes morceaux', 'Jemand mochte einen meiner Titel', 'Кому-то понравился один из моих треков', 'A alguien le gustó una de mis canciones.', 'Birisi izlerimden birini beğendi'),
(723, '', '', 'someone_liked_one_of_my_comments', 'Someone liked one of my comments', 'شخص ما أحب أحد تعليقاتي', 'Iemand vond een van mijn opmerkingen leuk', 'Quelqu\'un a aimé un de mes commentaires', 'Jemand mochte einen meiner Kommentare', 'Кому-то понравился один из моих комментариев', 'A alguien le gustó uno de mis comentarios.', 'Birisi benim yorumlarımdan birini beğendi'),
(724, '', '', 'approve_disapprove_artist_request', 'Approve/Disapprove artist request(s)', 'قبول / رفض طلب (طلبات) الفنان', 'Verzoek / aanvragen kunstenaar goedkeuren / afkeuren', 'Approuver / désapprouver les demandes d\'artistes', 'Künstleranfrage (n) genehmigen / ablehnen', 'Утвердить / Отклонить запрос художника', 'Aprobar / Rechazar solicitud (es) de artista', 'Sanatçı isteklerini onayla / reddet'),
(725, '', '', 'approve_disapprove_bank_payment_request', 'Approve/Disapprove bank payment request(s)', 'الموافقة / رفض طلب (طلبات) الدفع المصرفي', 'Verzoek / bankverzoek (en) goedkeuren / afkeuren', 'Approuver / désapprouver les demandes de paiement bancaire', 'Bankzahlungsanforderung (en) genehmigen / ablehnen', 'Утвердить / отклонить запрос (ы) банковского платежа', 'Aprobar / rechazar solicitudes de pago bancario', 'Banka ödeme taleplerini onayla / reddet'),
(726, '', '', 'one_of_my_following_upload_new_track', 'One of my following artists uploaded a new track', 'قام أحد الفنانين التابعين لي بتحميل مسار جديد', 'Een van mijn volgende artiesten heeft een nieuw nummer geüpload', 'Un de mes artistes suivants a ajouté une nouvelle piste', 'Einer meiner folgenden Künstler hat einen neuen Track hochgeladen', 'Один из моих следующих исполнителей загрузил новый трек', 'Uno de mis siguientes artistas subió una nueva canción.', 'Takip eden sanatçılarımdan biri yeni bir parça yükledi'),
(727, '', '', 'one_of_my_following_users_upload_new_track', 'One of my following artists uploaded a new track', 'قام أحد الفنانين التابعين لي بتحميل مسار جديد', 'Een van mijn volgende artiesten heeft een nieuw nummer geüpload', 'Un de mes artistes suivants a ajouté une nouvelle piste', 'Einer meiner folgenden Künstler hat einen neuen Track hochgeladen', 'Один из моих следующих исполнителей загрузил новый трек', 'Uno de mis siguientes artistas subió una nueva canción.', 'Takip eden sanatçılarımdan biri yeni bir parça yükledi'),
(728, '', '', 'notify_me_when', 'Notify me when', 'قم بتنبيهي عندما', 'Laat me weten wanneer', 'M\'avertir quand', 'Benachrichtigen Sie mich wenn', 'Сообщите мне, когда', 'Notifícame cuando', 'Bana ne zaman bildir'),
(729, '', '', 'new_notification', 'New notification', 'إشعار جديد', 'Nieuwe melding', 'New notification', 'Neue Benachrichtigung', 'Новое уведомление', 'Nueva notificación', 'Yeni bildirim'),
(730, '', '', 'you_can_not_import_this_track_because_this_track_is_imported_before.', 'This track is already imported, please choose another track.', 'تم استيراد هذا المسار بالفعل ، يرجى اختيار مسار آخر.', 'Deze track is al geïmporteerd, kies een andere track.', 'Cette piste est déjà importée, veuillez choisir une autre piste.', 'Dieser Track ist bereits importiert, bitte wählen Sie einen anderen Track.', 'Этот трек уже импортирован, выберите другой трек.', 'Esta pista ya está importada, elija otra pista.', 'Bu parça zaten içe aktarıldı, lütfen başka bir parça seçin.'),
(731, '', '', 'manage_sessions', 'Manage Sessions', 'إدارة الجلسات', 'Sessies beheren', 'Gérer les sessions', 'Sitzungen verwalten', 'Управление сессиями', 'Administrar sesiones', 'Oturumları Yönet'),
(732, '', '', 'ip_address', 'IP Address', 'عنوان IP', 'IP adres', 'Adresse IP', 'IP Adresse', 'Айпи адрес', 'Dirección IP', 'IP adresi'),
(733, '', '', 'platform', 'Platform', 'برنامج', 'Platform', 'Plate-forme', 'Plattform', 'Платформа', 'Plataforma', 'platform'),
(734, '', '', 'browser', 'Browser', 'المتصفح', 'browser', 'Navigateur', 'Browser', 'браузер', 'Navegador', 'Tarayıcı'),
(735, '', '', 'last_seen', 'Last Seen', 'اخر ظهور', 'Laatst gezien', 'Dernière vue', 'Zuletzt gesehen', 'Последнее посещение', 'Ultima vez visto', 'Son görülen'),
(736, '', '', 'actions', 'Actions', 'أفعال', 'acties', 'Actions', 'Aktionen', 'действия', 'Comportamiento', 'Eylemler'),
(737, '', '', 'session_expired', 'Session Expired', 'انتهت الجلسة', 'Sessie verlopen', 'La session a expiré', 'Sitzung abgelaufen', 'Сессия истекла', 'Sesión expirada', 'Oturum süresi doldu'),
(738, '', '', 'your_session_has_been_expired__please_login_again.', 'Your Session has been expired, please login again.', 'انتهت جلستك ، يرجى تسجيل الدخول مرة أخرى.', 'Uw sessie is verlopen, log opnieuw in.', 'Votre session a expiré, veuillez vous reconnecter.', 'Ihre Sitzung ist abgelaufen, bitte melden Sie sich erneut an.', 'Ваша сессия истекла, пожалуйста, войдите снова.', 'Su sesión ha caducado, vuelva a iniciar sesión.', 'Oturumunuzun süresi doldu, lütfen tekrar giriş yapın.'),
(739, '', '', 'two-factor_authentication', 'Two-factor authentication', 'توثيق ذو عاملين', 'Twee-factor authenticatie', 'Authentification à deux facteurs', 'Zwei-Faktor-Authentifizierung', 'Двухфакторная аутентификация', 'Autenticación de dos factores', 'İki faktörlü kimlik doğrulama'),
(740, '', '', 'phone', 'Phone', 'هاتف', 'Telefoon', 'Téléphone', 'Telefon', 'Телефон', 'Teléfono', 'Telefon'),
(741, '', '', 'enable', 'Enable', 'مكن', 'in staat stellen', 'Activer', 'Aktivieren', 'включить', 'Habilitar', 'etkinleştirme'),
(742, '', '', 'disable', 'Disable', 'تعطيل', 'uitschakelen', 'Désactiver', 'Deaktivieren', 'Отключить', 'Inhabilitar', 'Devre Dışı'),
(743, '', '', 'turn_on_2-step_login_to_level-up_your_account_s_security__once_turned_on__you_ll_use_both_your_password_and_a_6-digit_security_code_sent_to_your_phone_or_email_to_log_in.', 'Turn on 2-step login to level-up your account\'s security, Once turned on, you\'ll use both your password and a 6-digit security code sent to your phone or email to log in.', 'قم بتشغيل تسجيل الدخول من خطوتين لتحسين مستوى حسابك', 'Schakel inloggen in twee stappen in om uw account een hoger niveau te geven', 'Activez la connexion en deux étapes pour mettre votre compte à niveau.', 'Aktivieren Sie die zweistufige Anmeldung, um Ihr Konto aufzustocken', 'Включите двухэтапный вход, чтобы повысить уровень своей учетной записи', 'Active el inicio de sesión en 2 pasos para subir de nivel su cuenta', 'Hesabınızı seviyelendirmek için 2 adımlı girişi açın'),
(744, '', '', 'a_confirmation_email_has_been_sent.', 'A confirmation email has been sent.', 'تم إرسال رسالة تأكيد بالبريد الإلكتروني.', 'Er is een bevestigingsmail verzonden.', 'Un email de confirmation a été envoyé.', 'Eine Bestätigungs-E-Mail wurde gesendet.', 'Письмо с подтверждением было отправлено.', 'Un correo electrónico de confirmación ha sido enviado.', 'Bir onay e-postası gönderildi.'),
(745, '', '', 'we_have_sent_an_email_that_contains_the_confirmation_code_to_enable_two-factor_authentication.', 'We have sent an email that contains the confirmation code to enable Two-factor authentication.', 'لقد أرسلنا رسالة بريد إلكتروني تحتوي على رمز التأكيد لتمكين المصادقة الثنائية.', 'We hebben een e-mail verzonden met de bevestigingscode om tweefactorauthenticatie in te schakelen.', 'Nous avons envoyé un courrier électronique contenant le code de confirmation pour activer l\'authentification à deux facteurs.', 'Wir haben eine E-Mail gesendet, die den Bestätigungscode enthält, um die Zwei-Faktor-Authentifizierung zu aktivieren.', 'Мы отправили электронное письмо с кодом подтверждения для включения двухфакторной аутентификации.', 'Hemos enviado un correo electrónico que contiene el código de confirmación para habilitar la autenticación de dos factores.', 'İki faktörlü kimlik doğrulamayı etkinleştirmek için onay kodunu içeren bir e-posta gönderdik.'),
(746, '', '', 'confirmation_code', 'Confirmation code', 'رمز التأكيد', 'Bevestigingscode', 'Confirmation code', 'Bestätigungscode', 'Код подтверждения', 'Código de confirmación', 'Onay kodu'),
(747, '', '', 'a_confirmation_message_and_email_were_sent.', 'A confirmation message and email were sent.', 'تم إرسال رسالة تأكيد والبريد الإلكتروني.', 'Een bevestigingsbericht en e-mail zijn verzonden.', 'Un message de confirmation et un email ont été envoyés.', 'Eine Bestätigungsnachricht und eine E-Mail wurden gesendet.', 'Подтверждение и электронное письмо были отправлены.', 'Se envió un mensaje de confirmación y un correo electrónico.', 'Bir onay mesajı ve e-posta gönderildi.'),
(748, '', '', 'we_have_sent_a_message_and_an_email_that_contain_the_confirmation_code_to_enable_two-factor_authentication', 'We have sent a message and an email that contain the confirmation code to enable two-factor authentication', 'لقد أرسلنا رسالة ورسالة بريد إلكتروني تحتوي على رمز التأكيد لتمكين المصادقة الثنائية', 'We hebben een bericht en een e-mail verzonden met de bevestigingscode om tweefactorauthenticatie mogelijk te maken', 'Nous avons envoyé un message et un courrier électronique contenant le code de confirmation pour activer l\'authentification à deux facteurs.', 'Wir haben eine Nachricht und eine E-Mail gesendet, die den Bestätigungscode enthält, um die Zwei-Faktor-Authentifizierung zu aktivieren', 'Мы отправили сообщение и электронное письмо с кодом подтверждения для включения двухфакторной аутентификации', 'Hemos enviado un mensaje y un correo electrónico que contienen el código de confirmación para permitir la autenticación de dos factores.', 'İki faktörlü kimlik doğrulamayı etkinleştirmek için onay kodunu içeren bir mesaj ve e-posta gönderdik'),
(749, '', '', 'a_confirmation_message_was_sent.', 'A confirmation message was sent.', 'تم إرسال رسالة تأكيد.', 'Er is een bevestigingsbericht verzonden.', 'Un message de confirmation a été envoyé.', 'Eine Bestätigungsnachricht wurde gesendet.', 'Подтверждение было отправлено.', 'Se envió un mensaje de confirmación.', 'Bir onay mesajı gönderildi.'),
(750, '', '', 'we_have_sent_a_message_that_contains_the_confirmation_code_to_enable_two-factor_authentication.', 'We have sent a message that contains the confirmation code to enable Two-factor authentication.', 'لقد أرسلنا رسالة تحتوي على رمز التأكيد لتمكين المصادقة الثنائية.', 'We hebben een bericht verzonden met de bevestigingscode om tweefactorauthenticatie in te schakelen.', 'Nous avons envoyé un message contenant le code de confirmation pour activer l\'authentification à deux facteurs.', 'Wir haben eine Nachricht gesendet, die den Bestätigungscode enthält, um die Zwei-Faktor-Authentifizierung zu aktivieren.', 'Мы отправили сообщение с кодом подтверждения для включения двухфакторной аутентификации.', 'Hemos enviado un mensaje que contiene el código de confirmación para habilitar la autenticación de dos factores.', 'İki faktörlü kimlik doğrulamayı etkinleştirmek için onay kodunu içeren bir mesaj gönderdik.'),
(751, '', '', 'we_have_sent_you_an_email_with_the_confirmation_code.', 'We have sent you an email with the confirmation code.', 'لقد أرسلنا لك رسالة بريد إلكتروني مع رمز التأكيد.', 'We hebben je een e-mail gestuurd met de bevestigingscode.', 'Nous vous avons envoyé un email avec le code de confirmation.', 'Wir haben Ihnen eine E-Mail mit dem Bestätigungscode gesendet.', 'Мы отправили вам письмо с кодом подтверждения.', 'Le hemos enviado un correo electrónico con el código de confirmación.', 'Onay kodunu içeren bir e-posta gönderdik.'),
(752, '', '', 'wrong_confirmation_code.', 'Wrong confirmation code.', 'رمز التأكيد الخاطئ.', 'Verkeerde bevestigingscode.', 'Mauvais code de confirmation.', 'Falscher Bestätigungscode.', 'Неверный код подтверждения.', 'Código de confirmación incorrecto.', 'Yanlış onay kodu.'),
(753, '', '', 'your_e-mail_has_been_successfully_verified.', 'Your E-mail has been successfully verified.', 'تم التحقق من بريدك الإلكتروني بنجاح.', 'Uw e-mail is succesvol geverifieerd.', 'Votre courriel a été vérifié avec succès.', 'Ihre E-Mail wurde erfolgreich verifiziert.', 'Ваш E-mail был успешно подтвержден.', 'Su correo electrónico ha sido verificado con éxito.', 'E-posta adresiniz başarıyla doğrulandı.'),
(754, '', '', 'unusual_login', 'Unusual login', 'تسجيل دخول غير عادي', 'Ongebruikelijke login', 'Login inhabituel', 'Ungewöhnliches Login', 'Необычный логин', 'Inicio de sesión inusual', 'Olağandışı giriş'),
(755, '', '', 'we_have_sent_you_the_confirmation_code_to_your_email_address.', 'We have sent you the confirmation code to your email address.', 'لقد أرسلنا لك رمز التأكيد إلى عنوان بريدك الإلكتروني.', 'We hebben u de bevestigingscode naar uw e-mailadres gestuurd.', 'Nous vous avons envoyé le code de confirmation à votre adresse e-mail.', 'Wir haben Ihnen den Bestätigungscode an Ihre E-Mail-Adresse gesendet.', 'Мы отправили вам код подтверждения на ваш адрес электронной почты.', 'Le hemos enviado el código de confirmación a su dirección de correo electrónico.', 'Onay kodunu e-posta adresinize gönderdik.'),
(756, '', '', 'to_log_in__you_need_to_verify_your_identity.', 'To log in, you need to verify your identity.', 'لتسجيل الدخول ، تحتاج إلى التحقق من هويتك.', 'Om in te loggen, moet u uw identiteit verifiëren.', 'Pour vous connecter, vous devez vérifier votre identité.', 'Um sich anzumelden, müssen Sie Ihre Identität überprüfen.', 'Для входа необходимо подтвердить свою личность.', 'Para iniciar sesión, debe verificar su identidad.', 'Giriş yapmak için kimliğinizi doğrulamanız gerekir.'),
(757, '', '', 'welcome...', 'Welcome Back!', 'مرحبا بعودتك!', 'Welkom terug!', 'Nous saluons le retour!', 'Willkommen zurück!', 'Добро пожаловать назад!', '¡Dar una buena acogida!', 'Tekrar hoşgeldiniz!'),
(758, '', '', 'stations', 'Stations', 'محطات', 'stations', 'Stations', 'Stationen', 'станций', 'Estaciones', 'İstasyonlar'),
(759, '', '', 'no_stations_found', 'No stations found', 'لا توجد محطات', 'Geen zenders gevonden', 'Aucune station trouvée', 'Keine Sender gefunden', 'Станции не найдены', 'No se encontraron estaciones', 'İstasyon bulunamadı'),
(760, '', '', 'add_station', 'Add Station', 'إضافة محطة', 'Station toevoegen', 'Ajouter une station', 'Station hinzufügen', 'Добавить станцию', 'Agregar estación', 'İstasyon ekle'),
(761, '', '', 'search_for_stations', 'Search for stations', 'البحث عن المحطات', 'Zoeken naar zenders', 'Rechercher des stations', 'Nach Sendern suchen', 'Поиск станций', 'Busca estaciones', 'İstasyonları ara'),
(762, '', '', 'station_search.', 'Station Search.', 'محطة البحث.', 'Zender zoeken.', 'Recherche de station.', 'Sendersuche.', 'Поиск станции.', 'Búsqueda de estación.', 'İstasyon Arama.'),
(763, '', '', 'please_enter_more_than_3_characters.', 'Please enter more than 3 characters.', 'الرجاء إدخال أكثر من 3 أحرف.', 'Voer meer dan 3 tekens in.', 'Veuillez saisir plus de 3 caractères.', 'Bitte geben Sie mehr als 3 Zeichen ein.', 'Пожалуйста, введите более 3 символов.', 'Por favor, introduzca más de 3 caracteres.', 'Lütfen 3 karakterden fazla girin.'),
(764, '', '', 'error_found_while_search_stations__please_try_again_later.', 'Error found while search stations, please try again later.', 'تم العثور على خطأ أثناء محطات البحث ، يرجى المحاولة مرة أخرى لاحقًا.', 'Fout gevonden tijdens het zoeken van zenders, probeer het later opnieuw.', 'Erreur trouvée lors de la recherche de stations, veuillez réessayer ultérieurement.', 'Bei der Suche nach Sendern wurde ein Fehler gefunden. Bitte versuchen Sie es später erneut.', 'При поиске станций обнаружена ошибка, повторите попытку позже.', 'Se encontró un error al buscar estaciones, por favor intente más tarde.', 'Arama istasyonlarında hata bulundu, lütfen daha sonra tekrar deneyin.'),
(765, '', '', 'you_already_add_this_station.', 'You already add this station.', 'قمت بالفعل بإضافة هذه المحطة.', 'Je hebt dit station al toegevoegd.', 'Vous ajoutez déjà cette station.', 'Sie haben diesen Sender bereits hinzugefügt.', 'Вы уже добавили эту станцию.', 'Ya agregaste esta estación.', 'Bu istasyonu zaten eklediniz.'),
(766, '', '', 'delete_station', 'Delete Station', 'حذف المحطة', 'Station verwijderen', 'Delete Station', 'Station löschen', 'Удалить станцию', 'Eliminar estación', 'İstasyonu Sil'),
(767, '', '', 'no_more_stations_found', 'No more stations found', 'لم يتم العثور على المزيد من المحطات', 'Geen stations meer gevonden', 'Pas plus de stations trouvées', 'Keine weiteren Stationen gefunden', 'Больше станций не найдено', 'No se encontraron más estaciones', 'Başka istasyon bulunamadı'),
(768, '', '', 'the_track_has_been_moved_to_this_album_successfully.', 'The track has been moved to following album.', 'تم نقل المسار إلى الألبوم التالي.', 'Het nummer is verplaatst naar het volgende album.', 'La piste a été déplacée dans l\'album suivant.', 'Der Titel wurde in das folgende Album verschoben.', 'Трек был перемещен в следующий альбом.', 'La pista se ha movido al siguiente álbum.', 'Parça aşağıdaki albüme taşındı.'),
(769, '', '', 'someone_liked_disliked_one_of_my_tracks', 'Someone liked/disliked one of my tracks', 'شخص ما أحب / لم يعجبني أحد مساراتي', 'Iemand vond een van mijn tracks leuk / niet leuk', 'Quelqu\'un a aimé / n\'aime pas l\'un de mes morceaux', 'Jemand mochte / mochte einen meiner Titel nicht', 'Кому-то понравился / не понравился один из моих треков', 'A alguien le gustó / no le gustó una de mis canciones', 'Birisi izlerimden birini beğendi / beğenmedi'),
(770, '', '', 'disliked_your_song.', 'disliked your song.', 'لم يعجبك أغنيتك.', 'vond je lied niet leuk.', 'n\'a pas aimé votre chanson.', 'mochte dein Lied nicht.', 'не понравилась твоя песня', 'No me gustó tu canción.', 'şarkından hoşlanmadı.'),
(771, '', '', 'reviewed_your_song.', 'reviewed your song.', 'استعرض أغنيتك.', 'heeft je lied beoordeeld.', 'examiné votre chanson.', 'hat dein Lied rezensiert.', 'пересмотрел вашу песню.', 'revisó tu canción', 'şarkını inceledi.'),
(772, '', '', 'someone_reviewed_one_of_my_tracks', 'Someone reviewed one of my tracks', 'شخص ما استعرض أحد مساراتي', 'Iemand heeft een van mijn tracks beoordeeld', 'Quelqu\'un a passé en revue une de mes pistes', 'Jemand hat einen meiner Titel überprüft', 'Кто-то просмотрел один из моих треков', 'Alguien revisó una de mis canciones.', 'Birisi izlerimden birini inceledi'),
(773, '', '', 'you_can_not_import_this_track_because_this_track_is_one_of_soundcloud_go__tracks.', 'You can not import this track because this track is one of SoundCloud Go+ tracks.', 'لا يمكنك استيراد هذا المسار لأن هذا المسار هو أحد مسارات SoundCloud Go.', 'Je kunt deze track niet importeren omdat deze een van SoundCloud Go-tracks is.', 'Vous ne pouvez pas importer cette piste car cette piste est l\'une des pistes SoundCloud Go.', 'Sie können diese Spur nicht importieren, da es sich bei dieser Spur um eine SoundCloud Go-Spur handelt.', 'Вы не можете импортировать этот трек, потому что этот трек является одним из треков SoundCloud Go.', 'No puede importar esta pista porque esta es una de las pistas de SoundCloud Go.', 'Bu parçayı içe aktaramazsınız çünkü bu parça SoundCloud Go parçalarından biridir.'),
(774, '', '', 'remove_song', 'Remove Song', 'إزالة الأغنية', 'Nummer verwijderen', 'Supprimer la chanson', 'Song entfernen', 'Удалить песню', 'Eliminar canción', 'Şarkıyı Kaldır'),
(775, '', '', 'invalid_file_format__only_mp3__ogg__opus__oga__wav__and_mpeg_is_allowed', 'Invalid file format, only mp3, ogg, opus, oga, wav, and mpeg is allowed', 'تنسيق ملف غير صالح ، يُسمح فقط بملفات mp3 و ogg و opus و oga و wav و mpeg', 'Ongeldig bestandsformaat, alleen mp3, ogg, opus, oga, wav en mpeg zijn toegestaan', 'Format de fichier non valide, seuls les fichiers mp3, ogg, opus, oga, wav et mpeg sont autorisés', 'Ungültiges Dateiformat, nur MP3, Ogg, Opus, Oga, WAV und MPEG sind zulässig', 'Неверный формат файла, разрешены только mp3, ogg, opus, oga, wav и mpeg', 'Formato de archivo no válido, solo se permiten mp3, ogg, opus, oga, wav y mpeg', 'Geçersiz dosya biçimi, yalnızca mp3, ogg, opus, oga, wav ve mpeg&#39;e izin verilir'),
(776, '', '', 'remove_song', 'Remove Song', 'إزالة الأغنية', 'Nummer verwijderen', 'Supprimer la chanson', 'Song entfernen', 'Удалить песню', 'Eliminar canción', 'Şarkıyı Kaldır'),
(777, '', '', 'invalid_file_format__only_mp3__ogg__opus__oga__wav__and_mpeg_is_allowed', 'Invalid file format, only mp3, ogg, opus, oga, wav, and mpeg is allowed', 'تنسيق ملف غير صالح ، يُسمح فقط بملفات mp3 و ogg و opus و oga و wav و mpeg', 'Ongeldig bestandsformaat, alleen mp3, ogg, opus, oga, wav en mpeg zijn toegestaan', 'Format de fichier non valide, seuls les fichiers mp3, ogg, opus, oga, wav et mpeg sont autorisés', 'Ungültiges Dateiformat, nur MP3, Ogg, Opus, Oga, WAV und MPEG sind zulässig', 'Неверный формат файла, разрешены только mp3, ogg, opus, oga, wav и mpeg', 'Formato de archivo no válido, solo se permiten mp3, ogg, opus, oga, wav y mpeg', 'Geçersiz dosya biçimi, yalnızca mp3, ogg, opus, oga, wav ve mpeg&#39;e izin verilir'),
(778, '', '', 'my_affiliates', 'My Affiliates', 'الشركات التابعة لي', 'Mijn partners', 'Mes affiliés', 'Meine Partner', 'Мои филиалы', 'Mis afiliados', 'Bağlı Kuruluşlarım'),
(779, '', '', 'your_affiliate_link', 'Your affiliate link', 'رابط الانتساب الخاص بك', 'Uw partnerlink', 'Votre lien d&#39;affiliation', 'Ihr Affiliate-Link', 'Ваша партнерская ссылка', 'Su enlace de afiliado', 'Satış ortağı bağlantınız'),
(780, '', '', 'import_from', 'Import From', 'الاستيراد من', 'Importeren van', 'Importer de', 'Importieren von', 'Импортировать из', 'Importar de', 'Den ithal'),
(781, '', '', 'paste_your_url_above.', 'Paste your URL above.', 'قم بلصق عنوان URL الخاص بك أعلاه.', 'Plak hierboven uw URL.', 'Collez votre URL ci-dessus.', 'Fügen Sie oben Ihre URL ein.', 'Вставьте свой URL выше.', 'Pega tu URL arriba.', 'URL&#39;nizi yukarıya yapıştırın.'),
(782, '', '', 'please_enter_a_valid_link_to_import.', 'Please enter a valid link to import.', 'يرجى إدخال رابط صالح للاستيراد.', 'Voer een geldige link in om te importeren.', 'Veuillez saisir un lien valide pour importer.', 'Bitte geben Sie einen gültigen Link zum Importieren ein.', 'Пожалуйста, введите действительную ссылку для импорта.', 'Ingrese un enlace válido para importar.', 'Lütfen içe aktarmak için geçerli bir bağlantı girin.'),
(783, '', '', 'import_sounds', 'Import Music', 'استيراد الموسيقى', 'Muziek importeren', 'Importer de la musique', 'Musik importieren', 'Импорт музыки', 'Importar música', 'Müziği İçe Aktar'),
(784, '', '', 'link_must_be_like_ex__https___music.apple.com_us_album_wolves_1445055015_i_1445055017', 'Link must be like EX: https://music.apple.com/us/album/wolves/1445055015?i=1445055017', 'يجب أن يكون الرابط مثل EX: https://music.apple.com/us/album/wolves/1445055015؟i=1445055017', 'Link moet als EX zijn: https://music.apple.com/us/album/wolves/1445055015?i=1445055017', 'Le lien doit être comme EX: https://music.apple.com/us/album/wolves/1445055015?i=1445055017', 'Der Link muss wie EX sein: https://music.apple.com/us/album/wolves/1445055015?i=1445055017', 'Ссылка должна быть похожа на EX: https://music.apple.com/us/album/wolves/1445055015?i=1445055017', 'El enlace debe ser como EX: https://music.apple.com/us/album/wolves/1445055015?i=1445055017', 'Bağlantı EX gibi olmalıdır: https://music.apple.com/us/album/wolves/1445055015?i=1445055017'),
(785, '', '', 'itunes_partner_token', 'Itunes Partner Token', 'الرمز المميز لشريك Itunes', 'Itunes Partner Token', 'Jeton de partenaire Itunes', 'Itunes Partner Token', 'Itunes Партнерский токен', 'Token de socio de iTunes', 'Itunes İş Ortağı Jetonu'),
(786, '', '', 'listen_in_deezer', 'Listen in Deezer', 'الاستماع في Deezer', 'Listen in Deezer', 'Listen in Deezer', 'Listen in Deezer', 'Слушай в Deezer', 'Escucha en Deezer', 'Deezer&#39;ı dinle');
INSERT INTO `langs` (`id`, `ref`, `options`, `lang_key`, `english`, `arabic`, `dutch`, `french`, `german`, `russian`, `spanish`, `turkish`) VALUES
(787, '', '', 'type', 'Type', 'نوع', 'Type', 'Type', 'Art', 'Тип', 'Tipo', 'tip'),
(788, '', '', 'banners_ads', 'Banners Ads', 'إعلانات البانر', 'Banners advertenties', 'Bannières publicitaires', 'Bannerwerbung', 'Баннеры Реклама', 'Anuncios de Banners', 'Afiş Reklamları'),
(789, '', '', 'audio_ads', 'Audio Ads', 'إعلانات صوتية', 'Audio-advertenties', 'Audio Ads', 'Audio-Anzeigen', 'Аудиообъявления', 'Anuncios de audio', 'Sesli Reklamlar'),
(790, '', '', 'select_audio', 'Select Audio', 'حدد الصوت', 'Selecteer Audio', 'Select Audio', 'Wählen Sie Audio', 'Выберите Аудио', 'Seleccionar audio', 'Ses Seçin'),
(791, '', '', 'error_500_internal_server_error_', 'Error 500 internal server error!', 'خطأ 500 - خطأ داخلي في المخدم!', 'Fout 500 - Interne Server Fout!', 'Erreur 500 - Erreur interne du serveur!', 'Fehler 500 - interner Server-Fehler!', 'Ошибка 500 внутренняя ошибка сервера!', '¡Error 500 - Error Interno del Servidor!', 'Hata 500 - iç sunucu hatası!'),
(792, '', '', 'no_songs_found__add_new_songs.', 'No songs found, add new songs.', 'لم يتم العثور على أغاني ، أضف أغاني جديدة.', 'Geen nummers gevonden, voeg nieuwe nummers toe.', 'Aucune chanson trouvée, ajoutez de nouvelles chansons.', 'Keine Songs gefunden, neue Songs hinzufügen.', 'Песни не найдены, добавьте новые песни.', 'No se encontraron canciones, agregue nuevas canciones.', 'Hiçbir şarkı bulunamadı, yeni şarkılar ekleyin.'),
(793, '', '', 'add_upload_folder', 'Add Upload Folder', 'إضافة مجلد تحميل', 'Add Upload Folder', 'Ajouter un dossier de téléchargement', 'Upload-Ordner hinzufügen', 'Добавить папку загрузки', 'Agregar carpeta de carga', 'Yükleme Klasörü Ekle'),
(794, '', '', 'upload_folder', 'Upload Folder', 'تحميل مجلد', 'Upload Folder', 'Télécharger le dossier', 'Ordner hochladen', 'Загрузить папку', 'Subir carpeta', 'Klasörü Yükle'),
(795, '', '', 'playlist_single', 'Playlist', 'قائمة التشغيل', 'afspeellijst', 'Playlist', 'Wiedergabeliste', 'Playlist', 'Lista de reproducción', 'Oynatma Listesi'),
(796, '', '', 'drop_your_files', 'Drop your files to upload', 'أفلت ملفاتك للتحميل', 'Zet uw bestanden neer om te uploaden', 'Déposez vos fichiers à télécharger', 'Legen Sie Ihre Dateien zum Hochladen ab', 'Оставьте свои файлы для загрузки', 'Suelta tus archivos para subir', 'Yüklemek için dosyalarınızı bırakın'),
(797, '', '', 'invite_new_users', 'Invite new Users.', 'دعوة مستخدمين جدد.', 'Nodig nieuwe gebruikers uit.', 'Invitez de nouveaux utilisateurs.', 'Neue Benutzer einladen.', 'Пригласите новых пользователей.', 'Invita a nuevos usuarios.', 'Yeni Kullanıcılar davet edin.'),
(798, '', '', 'get_credits', 'Get Credits.', 'احصل على قروض.', 'Ontvang credits.', 'Obtenez des crédits.', 'Holen Sie sich Credits.', 'Получить кредиты.', 'Obtener créditos.', 'Kredi Al.'),
(799, '', '', 'to_skip_ad', 'to Skip Ad', 'لتخطي الإعلان', 'om advertentie over te slaan', 'ignorer l&#39;annonce', 'Anzeige überspringen', 'пропустить объявление', 'Saltar anuncio', 'Reklamı Atlamak için'),
(800, '', '', 'campaign_deleted_succ', 'Campaign deleted successfully.', 'تم حذف الحملة بنجاح.', 'Campagne is verwijderd.', 'La campagne a bien été supprimée.', 'Kampagne erfolgreich gelöscht.', 'Кампания успешно удалена.', 'Campaña eliminada con éxito.', 'Kampanya başarıyla silindi.'),
(801, '', '', 'earn_up', 'Earn up to {{PRICE}} for each user your refer to us!', 'اربح ما يصل إلى  {{PRICE}} لكل مستخدم تشير إليه!', 'Verdien tot {{PRICE}} voor elke gebruiker die u naar ons verwijst!', 'Gagnez jusqu&#39;à {{PRICE}} pour chaque utilisateur que vous nous référez!', 'Verdienen Sie bis zu {{PREIS}} für jeden Benutzer, den Sie an uns verweisen!', 'Зарабатывайте до {{PRICE}} за каждого пользователя, которого вы обращаетесь к нам!', '¡Gane hasta {{PRECIO}} por cada usuario que nos recomiende!', 'Bize referans verdiğiniz her kullanıcı için {{PRICE}} kazanın!'),
(802, '', '', 'buy_album', 'Buy Album', 'شراء الألبوم', 'Album kopen', 'Acheter l\'album', 'Album kaufen', 'Купить альбом', 'Comprar álbum', 'Albüm Satın Al'),
(803, '', '', 'buy_album', 'Buy Album', 'شراء الألبوم', 'Album kopen', 'Acheter l\'album', 'Album kaufen', 'Купить альбом', 'Comprar álbum', 'Albüm Satın Al'),
(804, '', '', 'mentioned_you_on_a_comment.', 'mentioned you on a comment.', 'ذكرك في تعليق.', 'noemde je bij een opmerking.', 'vous a mentionné dans un commentaire.', 'erwähnte dich in einem Kommentar.', 'упомянул вас в комментарии.', 'te mencionó en un comentario.', 'bir yorumda sizden bahsetti.'),
(805, '', '', 'one_of_my_follower_mention_me_in_comment', 'One of my followers mentioned me in a comment', 'ذكرني أحد المتابعين لي في تعليق', 'Een van mijn volgers noemde me in een opmerking', 'Un de mes followers m\'a mentionné dans un commentaire', 'Einer meiner Anhänger erwähnte mich in einem Kommentar', 'Один из моих подписчиков упомянул меня в комментарии', 'Uno de mis seguidores me mencionó en un comentario.', 'Takipçilerimden biri bir yorumda benden bahsetti'),
(806, '', '', 'one_of_my_follower_mention_me_in_comment_replay', 'One of my followers mentioned me in a comment\'s reply', 'ذكرني أحد متابعيني في رد أحد التعليقات', 'Een van mijn volgers noemde me in het antwoord van een reactie', 'Un de mes followers m\'a mentionné dans la réponse d\'un commentaire', 'Einer meiner Anhänger erwähnte mich in der Antwort eines Kommentars', 'Один из моих подписчиков упомянул меня в ответе на комментарий', 'Uno de mis seguidores me mencionó en la respuesta de un comentario.', 'Takipçilerimden biri bir yorumun yanıtında benden bahsetti'),
(807, '', '', 'replay_comment', 'Reply to comment', 'الرد للتعليق', 'Reageer op commentaar', 'Répondre au commentaire', 'Auf Kommentar antworten', 'Ответить на комментарий', 'Responder al comentario', 'Yorumu yanıtlayın'),
(808, '', '', 'replayed_on__auser__comment_', 'replied on |auser| comment,', 'تم الرد على | auser | ', 'antwoordde op | auser | ', 'a répondu le | auser | ', 'antwortete auf | auser | ', 'ответил на | auser | ', 'respondió en | auser | ', 'yanıtlandı | auser | '),
(809, '', '', 'mentioned_you_on_a_comment.', 'mentioned you on a comment.', 'ذكرك في تعليق.', 'noemde je bij een opmerking.', 'vous a mentionné dans un commentaire.', 'erwähnte dich in einem Kommentar.', 'упомянул вас в комментарии.', 'te mencionó en un comentario.', 'bir yorumda sizden bahsetti.'),
(810, '', '', 'point_system', 'Points system', 'نظام النقاط', 'Puntensysteem', 'Système de points', 'Punktesystem', 'Система очков', 'Sistema de puntos', 'Puan sistemi'),
(811, '', '', 'points_on_comment.', 'Points on comment.', 'النقاط على التعليق.', 'Punten op commentaar.', 'Points sur le commentaire.', 'Punkte auf Kommentar.', 'Очки по комментарию.', 'Puntos sobre el comentario.', 'Yorumdaki noktalar.'),
(812, '', '', 'points_on_upload_new_song.', 'Points on upload new song.', 'نقاط على تحميل أغنية جديدة.', 'Punten bij het uploaden van een nieuw nummer.', 'Points sur le téléchargement d\'une nouvelle chanson.', 'Punkte beim Hochladen eines neuen Songs.', 'Очки за загрузку новой песни.', 'Puntos por subir nueva canción.', 'Yeni şarkı yükleme puanları.'),
(813, '', '', 'points_on_replay_to_comment.', 'Points on replying to a comment.', 'نقاط على الرد على تعليق.', 'Punten over het beantwoorden van een opmerking.', 'Points sur la réponse à un commentaire.', 'Punkte bei der Beantwortung eines Kommentars.', 'Указывает на ответ на комментарий.', 'Puntos sobre la respuesta a un comentario.', 'Yoruma yanıt verme üzerine puan.'),
(814, '', '', 'points_on_like_some_one_track.', 'Points on liking some one track.', 'نقاط على الإعجاب بمسار واحد.', 'Punten op het leuk vinden van een track.', 'Points sur aimer une piste.', 'Punkte, die einen Track mögen.', 'Указывает на то, что нравится какой-то трек.', 'Señala que le gusta alguna pista.', 'Bir parçayı beğenmeye dikkat edin.'),
(815, '', '', 'points_on_dislike_some_one_track.', 'Points on disliking a track.', 'نقاط على كره المسار.', 'Punten op een afkeer van een track.', 'Points à ne pas aimer une piste.', 'Punkte, wenn man eine Spur nicht mag.', 'Указывает на то, что трек не нравится.', 'Puntos sobre el disgusto de una pista.', 'Bir parçayı beğenmeme noktaları.'),
(816, '', '', 'points_on_like_some_one_comment.', 'Points on liking a comment.', 'يشير إلى الإعجاب بالتعليق.', 'Punten op het leuk vinden van een opmerking.', 'Points sur aimer un commentaire.', 'Punkte zum Liken eines Kommentars.', 'Очки за понравившийся комментарий.', 'Puntos sobre dar me gusta a un comentario.', 'Bir yorumu beğenmeye işaret eder.'),
(817, '', '', 'points_on_create_new_playlist.', 'Points on creating new playlist.', 'نقاط على إنشاء قائمة تشغيل جديدة.', 'Punten voor het maken van een nieuwe afspeellijst.', 'Points sur la création d\'une nouvelle liste de lecture.', 'Punkte beim Erstellen einer neuen Wiedergabeliste.', 'Очки при создании нового списка воспроизведения.', 'Puntos sobre la creación de una nueva lista de reproducción.', 'Yeni oynatma listesi oluşturmaya ilişkin puanlar.'),
(818, '', '', 'points_on_re-post_track.', 'Points on re-posting a track.', 'نقاط على إعادة نشر المسار.', 'Punten bij het opnieuw plaatsen van een track.', 'Points lors de la re-publication d\'une piste.', 'Punkte beim erneuten Posten eines Tracks.', 'Очки за повторную публикацию трека.', 'Puntos por volver a publicar una pista.', 'Bir parçayı yeniden yayınlamaya ilişkin puanlar.'),
(819, '', '', 'points_on_download_track.', 'Points on downloading a track.', 'نقاط على تنزيل المسار.', 'Punten voor het downloaden van een track.', 'Points sur le téléchargement d\'une piste.', 'Punkte beim Herunterladen eines Titels.', 'Очки при загрузке трека.', 'Puntos al descargar una pista.', 'Parça indirme ile ilgili noktalar.'),
(820, '', '', 'points_on_like_blog_comment.', 'Points on liking a blog\'s comment.', 'يشير إلى الإعجاب بتعليق المدونة.', 'Punten over het leuk vinden van de opmerking van een blog.', 'Points sur aimer le commentaire d\'un blog.', 'Punkte, die den Kommentar eines Blogs mögen.', 'Очки за понравившийся комментарий в блоге.', 'Señala que le gusta el comentario de un blog.', 'Bir blogun yorumunun beğenilmesine ilişkin puan.'),
(821, '', '', 'points_on_dislike_comment.', 'Points on disliking a comment.', 'يشير إلى عدم الإعجاب بالتعليق.', 'Wijst op een afkeer van een opmerking.', 'Points sur le fait de ne pas aimer un commentaire.', 'Punkte, wenn Sie einen Kommentar nicht mögen.', 'Очки за неприятие комментария.', 'Señala que no le gusta un comentario.', 'Bir yorumu beğenmeme üzerine puan.'),
(822, '', '', 'points_on_dislike_blog_comment.', 'Points on disliking a blog\'s comment.', 'يشير إلى عدم الإعجاب بتعليق المدونة.', 'Punten op het afkeuren van de opmerking van een blog.', 'Points sur le fait de ne pas aimer le commentaire d\'un blog.', 'Punkte, wenn Sie den Kommentar eines Blogs nicht mögen.', 'Очки за неприятие комментария в блоге.', 'Señala que no le gusta el comentario de un blog.', 'Bir blogun yorumunu beğenmeme üzerine işaret eder.'),
(823, '', '', 'points_on_import_track.', 'Points on import track.', 'النقاط على مسار الاستيراد.', 'Punten op importspoor.', 'Points sur la piste d\'importation.', 'Punkte auf der Importspur.', 'Очки на импортном треке.', 'Puntos en pista de importación.', 'İçe aktarma yolundaki noktalar.'),
(824, '', '', 'points_on_user_update_his_profile_picture.', 'Points on user update his profile picture.', 'نقاط على المستخدم تحديث صورته الشخصية.', 'Punten waarop de gebruiker zijn profielfoto bijwerkt.', 'Les points sur l\'utilisateur mettent à jour sa photo de profil.', 'Punkte auf Benutzer aktualisieren sein Profilbild.', 'Очки на пользователя обновляют его изображение профиля.', 'Puntos en el usuario actualizan su foto de perfil.', 'Kullanıcı üzerindeki puanlar profil resmini günceller.'),
(825, '', '', 'points_on_purchase_track.', 'Points on purchase track.', 'النقاط على مسار الشراء.', 'Punten op aankoopspoor.', 'Points sur la piste d\'achat.', 'Punkte auf Kaufspur.', 'Очки на треке покупки.', 'Puntos en la pista de compra.', 'Satın alma yolundaki puanlar.'),
(826, '', '', 'points_on_user_go_pro.', 'Points on user go PRO.', 'النقاط على المستخدم GO PRO.', 'Punten op de gebruiker gaan PRO.', 'Les points sur l\'utilisateur vont PRO.', 'Punkte auf Benutzer gehen PRO.', 'Очки на пользователя становятся ПРО.', 'Los puntos en el usuario se vuelven PRO.', 'Kullanıcı üzerindeki puanlar PRO olur.'),
(827, '', '', 'points_on_review_some_one_track.', 'Points on review some one track.', 'نقاط على مراجعة بعض مسار واحد.', 'Punten op een overzicht van een track.', 'Points sur l\'examen d\'une piste.', 'Punkte bei der Überprüfung einer Spur.', 'Очки на просмотр какого-то одного трека.', 'Puntos al revisar alguna pista.', 'Bir parçayı gözden geçirme noktaları.'),
(828, '', '', 'points_on_report_some_one_track.', 'Points on reporting a track.', 'نقاط على الإبلاغ عن المسار.', 'Punten bij het melden van een track.', 'Points sur le rapport d\'une trace.', 'Punkte beim Melden eines Tracks.', 'Очки за сообщение о треке.', 'Puntos al informar una pista.', 'Bir parkuru bildirme noktaları.'),
(829, '', '', 'points_on_report_comment.', 'Points on reporting a comment.', 'نقاط على الإبلاغ عن تعليق.', 'Punten over het melden van een opmerking.', 'Points sur le rapport d\'un commentaire.', 'Punkte zum Melden eines Kommentars.', 'Очки за сообщение о комментарии.', 'Puntos sobre cómo informar un comentario.', 'Bir yorumu bildirmeye ilişkin noktalar.'),
(830, '', '', 'points_on_add_to_playlist.', 'Points on adding track to a playlist.', 'نقاط على إضافة المسار إلى قائمة التشغيل.', 'Wijst op het toevoegen van een track aan een afspeellijst.', 'Points sur l\'ajout d\'une piste à une liste de lecture.', 'Punkte beim Hinzufügen eines Titels zu einer Wiedergabeliste.', 'Очки при добавлении трека в плейлист.', 'Puntos al agregar una pista a una lista de reproducción.', 'Bir oynatma listesine parça eklemeye ilişkin noktalar.'),
(831, '', '', 'points_on_user_update_his_profile_cover.', 'Points on updating your profile cover.', 'نقاط على تحديث غلاف ملف التعريف الخاص بك.', 'Punten voor het bijwerken van uw profielomslag.', 'Points sur la mise à jour de la couverture de votre profil.', 'Punkte zum Aktualisieren Ihrer Profilabdeckung.', 'Очки по обновлению обложки вашего профиля.', 'Puntos sobre la actualización de la portada de su perfil.', 'Profil kapağınızı güncellemeyle ilgili noktalar.'),
(832, '', '', 'create_new_article', 'Create new article', 'إنشاء مقال جديد', 'Maak een nieuw artikel', 'Créer un nouvel article', 'Neuen Artikel erstellen', 'Создать новую статью', 'Crear nuevo artículo', 'Yeni makale oluştur'),
(833, '', '', 'create_blog_bost', 'Create blog post', 'إنشاء منشور مدونة', 'Maak een blogpost', 'Créer un article de blog', 'Erstellen Sie einen Blog-Beitrag', 'Создать сообщение в блоге', 'Crear publicación de blog', 'Blog yayını oluştur'),
(834, '', '', 'create_article_html', 'HTML data', 'بيانات HTML', 'HTML-gegevens', 'Données HTML', 'HTML-Daten', 'Данные HTML', 'Datos HTML', 'HTML verileri'),
(835, '', '', 'upload_file', 'Upload a file', 'قم بتحميل ملف', 'Een bestand uploaden', 'Télécharger un fichier', 'Eine Datei hochladen', 'Загрузить файл', 'Cargar un archivo', 'Bir dosya yükle'),
(836, '', '', 'upload_photo', 'Upload Photo', 'حمل الصورة', 'Upload foto', 'Envoyer la photo', 'Foto hochladen', 'Загрузить фото', 'Subir foto', 'Fotoğraf yükle'),
(837, '', '', 'success', 'Success', 'نجاح', 'Succes', 'Succès', 'Erfolg', 'Успех', 'Éxito', 'Başarı'),
(838, '', '', 'article_saved_successfully', 'Your article has been submitted successfully.', 'تم تقديم مقالتك بنجاح.', 'Uw artikel is met succes ingediend.', 'Votre article a été soumis avec succès.', 'Ihr Artikel wurde erfolgreich eingereicht.', 'Ваша статья была успешно отправлена.', 'Su artículo se ha enviado correctamente.', 'Makaleniz başarıyla gönderildi.'),
(839, '', '', 'warning', 'Warning', 'تحذير', 'Waarschuwing', 'avertissement', 'Warnung', 'Предупреждение', 'Advertencia', 'Uyarı'),
(840, '', '', 'stream_url_not_found.', 'Stream URL has not been found.', 'لم يتم العثور على عنوان البث.', 'Stream-URL is niet gevonden.', 'L\'URL du flux n\'a pas été trouvée.', 'Stream-URL wurde nicht gefunden.', 'URL-адрес потока не найден.', 'No se ha encontrado la URL de la transmisión.', 'Akış URL\'si bulunamadı.'),
(841, '', '', 'you_can_earn_points_and_transfer_them_to_your_wallet_by_doing_the_activities_below.', 'You can earn points and transfer them to your wallet by doing the activities below.', 'يمكنك كسب النقاط وتحويلها إلى محفظتك من خلال القيام بالأنشطة أدناه.', 'U kunt punten verdienen en deze overboeken naar uw portemonnee door de onderstaande activiteiten uit te voeren.', 'Vous pouvez gagner des points et les transférer dans votre portefeuille en effectuant les activités ci-dessous.', 'Sie können Punkte sammeln und diese in Ihre Brieftasche übertragen, indem Sie die folgenden Aktivitäten ausführen.', 'Вы можете зарабатывать баллы и переводить их в свой кошелек, выполняя указанные ниже действия.', 'Puede ganar puntos y transferirlos a su billetera realizando las siguientes actividades.', 'Aşağıdaki aktiviteleri yaparak puan kazanabilir ve cüzdanınıza aktarabilirsiniz.'),
(842, '', '', 'login_or_register_to_start_earning_points_', 'Login or Register to start earning points!', 'تسجيل الدخول أو التسجيل لبدء كسب النقاط!', 'Log in of registreer om punten te verdienen!', 'Connectez-vous ou inscrivez-vous pour commencer à gagner des points!', 'Melden Sie sich an oder registrieren Sie sich, um Punkte zu sammeln!', 'Войдите или зарегистрируйтесь, чтобы начать зарабатывать очки!', '¡Inicie sesión o regístrese para comenzar a ganar puntos!', 'Puan kazanmaya başlamak için Giriş Yapın veya Kaydolun!'),
(843, '', '', 'point', 'Point', 'نقطة', 'Punt', 'Point', 'Punkt', 'Точка', 'Punto', 'Nokta');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `track_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `comment_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `from_id` int(11) NOT NULL DEFAULT 0,
  `to_id` int(11) NOT NULL DEFAULT 0,
  `text` text DEFAULT NULL,
  `seen` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0,
  `from_deleted` int(11) NOT NULL DEFAULT 0,
  `to_deleted` int(11) NOT NULL DEFAULT 0,
  `sent_push` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `notification_id` varchar(50) NOT NULL DEFAULT '',
  `type_two` varchar(32) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `notifier_id` int(11) NOT NULL DEFAULT 0,
  `recipient_id` int(11) NOT NULL DEFAULT 0,
  `track_id` int(11) NOT NULL DEFAULT 0,
  `comment_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `type` varchar(20) NOT NULL DEFAULT '',
  `text` text DEFAULT NULL,
  `url` varchar(3000) NOT NULL DEFAULT '',
  `seen` varchar(50) NOT NULL DEFAULT '0',
  `sent_push` int(11) UNSIGNED DEFAULT 0,
  `admin` int(11) NOT NULL DEFAULT 0,
  `time` varchar(50) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `amount` float NOT NULL DEFAULT 0,
  `type` varchar(15) NOT NULL DEFAULT '',
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `pro_plan` varchar(100) DEFAULT '',
  `info` varchar(100) DEFAULT '0',
  `via` varchar(100) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `playlists`
--

CREATE TABLE `playlists` (
  `id` int(11) NOT NULL,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL DEFAULT 0,
  `privacy` int(11) NOT NULL DEFAULT 0,
  `thumbnail` varchar(120) NOT NULL,
  `uid` varchar(12) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `playlist_songs`
--

CREATE TABLE `playlist_songs` (
  `id` int(11) NOT NULL,
  `playlist_id` int(11) NOT NULL DEFAULT 0,
  `track_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `point_system`
--

CREATE TABLE `point_system` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `action` varchar(150) NOT NULL,
  `reword` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_add` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `obj` text NOT NULL,
  `time` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `profile_fields`
--

CREATE TABLE `profile_fields` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `length` int(11) NOT NULL DEFAULT 0,
  `placement` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'profile',
  `registration_page` int(11) NOT NULL DEFAULT 0,
  `profile_page` int(11) NOT NULL DEFAULT 0,
  `select_type` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'none',
  `active` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `track_id` int(11) NOT NULL DEFAULT 0,
  `track_owner_id` int(11) NOT NULL DEFAULT 0,
  `final_price` float NOT NULL DEFAULT 0,
  `commission` float NOT NULL DEFAULT 0,
  `price` float NOT NULL DEFAULT 0,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `track_id` int(11) NOT NULL DEFAULT 0,
  `comment_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `time` int(11) NOT NULL DEFAULT 0,
  `seen` int(11) UNSIGNED DEFAULT 0,
  `ignored` int(11) UNSIGNED DEFAULT 0,
  `mode` varchar(11) CHARACTER SET utf8mb4 DEFAULT 'track'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `track_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `rate` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `searches`
--

CREATE TABLE `searches` (
  `id` int(11) UNSIGNED NOT NULL,
  `keyword` varchar(250) NOT NULL DEFAULT '',
  `hits` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` int(11) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `session_id` varchar(100) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL DEFAULT 0,
  `platform` varchar(30) NOT NULL DEFAULT 'web',
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `site_ads`
--

CREATE TABLE `site_ads` (
  `id` int(11) NOT NULL,
  `placement` varchar(50) NOT NULL DEFAULT '',
  `code` text DEFAULT NULL,
  `active` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `site_ads`
--

INSERT INTO `site_ads` (`id`, `placement`, `code`, `active`) VALUES
(1, 'header', '', 0),
(2, 'footer', '', 0),
(3, 'side_bar', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `songs`
--

CREATE TABLE `songs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `audio_id` varchar(20) NOT NULL DEFAULT '',
  `title` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tags` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `thumbnail` varchar(150) NOT NULL DEFAULT 'default',
  `availability` int(11) NOT NULL DEFAULT 0,
  `age_restriction` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0,
  `views` int(11) NOT NULL DEFAULT 0,
  `artist_id` int(11) NOT NULL DEFAULT 0,
  `album_id` int(11) NOT NULL DEFAULT 0,
  `price` float NOT NULL DEFAULT 0,
  `duration` varchar(12) NOT NULL,
  `demo_duration` varchar(10) NOT NULL DEFAULT '0:0',
  `audio_location` varchar(120) NOT NULL DEFAULT '',
  `demo_track` varchar(200) NOT NULL DEFAULT '',
  `category_id` int(11) NOT NULL DEFAULT 0,
  `registered` varchar(12) NOT NULL,
  `size` int(11) NOT NULL DEFAULT 0,
  `dark_wave` varchar(120) NOT NULL DEFAULT '',
  `light_wave` varchar(120) NOT NULL DEFAULT '',
  `shares` int(11) NOT NULL DEFAULT 0,
  `spotlight` int(11) NOT NULL DEFAULT 0,
  `ffmpeg` int(11) UNSIGNED NOT NULL DEFAULT 1,
  `lyrics` text CHARACTER SET utf8mb4 DEFAULT NULL,
  `allow_downloads` int(11) UNSIGNED NOT NULL DEFAULT 1,
  `display_embed` int(11) UNSIGNED NOT NULL DEFAULT 1,
  `sort_order` int(11) UNSIGNED DEFAULT 0,
  `src` varchar(50) CHARACTER SET utf8mb4 DEFAULT '',
  `itunes_token` varchar(150) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `itunes_affiliate_url` varchar(300) CHARACTER SET utf8 NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `song_price`
--

CREATE TABLE `song_price` (
  `id` int(11) UNSIGNED NOT NULL,
  `price` decimal(20,2) UNSIGNED NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `song_price`
--

INSERT INTO `song_price` (`id`, `price`) VALUES
(1, '1.99'),
(2, '2.99'),
(3, '4.99'),
(4, '9.99'),
(5, '19.99');

-- --------------------------------------------------------

--
-- Table structure for table `terms`
--

CREATE TABLE `terms` (
  `id` int(11) NOT NULL,
  `type` varchar(100) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `content` longtext CHARACTER SET utf8mb4 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `terms`
--

INSERT INTO `terms` (`id`, `type`, `content`) VALUES
(1, 'terms', '&lt;h4&gt;1- Write your Terms Of Use here.&lt;/h4&gt; &lt;br&gt;                &lt;p&gt;Lorem ipsum dolor sit amet, consectetur adisdpisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis sdnostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse          cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.&lt;/p&gt; &lt;br&gt;    &lt;h4&gt;2- Random title&lt;/h4&gt; &lt;br&gt;                &lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.&lt;/p&gt;'),
(2, 'about', '&lt;h4&gt;1- Write your About us here.&lt;/h4&gt; &lt;br&gt;                &lt;p&gt;Lorem ipsum dolor sit amet, consectetur adisdpisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis sdnostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse          cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.&lt;/p&gt; &lt;br&gt;                &lt;br&gt; &lt;br&gt;                &lt;h4&gt;2- Random title&lt;/h4&gt; &lt;br&gt;                &lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.&lt;/p&gt;'),
(3, 'privacy', '&lt;h4&gt;1- Write your Privacy Policy here.&lt;/h4&gt; &lt;br&gt;                &lt;p&gt;Lorem ipsum dolor sit amet, consectetur adisdpisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis sdnostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse          cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.&lt;/p&gt; &lt;br&gt;                &lt;br&gt; &lt;br&gt;                &lt;h4&gt;2- Random title&lt;/h4&gt; &lt;br&gt;                &lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.&lt;/p&gt;'),
(4, 'dmca', '&lt;h4&gt;1- Write your DMCA Notice.&lt;/h4&gt; &lt;br&gt;                &lt;p&gt;Lorem ipsum dolor sit amet, consectetur adisdpisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis sdnostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse          cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.&lt;/p&gt; &lt;br&gt;                &lt;br&gt; &lt;br&gt;                &lt;h4&gt;2- Random title&lt;/h4&gt; &lt;br&gt;                &lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.&lt;/p&gt;');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `ip_address` varchar(150) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `password` varchar(150) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `gender` varchar(10) CHARACTER SET latin1 NOT NULL DEFAULT 'male',
  `email_code` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `language` varchar(22) CHARACTER SET latin1 NOT NULL DEFAULT 'english',
  `avatar` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT 'upload/photos/d-avatar.jpg',
  `cover` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT 'upload/photos/d-cover.jpg',
  `src` varchar(22) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `country_id` int(11) NOT NULL DEFAULT 0,
  `age` int(11) NOT NULL DEFAULT 0,
  `about` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `google` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `facebook` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `twitter` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `instagram` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `active` int(11) NOT NULL DEFAULT 0,
  `admin` int(11) NOT NULL DEFAULT 0,
  `verified` int(11) NOT NULL DEFAULT 0,
  `last_active` int(11) NOT NULL DEFAULT 0,
  `registered` varchar(40) CHARACTER SET latin1 NOT NULL DEFAULT '0000/00',
  `uploads` float NOT NULL DEFAULT 0,
  `wallet` varchar(200) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `balance` float UNSIGNED NOT NULL DEFAULT 0,
  `website` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `artist` int(11) NOT NULL DEFAULT 0,
  `is_pro` int(11) NOT NULL DEFAULT 0,
  `pro_time` int(11) NOT NULL DEFAULT 0,
  `last_follow_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `ios_device_id` varchar(100) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `android_device_id` varchar(100) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `web_device_id` varchar(100) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `email_on_follow_user` int(11) UNSIGNED DEFAULT 1,
  `email_on_liked_track` int(11) UNSIGNED DEFAULT 1,
  `email_on_liked_comment` int(11) UNSIGNED DEFAULT 1,
  `email_on_artist_status_changed` int(11) UNSIGNED DEFAULT 1,
  `email_on_receipt_status_changed` int(11) UNSIGNED DEFAULT 1,
  `email_on_new_track` int(11) UNSIGNED DEFAULT 1,
  `email_on_reviewed_track` int(11) UNSIGNED DEFAULT 1,
  `two_factor` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `new_email` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `two_factor_verified` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `new_phone` varchar(32) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `phone_number` varchar(32) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `last_login_data` text CHARACTER SET utf8mb4 DEFAULT NULL,
  `referrer` int(11) NOT NULL DEFAULT 0,
  `ref_user_id` int(11) NOT NULL DEFAULT 0,
  `upload_import` int(11) UNSIGNED DEFAULT 1,
  `paypal_email` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `info_file` text CHARACTER SET utf8 DEFAULT NULL,
  `email_on_comment_replay_mention` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `email_on_comment_mention` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `time` int(20) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `user_ads`
--

CREATE TABLE `user_ads` (
  `id` int(11) NOT NULL,
  `name` varchar(500) NOT NULL DEFAULT '',
  `results` int(11) NOT NULL DEFAULT 0,
  `spent` varchar(20) NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL DEFAULT 1,
  `audience` text DEFAULT NULL,
  `category` varchar(50) NOT NULL DEFAULT '',
  `media` varchar(1000) NOT NULL DEFAULT '',
  `audio_media` varchar(1000) NOT NULL DEFAULT '',
  `url` varchar(3000) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL DEFAULT 0,
  `placement` varchar(50) NOT NULL DEFAULT '',
  `posted` varchar(50) NOT NULL DEFAULT '0',
  `headline` varchar(1000) NOT NULL DEFAULT '',
  `description` varchar(1000) NOT NULL DEFAULT '',
  `location` varchar(1000) NOT NULL DEFAULT '',
  `type` varchar(50) NOT NULL DEFAULT '',
  `day_limit` varchar(11) NOT NULL DEFAULT '0',
  `day` varchar(50) NOT NULL DEFAULT '',
  `day_spend` varchar(11) NOT NULL DEFAULT '0',
  `ad_type` varchar(50) NOT NULL DEFAULT 'image'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_fields`
--

CREATE TABLE `user_fields` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_interest`
--

CREATE TABLE `user_interest` (
  `id` int(11) NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `category_id` int(11) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `views`
--

CREATE TABLE `views` (
  `id` int(11) NOT NULL,
  `track_id` int(11) NOT NULL DEFAULT 0,
  `album_id` int(11) NOT NULL DEFAULT 0,
  `fingerprint` varchar(50) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `withdrawal_requests`
--

CREATE TABLE `withdrawal_requests` (
  `id` int(20) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `email` varchar(200) NOT NULL DEFAULT '',
  `amount` varchar(100) NOT NULL DEFAULT '0',
  `currency` varchar(20) NOT NULL DEFAULT '',
  `requested` varchar(100) NOT NULL DEFAULT '',
  `status` int(5) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `track_id` (`track_id`),
  ADD KEY `time` (`time`);

--
-- Indexes for table `admin_invitations`
--
ALTER TABLE `admin_invitations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `code` (`code`(255));

--
-- Indexes for table `ads_transactions`
--
ALTER TABLE `ads_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `albums`
--
ALTER TABLE `albums`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `album_id` (`album_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `title` (`title`),
  ADD KEY `price` (`price`),
  ADD KEY `time` (`time`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `announcement`
--
ALTER TABLE `announcement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `active` (`active`);

--
-- Indexes for table `announcement_views`
--
ALTER TABLE `announcement_views`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `announcement_id` (`announcement_id`);

--
-- Indexes for table `apps_sessions`
--
ALTER TABLE `apps_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_id` (`session_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `platform` (`platform`);

--
-- Indexes for table `artists_tags`
--
ALTER TABLE `artists_tags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `artist_requests`
--
ALTER TABLE `artist_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `bad_login`
--
ALTER TABLE `bad_login`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ip` (`ip`);

--
-- Indexes for table `bank_receipts`
--
ALTER TABLE `bank_receipts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `banned_ip`
--
ALTER TABLE `banned_ip`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ip_address` (`ip_address`);

--
-- Indexes for table `blocks`
--
ALTER TABLE `blocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `blocked_id` (`blocked_id`),
  ADD KEY `time` (`time`);

--
-- Indexes for table `blog`
--
ALTER TABLE `blog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `title` (`title`),
  ADD KEY `category` (`category`),
  ADD KEY `tags` (`tags`(255));

--
-- Indexes for table `blog_comments`
--
ALTER TABLE `blog_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `track_id` (`article_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `blog_likes`
--
ALTER TABLE `blog_likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `article_id` (`article_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `time` (`time`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cateogry_name` (`cateogry_name`),
  ADD KEY `tracks` (`tracks`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `track_id` (`track_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `comment_replies`
--
ALTER TABLE `comment_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comment_id` (`comment_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`),
  ADD KEY `value` (`value`(255));

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_one` (`user_one`),
  ADD KEY `user_two` (`user_two`),
  ADD KEY `time` (`time`);

--
-- Indexes for table `copyrights`
--
ALTER TABLE `copyrights`
  ADD PRIMARY KEY (`id`),
  ADD KEY `track_id` (`track_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `time` (`time`);

--
-- Indexes for table `custompages`
--
ALTER TABLE `custompages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dislikes`
--
ALTER TABLE `dislikes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `track_id` (`track_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `time` (`time`);

--
-- Indexes for table `downloads`
--
ALTER TABLE `downloads`
  ADD PRIMARY KEY (`id`,`user_id`),
  ADD KEY `track_id` (`track_id`),
  ADD KEY `fingerprint` (`fingerprint`),
  ADD KEY `time` (`time`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `track_id` (`track_id`),
  ADD KEY `time` (`time`);

--
-- Indexes for table `followers`
--
ALTER TABLE `followers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `follower_id` (`follower_id`),
  ADD KEY `following_id` (`following_id`),
  ADD KEY `artist_id` (`artist_id`);

--
-- Indexes for table `langs`
--
ALTER TABLE `langs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lang_key` (`lang_key`(255)),
  ADD KEY `ref` (`ref`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `track_id` (`track_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `time` (`time`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `from_id` (`from_id`),
  ADD KEY `to_id` (`to_id`),
  ADD KEY `seen` (`seen`),
  ADD KEY `time` (`time`),
  ADD KEY `from_deleted` (`from_deleted`),
  ADD KEY `to_deleted` (`to_deleted`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipient_id` (`recipient_id`),
  ADD KEY `type` (`type`),
  ADD KEY `seen` (`seen`),
  ADD KEY `notifier_id` (`notifier_id`),
  ADD KEY `time` (`time`),
  ADD KEY `music_id` (`track_id`),
  ADD KEY `admin` (`admin`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `playlists`
--
ALTER TABLE `playlists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uid` (`uid`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `privacy` (`privacy`);

--
-- Indexes for table `playlist_songs`
--
ALTER TABLE `playlist_songs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `playlist_id` (`playlist_id`),
  ADD KEY `song_id` (`track_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `point_system`
--
ALTER TABLE `point_system`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `profile_fields`
--
ALTER TABLE `profile_fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `registration_page` (`registration_page`),
  ADD KEY `active` (`active`),
  ADD KEY `profile_page` (`profile_page`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `track_id` (`track_id`),
  ADD KEY `timestamp` (`timestamp`),
  ADD KEY `time` (`time`),
  ADD KEY `track_owner_id` (`track_owner_id`),
  ADD KEY `final_price` (`final_price`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `track_id` (`track_id`),
  ADD KEY `comment_id` (`comment_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `time` (`time`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `track_id` (`track_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `rate` (`rate`),
  ADD KEY `time` (`time`);

--
-- Indexes for table `searches`
--
ALTER TABLE `searches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `platform` (`platform`),
  ADD KEY `time` (`time`);

--
-- Indexes for table `site_ads`
--
ALTER TABLE `site_ads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `placement` (`placement`);

--
-- Indexes for table `songs`
--
ALTER TABLE `songs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `title` (`title`),
  ADD KEY `views` (`views`),
  ADD KEY `artist_id` (`artist_id`),
  ADD KEY `album_id` (`album_id`),
  ADD KEY `price` (`price`),
  ADD KEY `audio_id` (`audio_id`),
  ADD KEY `registered` (`registered`),
  ADD KEY `spotlight` (`spotlight`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `ffmpeg` (`ffmpeg`),
  ADD KEY `age_restriction` (`age_restriction`),
  ADD KEY `time` (`time`),
  ADD KEY `itunes_token` (`itunes_token`);
ALTER TABLE `songs` ADD FULLTEXT KEY `description` (`description`);
ALTER TABLE `songs` ADD FULLTEXT KEY `tags` (`tags`);

--
-- Indexes for table `song_price`
--
ALTER TABLE `song_price`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `terms`
--
ALTER TABLE `terms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type` (`type`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`),
  ADD KEY `email` (`email`),
  ADD KEY `password` (`password`),
  ADD KEY `last_active` (`last_active`),
  ADD KEY `admin` (`admin`),
  ADD KEY `active` (`active`),
  ADD KEY `registered` (`registered`),
  ADD KEY `wallet` (`wallet`),
  ADD KEY `balance` (`balance`),
  ADD KEY `pro_time` (`pro_time`),
  ADD KEY `country_id` (`country_id`),
  ADD KEY `verified` (`verified`),
  ADD KEY `artist` (`artist`),
  ADD KEY `is_pro` (`is_pro`),
  ADD KEY `time` (`time`);

--
-- Indexes for table `user_ads`
--
ALTER TABLE `user_ads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type` (`type`),
  ADD KEY `location` (`location`(255)),
  ADD KEY `placement` (`placement`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category` (`category`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `user_fields`
--
ALTER TABLE `user_fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_interest`
--
ALTER TABLE `user_interest`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `views`
--
ALTER TABLE `views`
  ADD PRIMARY KEY (`id`),
  ADD KEY `track_id` (`track_id`),
  ADD KEY `fingerprint` (`fingerprint`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `album_id` (`album_id`);

--
-- Indexes for table `withdrawal_requests`
--
ALTER TABLE `withdrawal_requests`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_invitations`
--
ALTER TABLE `admin_invitations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ads_transactions`
--
ALTER TABLE `ads_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `albums`
--
ALTER TABLE `albums`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `announcement`
--
ALTER TABLE `announcement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `announcement_views`
--
ALTER TABLE `announcement_views`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `apps_sessions`
--
ALTER TABLE `apps_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `artists_tags`
--
ALTER TABLE `artists_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `artist_requests`
--
ALTER TABLE `artist_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bad_login`
--
ALTER TABLE `bad_login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bank_receipts`
--
ALTER TABLE `bank_receipts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `banned_ip`
--
ALTER TABLE `banned_ip`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blocks`
--
ALTER TABLE `blocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog`
--
ALTER TABLE `blog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_comments`
--
ALTER TABLE `blog_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_likes`
--
ALTER TABLE `blog_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comment_replies`
--
ALTER TABLE `comment_replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `config`
--
ALTER TABLE `config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=224;

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `copyrights`
--
ALTER TABLE `copyrights`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `custompages`
--
ALTER TABLE `custompages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dislikes`
--
ALTER TABLE `dislikes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `downloads`
--
ALTER TABLE `downloads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `followers`
--
ALTER TABLE `followers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `langs`
--
ALTER TABLE `langs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=844;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `playlists`
--
ALTER TABLE `playlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `playlist_songs`
--
ALTER TABLE `playlist_songs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `point_system`
--
ALTER TABLE `point_system`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `profile_fields`
--
ALTER TABLE `profile_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `searches`
--
ALTER TABLE `searches`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `site_ads`
--
ALTER TABLE `site_ads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `songs`
--
ALTER TABLE `songs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `song_price`
--
ALTER TABLE `song_price`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `terms`
--
ALTER TABLE `terms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_ads`
--
ALTER TABLE `user_ads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_fields`
--
ALTER TABLE `user_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_interest`
--
ALTER TABLE `user_interest`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `views`
--
ALTER TABLE `views`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `withdrawal_requests`
--
ALTER TABLE `withdrawal_requests`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
