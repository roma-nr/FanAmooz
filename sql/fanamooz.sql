-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 19, 2026 at 10:01 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fanamooz`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(200) DEFAULT NULL,
  `summary` varchar(500) DEFAULT NULL,
  `body` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `link_url` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `show_on_home` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `slug`, `summary`, `body`, `image`, `link_url`, `is_active`, `show_on_home`, `sort_order`, `published_at`, `created_at`, `updated_at`) VALUES
(7, 'افتتاح فن‌آموز', NULL, 'سامانه آموزش آنلاین راه‌اندازی شد', 'به فن‌آموز خوش آمدید.', NULL, NULL, 1, 1, 1, '2026-05-23 18:09:24', '2026-05-23 18:09:24', '2026-05-23 18:09:24'),
(8, 'ثبت‌نام ترم جدید', NULL, 'ثبت‌نام دوره‌ها آغاز شد', 'به بخش دوره‌ها مراجعه کنید.', NULL, NULL, 1, 1, 2, '2026-05-23 18:09:24', '2026-05-23 18:09:24', '2026-05-23 18:09:24'),
(10, 'تست اولیه', NULL, '<p>برشبشبیشر&nbsp; فیغلرزرررر زز</p>', '<p>فبلذسفیلباذسیبسثشفبسبف</p>\r\n<p>ثالرثهییریهعذیزنس</p>\r\n<p>غهیثرعصثغریطهعلذطهعصثیذزهعستی</p>\r\n<p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;نتهعیهعسذاعهسی</p>', 'announcements/c62aeeff17b52f95ddbb7e9fc6c4396c.jpeg', '', 1, 1, 1, '2026-06-12 08:01:07', '2026-06-12 08:01:07', '2026-06-12 08:01:07'),
(11, 'با ما همراه باشید', NULL, 'فلفلبللبل', 'لبلذزطذر لبرلبذفذ', 'announcements/ccf050356834706f5259d5e7d81eb5bb.jpg', NULL, 1, 1, 2, '2026-06-18 20:15:00', '2026-06-18 20:15:18', '2026-06-18 20:15:18'),
(12, 'لرترا', NULL, '<p>غیفبازلل</p>', '<p>لارررررررردراد</p>', 'announcements/f37138421687544e311ac187ea0dae04.png', NULL, 1, 1, 3, '2026-06-18 21:11:00', '2026-06-18 21:11:10', '2026-06-18 21:11:10'),
(13, 'شروع ترم تابستان ۱۴۰۵', 'summer-1405', 'ثبت‌نام دوره‌های تابستانی آغاز شد.', '<p>دانشجویان عزیز می‌توانند از هم‌اکنون برای دوره‌های متنوع برنامه‌نویسی، شبکه و هوش مصنوعی ثبت‌نام کنند. ظرفیت محدود است.</p><p><a href=\"courses.php\">مشاهده دوره‌ها</a></p>', 'announcements/blog-1.jpg', NULL, 1, 1, 1, '2026-06-19 07:14:07', '2026-06-19 07:14:07', '2026-06-19 07:14:07'),
(14, 'کارگاه رایگان آشنایی با هوش مصنوعی', 'ai-workshop', 'جمعه ۲۵ خرداد، ساعت ۱۰ صبح', '<p>در این کارگاه با مفاهیم اولیه هوش مصنوعی و مسیر یادگیری آن آشنا می‌شوید. حضور برای عموم آزاد است.</p><p>لینک شرکت در کارگاه متعاقباً اعلام می‌شود.</p>', 'announcements/blog-2.jpg', NULL, 1, 1, 2, '2026-06-19 07:14:07', '2026-06-19 07:14:07', '2026-06-19 07:14:07'),
(15, 'اطلاعیه مهم: بروزرسانی سامانه', 'system-update', 'سامانه فن‌آموز در تاریخ ۳۰ خرداد به‌روزرسانی می‌شود.', '<p>به‌منظور بهبود عملکرد، سامانه از ساعت ۲ بامداد تا ۶ صبح در دسترس نخواهد بود. لطفاً پیش از آن اطلاعات خود را ذخیره کنید.</p>', NULL, NULL, 1, 1, 3, '2026-06-19 07:14:07', '2026-06-19 07:14:07', '2026-06-19 07:14:07');

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `due_at` datetime DEFAULT NULL,
  `max_score` decimal(5,2) NOT NULL DEFAULT 100.00,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assignment_submissions`
--

CREATE TABLE `assignment_submissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `assignment_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `body_text` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `graded_at` timestamp NULL DEFAULT NULL,
  `graded_by` int(10) UNSIGNED DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certificate_requests`
--

CREATE TABLE `certificate_requests` (
  `id` int(10) UNSIGNED NOT NULL,
  `enrollment_id` int(10) UNSIGNED NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `certificate_number` varchar(50) DEFAULT NULL,
  `admin_note` text DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` int(10) UNSIGNED DEFAULT NULL,
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `certificate_requests`
--

INSERT INTO `certificate_requests` (`id`, `enrollment_id`, `status`, `certificate_number`, `admin_note`, `reviewed_at`, `reviewed_by`, `requested_at`) VALUES
(1, 6, 'approved', 'FA-2026-000001', 'مبارک باشه', '2026-06-12 17:14:07', 1, '2026-06-12 17:13:31');

-- --------------------------------------------------------

--
-- Table structure for table `cms_pages`
--

CREATE TABLE `cms_pages` (
  `id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(50) NOT NULL,
  `title` varchar(200) NOT NULL,
  `body` longtext DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cms_pages`
--

INSERT INTO `cms_pages` (`id`, `slug`, `title`, `body`, `updated_at`) VALUES
(1, 'about', 'درباره ما', '<p>سامانه <strong>فن&zwnj;آموز</strong> بستر آنلاین دانشکده&zwnj;های <em>ملی مهارت</em> است.</p>\r\n<p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <img src=\"https://ucarecdn.com/d24b8f7a-7d78-4db3-8521-23d9b0dafc12/-/preview/\" width=\"436\" height=\"127\"></p>', '2026-06-11 09:37:50'),
(2, 'contact', 'تماس با ما', '<p>برای پشتیبانی با ما تماس بگیرید.</p>', '2026-05-16 14:29:06');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(10) UNSIGNED NOT NULL,
  `teacher_id` int(10) UNSIGNED DEFAULT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `province_id` int(10) UNSIGNED DEFAULT NULL,
  `institution_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `price` decimal(12,0) NOT NULL DEFAULT 0,
  `is_paid` tinyint(1) NOT NULL DEFAULT 0,
  `min_pass_grade` decimal(5,2) NOT NULL DEFAULT 60.00,
  `session_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `duration_hours` int(10) UNSIGNED DEFAULT 0,
  `session_duration_minutes` int(10) UNSIGNED DEFAULT 90,
  `session_days` varchar(100) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `schedule_notes` text DEFAULT NULL,
  `status` enum('draft','published','archived') NOT NULL DEFAULT 'draft',
  `featured_on_home` tinyint(1) NOT NULL DEFAULT 0,
  `home_sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `teacher_id`, `category_id`, `province_id`, `institution_id`, `title`, `slug`, `description`, `image`, `price`, `is_paid`, `min_pass_grade`, `session_count`, `duration_hours`, `session_duration_minutes`, `session_days`, `start_date`, `end_date`, `schedule_notes`, `status`, `featured_on_home`, `home_sort_order`, `created_at`, `updated_at`) VALUES
(14, 14, 1, 1, 1, 'دوره پایتون مقدماتی', 'دوره-پایتون-مقدماتی', '<p>دوره مقدماتی پایتون برای افرادی که میخواهند از صفر شروع کنند</p>', 'courses/d0f7a0d2ad91bce6a4709c2d930ebf32.jpg', 0, 1, 60.00, 4, 0, 90, NULL, '2026-06-19', '2026-06-22', 'بسیزسطیط', 'published', 1, 0, '2026-06-11 11:04:38', '2026-06-19 05:09:46'),
(15, 14, 2, NULL, NULL, 'دوره شبکه', 'دوره-شبکه-سطح-پیشرفته', '<p>دوره شبکه برای کسانی میخواهند وارد بازار کار شوند</p>', 'courses/25661bd4a7082b97f4e5388a6c98ebbc.jpg', 1500000, 1, 60.00, 6, 0, 90, NULL, '2026-06-10', '2026-06-13', 'لللللللللللللللللللللللل', 'published', 1, 0, '2026-06-11 14:08:10', '2026-06-19 05:19:55'),
(21, 13, 2, NULL, NULL, 'jhvjhv', '', 'gchcch nv', NULL, 0, 0, 60.00, 0, 0, 90, 'hu jnjknj bjkk', '2026-06-19', '2026-06-23', 'hvjhvhbhb nm n  n mn   n mnb', 'published', 1, 2, '2026-06-18 21:26:55', '2026-06-18 21:26:55'),
(22, 1, 1, NULL, NULL, 'لاذتا', 'slug-1781818055', NULL, NULL, 0, 0, 60.00, 0, 0, 90, NULL, NULL, NULL, NULL, 'draft', 0, 0, '2026-06-18 21:27:35', '2026-06-18 21:27:35'),
(23, 1, 1, NULL, NULL, 'vvhvnn', 'slug-1781818061', NULL, NULL, 0, 0, 60.00, 0, 0, 90, NULL, NULL, NULL, NULL, 'draft', 0, 0, '2026-06-18 21:27:41', '2026-06-18 21:27:41'),
(24, 14, 20, NULL, NULL, 'ففلفلفل', 'ففلفلفل', '<p>5قفثقفببث</p>', 'courses/936b7ed1a2efe4e82ec91fc6fd257e5d.jpg', 23344000, 1, 60.00, 5, 0, 90, NULL, '2026-06-19', '2026-06-24', '<p>قف5صثقفبثب54فقث</p>', 'published', 1, 3, '2026-06-19 04:10:46', '2026-06-19 04:10:46'),
(25, 14, 1, NULL, NULL, 'آموزش جامع PHP و MySQL', 'php-mysql', '<p>در این دوره از صفر تا صد برنامه‌نویسی وب با PHP و پایگاه داده MySQL را یاد می‌گیرید. مباحث شامل:</p>\r\n<ul>\r\n<li>متغیرها، آرایه‌ها و توابع</li>\r\n<li>کار با فرم‌ها و نشست‌ها</li>\r\n<li>اتصال به دیتابیس و عملیات CRUD</li>\r\n<li>پروژه عملی: فروشگاه اینترنتی</li>\r\n</ul>\r\n<p>پیش‌نیاز: آشنایی ابتدایی با HTML</p>', 'courses/case-1.jpg', 2500000, 1, 60.00, 10, 0, 90, NULL, '2026-07-01', '2026-08-05', 'شنبه‌ها و سه‌شنبه‌ها ساعت ۱۶ تا ۱۸', 'published', 1, 1, '2026-06-19 06:48:50', '2026-06-19 06:48:50'),
(26, 14, 2, NULL, NULL, 'شبکه‌های کامپیوتری پیشرفته', 'network-adv', '<p>آشنایی با مفاهیم Routing، Switching، امنیت شبکه و پیاده‌سازی عملی با Cisco Packet Tracer.</p>\r\n<p>مناسب برای داوطلبان آزمون CCNA</p>', 'courses/case-2.jpg', 3000000, 1, 70.00, 8, 0, 90, NULL, '2026-07-05', '2026-08-20', 'یک‌شنبه‌ها و چهارشنبه‌ها ۱۰ تا ۱۲', 'published', 1, 2, '2026-06-19 06:48:51', '2026-06-19 06:48:51'),
(27, 14, 3, NULL, NULL, 'امنیت سایبری مقدماتی', 'cyber-sec', '<p>با تهدیدات رایج، رمزنگاری، فایروال و امنیت وب آشنا شوید. ابزارهای Kali Linux را در آزمایشگاه مجازی تمرین می‌کنیم.</p>', 'courses/case-3.jpg', 2000000, 1, 65.00, 6, 0, 90, NULL, '2026-07-10', '2026-08-15', 'جمعه‌ها ۹ تا ۱۲', 'published', 1, 3, '2026-06-19 06:48:52', '2026-06-19 06:48:52'),
(28, 14, 18, NULL, NULL, 'پایتون برای هوش مصنوعی', 'python-ai', '<p>از مبانی پایتون تا کتابخانه‌های NumPy، Pandas و Matplotlib. در پایان یک پروژه تحلیل داده واقعی انجام می‌دهیم.</p>', 'courses/case-4.jpg', 1800000, 1, 60.00, 12, 0, 90, NULL, '2026-07-15', '2026-09-01', 'دوشنبه‌ها ۱۴ تا ۱۶', 'published', 1, 4, '2026-06-19 06:48:52', '2026-06-19 06:48:52'),
(29, 14, 19, NULL, NULL, 'یادگیری ماشین با Scikit-Learn', 'ml-sklearn', '<p>مفاهیم رگرسیون، طبقه‌بندی، خوشه‌بندی و کاهش ابعاد. کار با دیتاست‌های واقعی.</p>', 'courses/case-5.jpg', 3500000, 1, 70.00, 10, 0, 90, NULL, '2026-07-20', '2026-08-30', 'سه‌شنبه‌ها ۱۷ تا ۱۹', 'published', 1, 5, '2026-06-19 06:48:52', '2026-06-19 06:48:52'),
(30, 14, 20, NULL, NULL, 'داده‌کاوی کاربردی', 'data-mining', '<p>تکنیک‌های پیش‌پردازش، قوانین انجمنی، درخت تصمیم و شبکه‌های عصبی ساده. ابزار: Python و Weka.</p>', 'courses/case-6.jpg', 2200000, 1, 65.00, 8, 0, 90, NULL, '2026-08-01', '2026-09-10', 'پنج‌شنبه‌ها ۱۰ تا ۱۲', 'published', 1, 6, '2026-06-19 06:48:52', '2026-06-19 06:48:52');

-- --------------------------------------------------------

--
-- Table structure for table `course_categories`
--

CREATE TABLE `course_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_categories`
--

INSERT INTO `course_categories` (`id`, `name`, `slug`, `is_active`, `sort_order`) VALUES
(1, 'نرم‌افزار', 'software', 1, 1),
(2, 'شبکه', 'network', 1, 2),
(3, 'امنیت', 'security', 1, 3),
(18, 'برنامه‌نویسی', 'programming', 1, 1),
(19, 'هوش مصنوعی', 'ai', 1, 4),
(20, 'داده‌کاوی', 'data-mining', 1, 5);

-- --------------------------------------------------------

--
-- Table structure for table `course_messages`
--

CREATE TABLE `course_messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `sender_id` int(10) UNSIGNED NOT NULL,
  `body` text NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_messages`
--

INSERT INTO `course_messages` (`id`, `course_id`, `sender_id`, `body`, `file_path`, `created_at`) VALUES
(5, 14, 14, 'درود بر همگی', NULL, '2026-06-11 11:13:33'),
(6, 14, 14, 'کلاس را دیدید؟', NULL, '2026-06-11 11:13:45'),
(7, 14, 9, 'بله استاد', NULL, '2026-06-11 11:14:20'),
(8, 14, 11, 'بله استاد', NULL, '2026-06-11 12:30:39'),
(9, 14, 11, 'خسته نباشید \r\nخدانگهدار', NULL, '2026-06-11 12:30:56');

-- --------------------------------------------------------

--
-- Table structure for table `course_message_reads`
--

CREATE TABLE `course_message_reads` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `last_read_message_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_message_reads`
--

INSERT INTO `course_message_reads` (`user_id`, `course_id`, `last_read_message_id`, `updated_at`) VALUES
(9, 14, 9, '2026-06-11 14:18:35'),
(11, 14, 9, '2026-06-11 12:30:57'),
(14, 14, 9, '2026-06-11 13:00:58'),
(14, 15, 0, '2026-06-12 12:00:09');

-- --------------------------------------------------------

--
-- Table structure for table `course_sessions`
--

CREATE TABLE `course_sessions` (
  `id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `session_number` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `video_path` varchar(255) DEFAULT NULL,
  `audio_path` varchar(255) DEFAULT NULL,
  `pdf_path` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `adobe_connect_url` varchar(500) DEFAULT NULL,
  `scheduled_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_sessions`
--

INSERT INTO `course_sessions` (`id`, `course_id`, `title`, `session_number`, `description`, `video_path`, `audio_path`, `pdf_path`, `sort_order`, `adobe_connect_url`, `scheduled_at`, `created_at`, `updated_at`) VALUES
(2, 14, 'جلسه اول: آشنایی', 1, 'آشنایی با مفاهیم پایه', 'courses/sessions/d70ead70baded5f095bd4568b90be5d5.mp4', 'courses/sessions/0a25f6bc5e730cd92e585d144615af5a.mp3', 'courses/sessions/cf372ad47f42a2b293b13b3096554fdf.pdf', 1, NULL, NULL, '2026-06-11 11:10:04', '2026-06-11 11:10:04');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `status` enum('pending_payment','waiting_approval','active','completed','cancelled') NOT NULL DEFAULT 'pending_payment',
  `final_grade` decimal(5,2) DEFAULT NULL,
  `receipt_path` varchar(255) DEFAULT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `user_id`, `course_id`, `status`, `final_grade`, `receipt_path`, `enrolled_at`) VALUES
(5, 9, 14, 'active', 55.00, NULL, '2026-06-11 11:11:05'),
(6, 11, 14, 'completed', 80.00, NULL, '2026-06-11 11:15:34'),
(7, 9, 15, 'active', NULL, NULL, '2026-06-11 15:26:58'),
(8, 12, 15, 'pending_payment', NULL, NULL, '2026-06-13 05:34:39'),
(9, 9, 24, 'cancelled', NULL, 'payments/3078214b81c248e170a528035b5d5724.jpg', '2026-06-19 05:49:59');

-- --------------------------------------------------------

--
-- Table structure for table `error_reports`
--

CREATE TABLE `error_reports` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `user_role` enum('admin','teacher','student','system') NOT NULL,
  `type` enum('video_missing','general','system','user') NOT NULL DEFAULT 'general',
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('new','read','resolved') NOT NULL DEFAULT 'new',
  `admin_response` text DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `institutions`
--

CREATE TABLE `institutions` (
  `id` int(10) UNSIGNED NOT NULL,
  `province_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `institutions`
--

INSERT INTO `institutions` (`id`, `province_id`, `name`, `slug`, `is_active`, `sort_order`, `created_at`) VALUES
(6, 9, 'دانشکده ملی مهارت آذربایجان شرقی پسران', 'east-azerbaijan-boys', 1, 1, '2026-06-11 18:12:51'),
(7, 10, 'دانشکده ملی مهارت آذربایجان غربی پسران', 'west-azerbaijan-boys', 1, 1, '2026-06-11 18:12:51'),
(8, 11, 'دانشکده ملی مهارت اردبیل پسران', 'ardabil-boys', 1, 1, '2026-06-11 18:12:51'),
(9, 12, 'دانشکده ملی مهارت اصفهان پسران', 'isfahan-boys', 1, 1, '2026-06-11 18:12:51'),
(10, 13, 'دانشکده ملی مهارت البرز پسران', 'alborz-boys', 1, 1, '2026-06-11 18:12:51'),
(11, 14, 'دانشکده ملی مهارت ایلام پسران', 'ilam-boys', 1, 1, '2026-06-11 18:12:51'),
(12, 15, 'دانشکده ملی مهارت بوشهر پسران', 'bushehr-boys', 1, 1, '2026-06-11 18:12:51'),
(13, 16, 'دانشکده ملی مهارت تهران پسران', 'tehran-boys', 1, 1, '2026-06-11 18:12:51'),
(14, 17, 'دانشکده ملی مهارت چهارمحال و بختیاری پسران', 'chaharmahal-bakhtiari-boys', 1, 1, '2026-06-11 18:12:51'),
(15, 18, 'دانشکده ملی مهارت خراسان جنوبی پسران', 'south-khorasan-boys', 1, 1, '2026-06-11 18:12:51'),
(16, 19, 'دانشکده ملی مهارت خراسان رضوی پسران', 'razavi-khorasan-boys', 1, 1, '2026-06-11 18:12:51'),
(17, 20, 'دانشکده ملی مهارت خراسان شمالی پسران', 'north-khorasan-boys', 1, 1, '2026-06-11 18:12:51'),
(18, 21, 'دانشکده ملی مهارت خوزستان پسران', 'khuzestan-boys', 1, 1, '2026-06-11 18:12:51'),
(19, 22, 'دانشکده ملی مهارت زنجان پسران', 'zanjan-boys', 1, 1, '2026-06-11 18:12:51'),
(20, 23, 'دانشکده ملی مهارت سمنان پسران', 'semnan-boys', 1, 1, '2026-06-11 18:12:51'),
(21, 24, 'دانشکده ملی مهارت سیستان و بلوچستان پسران', 'sistan-baluchestan-boys', 1, 1, '2026-06-11 18:12:51'),
(22, 25, 'دانشکده ملی مهارت فارس پسران', 'fars-boys', 1, 1, '2026-06-11 18:12:51'),
(23, 26, 'دانشکده ملی مهارت قزوین پسران', 'qazvin-boys', 1, 1, '2026-06-11 18:12:51'),
(24, 27, 'دانشکده ملی مهارت قم پسران', 'qom-boys', 1, 1, '2026-06-11 18:12:51'),
(25, 28, 'دانشکده ملی مهارت کردستان پسران', 'kurdistan-boys', 1, 1, '2026-06-11 18:12:51'),
(26, 29, 'دانشکده ملی مهارت کرمان پسران', 'kerman-boys', 1, 1, '2026-06-11 18:12:51'),
(27, 30, 'دانشکده ملی مهارت کرمانشاه پسران', 'kermanshah-boys', 1, 1, '2026-06-11 18:12:51'),
(28, 31, 'دانشکده ملی مهارت کهگیلویه و بویراحمد پسران', 'kohgiluyeh-boyer-ahmad-boys', 1, 1, '2026-06-11 18:12:51'),
(29, 32, 'دانشکده ملی مهارت گلستان پسران', 'golestan-boys', 1, 1, '2026-06-11 18:12:51'),
(30, 33, 'دانشکده ملی مهارت گیلان پسران', 'gilan-boys', 1, 1, '2026-06-11 18:12:51'),
(31, 34, 'دانشکده ملی مهارت لرستان پسران', 'lorestan-boys', 1, 1, '2026-06-11 18:12:51'),
(32, 35, 'دانشکده ملی مهارت مازندران پسران', 'mazandaran-boys', 1, 1, '2026-06-11 18:12:51'),
(33, 36, 'دانشکده ملی مهارت مرکزی پسران', 'markazi-boys', 1, 1, '2026-06-11 18:12:51'),
(34, 37, 'دانشکده ملی مهارت هرمزگان پسران', 'hormozgan-boys', 1, 1, '2026-06-11 18:12:51'),
(35, 38, 'دانشکده ملی مهارت همدان پسران', 'hamadan-boys', 1, 1, '2026-06-11 18:12:51'),
(36, 39, 'دانشکده ملی مهارت یزد پسران', 'yazd-boys', 1, 1, '2026-06-11 18:12:51'),
(37, 9, 'دانشکده ملی مهارت آذربایجان شرقی دختران', 'east-azerbaijan-girls', 1, 2, '2026-06-11 18:12:51'),
(38, 10, 'دانشکده ملی مهارت آذربایجان غربی دختران', 'west-azerbaijan-girls', 1, 2, '2026-06-11 18:12:51'),
(39, 11, 'دانشکده ملی مهارت اردبیل دختران', 'ardabil-girls', 1, 2, '2026-06-11 18:12:51'),
(40, 12, 'دانشکده ملی مهارت اصفهان دختران', 'isfahan-girls', 1, 2, '2026-06-11 18:12:51'),
(41, 13, 'دانشکده ملی مهارت البرز دختران', 'alborz-girls', 1, 2, '2026-06-11 18:12:51'),
(42, 14, 'دانشکده ملی مهارت ایلام دختران', 'ilam-girls', 1, 2, '2026-06-11 18:12:51'),
(43, 15, 'دانشکده ملی مهارت بوشهر دختران', 'bushehr-girls', 1, 2, '2026-06-11 18:12:51'),
(44, 16, 'دانشکده ملی مهارت تهران دختران', 'tehran-girls', 1, 2, '2026-06-11 18:12:51'),
(45, 17, 'دانشکده ملی مهارت چهارمحال و بختیاری دختران', 'chaharmahal-bakhtiari-girls', 1, 2, '2026-06-11 18:12:51'),
(46, 18, 'دانشکده ملی مهارت خراسان جنوبی دختران', 'south-khorasan-girls', 1, 2, '2026-06-11 18:12:51'),
(47, 19, 'دانشکده ملی مهارت خراسان رضوی دختران', 'razavi-khorasan-girls', 1, 2, '2026-06-11 18:12:51'),
(48, 20, 'دانشکده ملی مهارت خراسان شمالی دختران', 'north-khorasan-girls', 1, 2, '2026-06-11 18:12:51'),
(49, 21, 'دانشکده ملی مهارت خوزستان دختران', 'khuzestan-girls', 1, 2, '2026-06-11 18:12:51'),
(50, 22, 'دانشکده ملی مهارت زنجان دختران', 'zanjan-girls', 1, 2, '2026-06-11 18:12:51'),
(51, 23, 'دانشکده ملی مهارت سمنان دختران', 'semnan-girls', 1, 2, '2026-06-11 18:12:51'),
(52, 24, 'دانشکده ملی مهارت سیستان و بلوچستان دختران', 'sistan-baluchestan-girls', 1, 2, '2026-06-11 18:12:51'),
(53, 25, 'دانشکده ملی مهارت فارس دختران', 'fars-girls', 1, 2, '2026-06-11 18:12:51'),
(54, 26, 'دانشکده ملی مهارت قزوین دختران', 'qazvin-girls', 1, 2, '2026-06-11 18:12:51'),
(55, 27, 'دانشکده ملی مهارت قم دختران', 'qom-girls', 1, 2, '2026-06-11 18:12:51'),
(56, 28, 'دانشکده ملی مهارت کردستان دختران', 'kurdistan-girls', 1, 2, '2026-06-11 18:12:51'),
(57, 29, 'دانشکده ملی مهارت کرمان دختران', 'kerman-girls', 1, 2, '2026-06-11 18:12:51'),
(58, 30, 'دانشکده ملی مهارت کرمانشاه دختران', 'kermanshah-girls', 1, 2, '2026-06-11 18:12:51'),
(59, 31, 'دانشکده ملی مهارت کهگیلویه و بویراحمد دختران', 'kohgiluyeh-boyer-ahmad-girls', 1, 2, '2026-06-11 18:12:51'),
(60, 32, 'دانشکده ملی مهارت گلستان دختران', 'golestan-girls', 1, 2, '2026-06-11 18:12:51'),
(61, 33, 'دانشکده ملی مهارت گیلان دختران', 'gilan-girls', 1, 2, '2026-06-11 18:12:51'),
(62, 34, 'دانشکده ملی مهارت لرستان دختران', 'lorestan-girls', 1, 2, '2026-06-11 18:12:51'),
(63, 35, 'دانشکده ملی مهارت مازندران دختران', 'mazandaran-girls', 1, 2, '2026-06-11 18:12:51'),
(64, 36, 'دانشکده ملی مهارت مرکزی دختران', 'markazi-girls', 1, 2, '2026-06-11 18:12:51'),
(65, 37, 'دانشکده ملی مهارت هرمزگان دختران', 'hormozgan-girls', 1, 2, '2026-06-11 18:12:51'),
(66, 38, 'دانشکده ملی مهارت همدان دختران', 'hamadan-girls', 1, 2, '2026-06-11 18:12:51'),
(67, 39, 'دانشکده ملی مهارت یزد دختران', 'yazd-girls', 1, 2, '2026-06-11 18:12:51');

-- --------------------------------------------------------

--
-- Table structure for table `interests`
--

CREATE TABLE `interests` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `interests`
--

INSERT INTO `interests` (`id`, `name`, `slug`, `category_id`, `is_active`, `sort_order`) VALUES
(1, 'برنامه‌نویسی', 'programming', 1, 1, 1),
(2, 'شبکه', 'network', 2, 1, 2),
(3, 'امنیت سایبری', 'cybersecurity', 3, 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(150) NOT NULL,
  `token` varchar(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expired_at` datetime DEFAULT NULL,
  `used` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `created_at`, `expired_at`, `used`) VALUES
(1, 'negar.ray.r@gmail.com', '3dc7a6133a6dfb944927e9f6a0a1941be61f44ae20fa1b0d2f0a95f42d3db203', '2026-06-18 16:32:48', '2026-06-18 21:02:48', 0);

-- --------------------------------------------------------

--
-- Table structure for table `provinces`
--

CREATE TABLE `provinces` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `provinces`
--

INSERT INTO `provinces` (`id`, `name`, `slug`, `is_active`, `sort_order`, `created_at`) VALUES
(9, 'آذربایجان شرقی', 'east-azerbaijan', 1, 1, '2026-06-11 17:30:15'),
(10, 'آذربایجان غربی', 'west-azerbaijan', 1, 2, '2026-06-11 17:30:15'),
(11, 'اردبیل', 'ardabil', 1, 3, '2026-06-11 17:30:15'),
(12, 'اصفهان', 'isfahan', 1, 4, '2026-06-11 17:30:15'),
(13, 'البرز', 'alborz', 1, 5, '2026-06-11 17:30:15'),
(14, 'ایلام', 'ilam', 1, 6, '2026-06-11 17:30:15'),
(15, 'بوشهر', 'bushehr', 1, 7, '2026-06-11 17:30:15'),
(16, 'تهران', 'tehran', 1, 8, '2026-06-11 17:30:15'),
(17, 'چهارمحال و بختیاری', 'chaharmahal-bakhtiari', 1, 9, '2026-06-11 17:30:15'),
(18, 'خراسان جنوبی', 'south-khorasan', 1, 10, '2026-06-11 17:30:15'),
(19, 'خراسان رضوی', 'razavi-khorasan', 1, 11, '2026-06-11 17:30:15'),
(20, 'خراسان شمالی', 'north-khorasan', 1, 12, '2026-06-11 17:30:15'),
(21, 'خوزستان', 'khuzestan', 1, 13, '2026-06-11 17:30:15'),
(22, 'زنجان', 'zanjan', 1, 14, '2026-06-11 17:30:15'),
(23, 'سمنان', 'semnan', 1, 15, '2026-06-11 17:30:15'),
(24, 'سیستان و بلوچستان', 'sistan-baluchestan', 1, 16, '2026-06-11 17:30:15'),
(25, 'فارس', 'fars', 1, 17, '2026-06-11 17:30:15'),
(26, 'قزوین', 'qazvin', 1, 18, '2026-06-11 17:30:15'),
(27, 'قم', 'qom', 1, 19, '2026-06-11 17:30:15'),
(28, 'کردستان', 'kurdistan', 1, 20, '2026-06-11 17:30:15'),
(29, 'کرمان', 'kerman', 1, 21, '2026-06-11 17:30:15'),
(30, 'کرمانشاه', 'kermanshah', 1, 22, '2026-06-11 17:30:15'),
(31, 'کهگیلویه و بویراحمد', 'kohgiluyeh-boyer-ahmad', 1, 23, '2026-06-11 17:30:15'),
(32, 'گلستان', 'golestan', 1, 24, '2026-06-11 17:30:15'),
(33, 'گیلان', 'gilan', 1, 25, '2026-06-11 17:30:15'),
(34, 'لرستان', 'lorestan', 1, 26, '2026-06-11 17:30:15'),
(35, 'مازندران', 'mazandaran', 1, 27, '2026-06-11 17:30:15'),
(36, 'مرکزی', 'markazi', 1, 28, '2026-06-11 17:30:15'),
(37, 'هرمزگان', 'hormozgan', 1, 29, '2026-06-11 17:30:15'),
(38, 'همدان', 'hamadan', 1, 30, '2026-06-11 17:30:15'),
(39, 'یزد', 'yazd', 1, 31, '2026-06-11 17:30:15');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `user_role` enum('student','teacher','admin','guest','system') NOT NULL,
  `type` enum('video_missing','general','system','user') NOT NULL DEFAULT 'general',
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('new','read','resolved') NOT NULL DEFAULT 'new',
  `admin_response` text DEFAULT NULL,
  `resolved_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `session_attendance`
--

CREATE TABLE `session_attendance` (
  `id` int(10) UNSIGNED NOT NULL,
  `course_session_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `present` tinyint(1) NOT NULL DEFAULT 1,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `recorded_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `session_attendance`
--

INSERT INTO `session_attendance` (`id`, `course_session_id`, `user_id`, `present`, `recorded_at`, `recorded_by`) VALUES
(1, 2, 9, 0, '2026-06-13 05:41:21', 14),
(2, 2, 11, 1, '2026-06-13 05:41:21', 14);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_key`, `setting_value`, `updated_at`) VALUES
('bank_account_name', 'نگار رستاقی چالکی', '2026-06-11 19:32:01'),
('bank_card_number', '6104-3374-6659-7216', '2026-06-11 19:31:21'),
('bank_name', 'بانک ملت', '2026-06-11 19:30:07'),
('contact_address', 'تهران، ایران', '2026-05-16 14:29:05'),
('contact_email', 'info@fanamooz.ir', '2026-05-16 14:29:05'),
('contact_phone', '021-00000000', '2026-05-16 14:29:05'),
('logo_1', 'logos/95ee5db0244ad3c916e6eba447ee0725.png', '2026-06-11 09:37:50'),
('logo_1_alt', 'وزارت علوم', '2026-06-11 09:37:50'),
('logo_1_url', '', '2026-05-16 14:29:05'),
('logo_2', 'logos/a681e558a0b6c460bc88a9c19312af2c.png', '2026-06-11 09:37:50'),
('logo_2_alt', 'دانشگاه ملی مهارت', '2026-06-11 09:37:50'),
('logo_2_url', '', '2026-05-16 14:29:05'),
('logo_3', 'logos/fd3c7204f61816a0694249743d601588.png', '2026-06-11 09:52:44'),
('logo_3_alt', 'دانشکده مائده', '2026-06-11 09:52:43'),
('logo_3_url', '', '2026-05-16 14:29:05'),
('president_name', 'دکتر سید محمد حسینی', '2026-06-18 19:31:35'),
('site_name', 'فن‌آموز', '2026-05-16 14:29:05'),
('zarinpal_merchant_id', '', '2026-05-16 14:29:05'),
('zarinpal_sandbox', '1', '2026-05-16 14:29:05');

-- --------------------------------------------------------

--
-- Table structure for table `student_interests`
--

CREATE TABLE `student_interests` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `interest_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_interests`
--

INSERT INTO `student_interests` (`user_id`, `interest_id`, `created_at`) VALUES
(9, 1, '2026-06-03 05:34:16'),
(9, 3, '2026-06-03 05:34:16'),
(11, 1, '2026-06-11 11:15:27'),
(11, 2, '2026-06-11 11:15:27'),
(11, 3, '2026-06-11 11:15:27'),
(12, 2, '2026-06-13 05:34:28'),
(12, 3, '2026-06-13 05:34:28'),
(25, 1, '2026-06-19 07:41:26'),
(25, 2, '2026-06-19 07:41:26'),
(26, 2, '2026-06-19 07:41:26'),
(26, 3, '2026-06-19 07:41:26'),
(27, 1, '2026-06-19 07:41:26'),
(27, 3, '2026-06-19 07:41:26'),
(28, 1, '2026-06-19 07:41:26'),
(29, 2, '2026-06-19 07:41:26'),
(30, 1, '2026-06-19 07:41:26'),
(30, 3, '2026-06-19 07:41:26'),
(31, 2, '2026-06-19 07:41:26'),
(31, 3, '2026-06-19 07:41:27'),
(32, 1, '2026-06-19 07:41:27'),
(33, 2, '2026-06-19 07:41:27'),
(34, 3, '2026-06-19 07:41:27');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_applications`
--

CREATE TABLE `teacher_applications` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `education` text NOT NULL,
  `work_experience` text NOT NULL,
  `skills_summary` varchar(1000) NOT NULL,
  `resume_path` varchar(255) DEFAULT NULL,
  `admin_note` text DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` int(10) UNSIGNED DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teacher_applications`
--

INSERT INTO `teacher_applications` (`id`, `user_id`, `education`, `work_experience`, `skills_summary`, `resume_path`, `admin_note`, `reviewed_at`, `reviewed_by`, `submitted_at`, `updated_at`) VALUES
(1, 13, 'دیپلم کامپیوتر دارم و مدرک ICDL و مدرک زبان انگلیسی', 'در موسسه افق درس دادم', 'برنامه نویسی وب', 'teachers/resumes/3e4ee5908ff2693a34694752d9403b56.pdf', 'fggggggggg', '2026-06-11 10:56:01', 1, '2026-06-11 10:19:28', '2026-06-11 10:56:01'),
(2, 14, 'دکترای کامپیوتر دارم. و در دانشگاه شریف درس خوانده ام', 'در دانشگاه شریف درس میدهم', 'شبکه بلد هستم', 'teachers/resumes/3074a0c98e53e17caf86c12376870288.pdf', NULL, '2026-06-11 11:00:49', 1, '2026-06-11 11:00:07', '2026-06-11 11:00:49');

-- --------------------------------------------------------

--
-- Table structure for table `useful_links`
--

CREATE TABLE `useful_links` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `url` varchar(500) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `show_on_home` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `useful_links`
--

INSERT INTO `useful_links` (`id`, `title`, `description`, `url`, `icon`, `is_active`, `show_on_home`, `sort_order`, `created_at`, `updated_at`) VALUES
(7, 'وزارت علوم', 'پورتال وزارت علوم', 'https://www.msrt.ir', 'links/34c624b7239bb9c4be7c9ae27d67b05e.png', 1, 1, 1, '2026-05-23 18:09:24', '2026-06-13 06:01:21'),
(10, 'دانشگاه ملی مهارت', 'دانشگاه ملی مهارت', 'https://tvu.ac.ir/', NULL, 1, 1, 2, '2026-06-11 09:59:53', '2026-06-11 09:59:53'),
(11, 'دانشکده دختران گرگان', 'دانشکده مائده', 'https://d-gorgan.tvu.ac.ir/', NULL, 1, 1, 3, '2026-06-11 10:01:29', '2026-06-11 10:01:29'),
(12, 'سایت W3Schools', 'آموزش برنامه نویسی', 'https://www.w3schools.com/', NULL, 1, 1, 4, '2026-06-11 10:03:18', '2026-06-11 10:03:18');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `role` enum('admin','teacher','student') NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(200) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `province_id` int(10) UNSIGNED DEFAULT NULL,
  `institution_id` int(10) UNSIGNED DEFAULT NULL,
  `student_code` varchar(50) DEFAULT NULL,
  `education_level` varchar(50) DEFAULT NULL,
  `national_id` varchar(10) DEFAULT NULL,
  `teacher_status` enum('none','pending','approved','rejected') NOT NULL DEFAULT 'none',
  `first_login_done` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role`, `username`, `email`, `password_hash`, `full_name`, `phone`, `province_id`, `institution_id`, `student_code`, `education_level`, `national_id`, `teacher_status`, `first_login_done`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin', 'admin@fanamooz.local', '$2y$10$qj1B4gYmXBjQ9I6h7kHAOuorA/rwP7Zn/clpmtc.j57tn9xcwBFWa', 'مدیر سیستم', NULL, NULL, NULL, NULL, NULL, NULL, 'none', 1, 1, '2026-05-16 14:29:06', '2026-05-23 18:09:25'),
(9, 'student', '03111119302024', NULL, '$2y$10$WBNvfZJQVmmVKHaX60BhYuJJJ81EwieXV8qnN1Bx12G2m95IcGfhO', 'نگار رستاقی', '09369234656', NULL, NULL, '03111119302024', NULL, '2111232807', 'none', 1, 1, '2026-06-03 05:09:10', '2026-06-10 10:18:54'),
(11, 'student', '03111119302025', NULL, '$2y$10$XqhIYTfOcsZSjn5KDblRFuJZmmWE7ScYHxiCbpLsy/FrztCWmh1Du', 'ثمین کریمی', '09369234756', NULL, NULL, '03111119302025', NULL, '2111144444', 'none', 1, 1, '2026-06-03 05:11:58', '2026-06-11 11:15:28'),
(12, 'student', '03111111102024', NULL, '$2y$10$cSy16vT9nEcLSRx5yE/G9OdkGFeTnOLbBJ2ugJjtEv9ksnk5c3LeC', 'یلدا خمر', '09369234656', 19, 47, '03111111102024', NULL, '2111232807', 'none', 1, 1, '2026-06-10 08:44:46', '2026-06-13 05:34:28'),
(13, 'teacher', 'negar.ray.r@gmail.com', 'negar.ray.r@gmail.com', '$2y$10$ylBMeyrBpYc0gv57rok0Eepdbg30nlGkLYmznToCsGbPrpJee3/Cq', 'نگار رستاقی', '09369234656', NULL, NULL, NULL, NULL, '2111232807', 'rejected', 1, 1, '2026-06-11 10:19:28', '2026-06-11 10:56:01'),
(14, 'teacher', 'roma@gmail.com', 'roma@gmail.com', '$2y$10$dX3jUOX2p4NgxMaPXQD/vOiR2dWuqu9lj/8V18KVEg0/MQG4OwG0W', 'roma mills', '09369234656', NULL, NULL, NULL, NULL, '2111232807', 'approved', 1, 1, '2026-06-11 11:00:07', '2026-06-12 12:21:50'),
(25, 'student', '0311003302001', NULL, '$2y$10$XqhIYTfOcsZSjn5KDblRFuJZmmWE7ScYHxiCbpLsy/FrztCWmh1Du', 'سارا محمدی', '09120000010', NULL, 6, '0311003302001', NULL, '1234567801', 'none', 1, 1, '2026-06-19 07:41:25', '2026-06-19 07:41:25'),
(26, 'student', '0311003402002', NULL, '$2y$10$XqhIYTfOcsZSjn5KDblRFuJZmmWE7ScYHxiCbpLsy/FrztCWmh1Du', 'امیر حسینی', '09120000011', NULL, 6, '0311003402002', NULL, '1234567802', 'none', 1, 1, '2026-06-19 07:41:25', '2026-06-19 07:41:25'),
(27, 'student', '0311003502003', NULL, '$2y$10$XqhIYTfOcsZSjn5KDblRFuJZmmWE7ScYHxiCbpLsy/FrztCWmh1Du', 'فاطمه کریمی', '09120000012', NULL, 6, '0311003502003', NULL, '1234567803', 'none', 1, 1, '2026-06-19 07:41:25', '2026-06-19 07:41:25'),
(28, 'student', '0311003602004', NULL, '$2y$10$XqhIYTfOcsZSjn5KDblRFuJZmmWE7ScYHxiCbpLsy/FrztCWmh1Du', 'علی رضایی', '09120000013', NULL, 6, '0311003602004', NULL, '1234567804', 'none', 1, 1, '2026-06-19 07:41:25', '2026-06-19 07:41:25'),
(29, 'student', '0311003702005', NULL, '$2y$10$XqhIYTfOcsZSjn5KDblRFuJZmmWE7ScYHxiCbpLsy/FrztCWmh1Du', 'نرگس احمدی', '09120000014', NULL, 6, '0311003702005', NULL, '1234567805', 'none', 1, 1, '2026-06-19 07:41:25', '2026-06-19 07:41:25'),
(30, 'student', '0311103308006', NULL, '$2y$10$XqhIYTfOcsZSjn5KDblRFuJZmmWE7ScYHxiCbpLsy/FrztCWmh1Du', 'رضا قاسمی', '09120000015', NULL, 7, '0311103308006', NULL, '1234567806', 'none', 1, 1, '2026-06-19 07:41:25', '2026-06-19 07:41:25'),
(31, 'student', '0311103408007', NULL, '$2y$10$XqhIYTfOcsZSjn5KDblRFuJZmmWE7ScYHxiCbpLsy/FrztCWmh1Du', 'زهرا رستمی', '09120000016', NULL, 7, '0311103408007', NULL, '1234567807', 'none', 1, 1, '2026-06-19 07:41:25', '2026-06-19 07:41:25'),
(32, 'student', '0311103508008', NULL, '$2y$10$XqhIYTfOcsZSjn5KDblRFuJZmmWE7ScYHxiCbpLsy/FrztCWmh1Du', 'مهدی نوروزی', '09120000017', NULL, 7, '0311103508008', NULL, '1234567808', 'none', 1, 1, '2026-06-19 07:41:25', '2026-06-19 07:41:25'),
(33, 'student', '0311103608009', NULL, '$2y$10$XqhIYTfOcsZSjn5KDblRFuJZmmWE7ScYHxiCbpLsy/FrztCWmh1Du', 'الهام صادقی', '09120000018', NULL, 7, '0311103608009', NULL, '1234567809', 'none', 1, 1, '2026-06-19 07:41:25', '2026-06-19 07:41:25'),
(34, 'student', '0311103708010', NULL, '$2y$10$XqhIYTfOcsZSjn5KDblRFuJZmmWE7ScYHxiCbpLsy/FrztCWmh1Du', 'محمد موسوی', '09120000019', NULL, 7, '0311103708010', NULL, '1234567810', 'none', 1, 1, '2026-06-19 07:41:25', '2026-06-19 07:41:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_assign_course` (`course_id`),
  ADD KEY `fk_assign_creator` (`created_by`);

--
-- Indexes for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_submission` (`assignment_id`,`user_id`),
  ADD KEY `fk_sub_user` (`user_id`),
  ADD KEY `fk_sub_grader` (`graded_by`);

--
-- Indexes for table `certificate_requests`
--
ALTER TABLE `certificate_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_cert_enrollment` (`enrollment_id`),
  ADD KEY `fk_cert_reviewer` (`reviewed_by`);

--
-- Indexes for table `cms_pages`
--
ALTER TABLE `cms_pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_course_teacher` (`teacher_id`),
  ADD KEY `fk_course_category` (`category_id`);

--
-- Indexes for table `course_categories`
--
ALTER TABLE `course_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `course_messages`
--
ALTER TABLE `course_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_msg_sender` (`sender_id`),
  ADD KEY `idx_msg_course_time` (`course_id`,`created_at`);

--
-- Indexes for table `course_message_reads`
--
ALTER TABLE `course_message_reads`
  ADD PRIMARY KEY (`user_id`,`course_id`),
  ADD KEY `fk_cmr_course` (`course_id`);

--
-- Indexes for table `course_sessions`
--
ALTER TABLE `course_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_session_course` (`course_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_enrollment` (`user_id`,`course_id`),
  ADD KEY `fk_enrollment_course` (`course_id`);

--
-- Indexes for table `error_reports`
--
ALTER TABLE `error_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `fk_report_user` (`user_id`);

--
-- Indexes for table `institutions`
--
ALTER TABLE `institutions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_institution` (`province_id`,`slug`);

--
-- Indexes for table `interests`
--
ALTER TABLE `interests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_interest_category` (`category_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `provinces`
--
ALTER TABLE `provinces`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `fk_reports_user` (`user_id`);

--
-- Indexes for table `session_attendance`
--
ALTER TABLE `session_attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_attendance` (`course_session_id`,`user_id`),
  ADD KEY `fk_att_user` (`user_id`),
  ADD KEY `fk_att_recorder` (`recorded_by`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `student_interests`
--
ALTER TABLE `student_interests`
  ADD PRIMARY KEY (`user_id`,`interest_id`),
  ADD KEY `fk_si_interest` (`interest_id`);

--
-- Indexes for table `teacher_applications`
--
ALTER TABLE `teacher_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_ta_user` (`user_id`),
  ADD KEY `fk_ta_reviewer` (`reviewed_by`);

--
-- Indexes for table `useful_links`
--
ALTER TABLE `useful_links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_username` (`username`),
  ADD UNIQUE KEY `uk_student_institution` (`institution_id`,`student_code`,`national_id`),
  ADD KEY `fk_user_province` (`province_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `certificate_requests`
--
ALTER TABLE `certificate_requests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cms_pages`
--
ALTER TABLE `cms_pages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `course_categories`
--
ALTER TABLE `course_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `course_messages`
--
ALTER TABLE `course_messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `course_sessions`
--
ALTER TABLE `course_sessions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `error_reports`
--
ALTER TABLE `error_reports`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `institutions`
--
ALTER TABLE `institutions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `interests`
--
ALTER TABLE `interests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `provinces`
--
ALTER TABLE `provinces`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `session_attendance`
--
ALTER TABLE `session_attendance`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `teacher_applications`
--
ALTER TABLE `teacher_applications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `useful_links`
--
ALTER TABLE `useful_links`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `fk_assign_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_assign_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  ADD CONSTRAINT `fk_sub_assign` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sub_grader` FOREIGN KEY (`graded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_sub_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `certificate_requests`
--
ALTER TABLE `certificate_requests`
  ADD CONSTRAINT `fk_cert_enrollment` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cert_reviewer` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `fk_course_category` FOREIGN KEY (`category_id`) REFERENCES `course_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_course_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `course_messages`
--
ALTER TABLE `course_messages`
  ADD CONSTRAINT `fk_msg_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_msg_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_message_reads`
--
ALTER TABLE `course_message_reads`
  ADD CONSTRAINT `fk_cmr_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cmr_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_sessions`
--
ALTER TABLE `course_sessions`
  ADD CONSTRAINT `fk_session_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `fk_enrollment_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_enrollment_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `error_reports`
--
ALTER TABLE `error_reports`
  ADD CONSTRAINT `fk_report_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `institutions`
--
ALTER TABLE `institutions`
  ADD CONSTRAINT `fk_institution_province` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `interests`
--
ALTER TABLE `interests`
  ADD CONSTRAINT `fk_interest_category` FOREIGN KEY (`category_id`) REFERENCES `course_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `fk_reports_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `session_attendance`
--
ALTER TABLE `session_attendance`
  ADD CONSTRAINT `fk_att_recorder` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_att_session` FOREIGN KEY (`course_session_id`) REFERENCES `course_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_att_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_interests`
--
ALTER TABLE `student_interests`
  ADD CONSTRAINT `fk_si_interest` FOREIGN KEY (`interest_id`) REFERENCES `interests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_si_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `teacher_applications`
--
ALTER TABLE `teacher_applications`
  ADD CONSTRAINT `fk_ta_reviewer` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ta_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_institution` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_user_province` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
