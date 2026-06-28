-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 28, 2026 at 09:27 PM
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
(13, 'شروع ترم تابستان ۱۴۰۵', 'summer-1405', '', '<p>داده&zwnj;های خام همه جا هستند، اما ارزش در الگوهای پنهان آنهاست. این دوره شما را با فرایند کامل داده&zwnj;کاوی آشنا می&zwnj;کند: از پاکسازی داده تا استخراج قوانین انجمنی و ساخت مدل&zwnj;های پیش&zwnj;بینی. از ابزارهای Python و Weka استفاده می&zwnj;کنیم.</p>\r\n<p><strong>فهرست مهارت&zwnj;های کسب&zwnj;شده:</strong></p>\r\n<ul>\r\n<li>\r\n<p>آشنایی با مراحل CRISP-DM</p>\r\n</li>\r\n<li>\r\n<p>پاکسازی و تبدیل داده&zwnj;ها</p>\r\n</li>\r\n<li>\r\n<p>تحلیل اکتشافی داده (EDA)</p>\r\n</li>\r\n<li>\r\n<p>قوانین انجمنی (Apriori)</p>\r\n</li>\r\n<li>\r\n<p>درخت تصمیم و Rule-Based Classifiers</p>\r\n</li>\r\n<li>\r\n<p>خوشه&zwnj;بندی (K-Means, DBSCAN)</p>\r\n</li>\r\n<li>\r\n<p>ارزیابی مدل&zwnj;ها با معیارهای مختلف</p>\r\n</li>\r\n</ul>\r\n<p><strong>تصویر ۱:</strong>&nbsp;گراف قوانین انجمنی استخراج&zwnj;شده از یک فروشگاه<br><strong>تصویر ۲:</strong>&nbsp;تفکیک خوشه&zwnj;ها در یک دیتاست</p>\r\n<p><strong>پیش&zwnj;نیازها:</strong></p>\r\n<ul>\r\n<li>\r\n<p>آشنایی با پایتون و کتابخانه Pandas (سطح مقدماتی)</p>\r\n</li>\r\n<li>\r\n<p>مبانی آمار توصیفی</p>\r\n</li>\r\n</ul>\r\n<p><strong>قالب دوره:</strong></p>\r\n<ul>\r\n<li>\r\n<p>۸ جلسه ۱.۵ ساعته</p>\r\n</li>\r\n<li>\r\n<p>پنج&zwnj;شنبه&zwnj;ها ۱۰ تا ۱۲</p>\r\n</li>\r\n<li>\r\n<p>کارگاه&zwnj;های عملی با دیتاست&zwnj;های واقعی</p>\r\n</li>\r\n</ul>\r\n<p><strong>دستاورد نهایی:</strong></p>\r\n<p>پس از این دوره، می&zwnj;توانید یک پروژه داده&zwnj;کاوی کامل را از جمع&zwnj;آوری داده تا ارائه نتایج اجرا کنید و بینش&zwnj;های ارزشمندی برای کسب&zwnj;وکارها استخراج نمایید.</p>', 'announcements/442f69178216cfc5877aab912a3f7617.jpg', NULL, 1, 0, 1, '2026-06-19 07:14:00', '2026-06-19 07:14:07', '2026-06-21 20:57:12'),
(14, 'کارگاه رایگان آشنایی با هوش مصنوعی', 'ai-workshop', '', '<pre>این کارگاه کاملاً رایگان و آنلاین برگزار می&zwnj;شود و در آن به سوالات زیر پاسخ می&zwnj;دهیم:\r\n\r\n🧠 هوش مصنوعی چیست و چه کاربردهایی دارد؟  \r\n📚 چه پیش&zwnj;نیازهایی برای یادگیری AI نیاز است؟  \r\n🗺️ مسیر یادگیری از صفر تا اشتغال در حوزه هوش مصنوعی  \r\n🛠️ معرفی ابزارها و کتابخانه&zwnj;های پایتون (NumPy, Pandas, Scikit-Learn)\r\n\r\n👤 مدرس: مهندس علیرضا احمدی  \r\n📅 زمان: جمعه ۲۶ تیر ۱۴۰۵، ساعت ۱۰ تا ۱۲  \r\n🔗 لینک ورود به کلاس آنلاین، نیم ساعت قبل از شروع در پنل شما نمایش داده می&zwnj;شود.</pre>', 'announcements/1655e58dc7cdda7ce775c715c67a3d9d.jpg', NULL, 1, 0, 2, '2026-06-19 07:14:00', '2026-06-19 07:14:07', '2026-06-21 20:59:10'),
(15, 'اطلاعیه مهم: بروزرسانی سامانه', 'system-update', '', '<pre>به منظور ارتقای سرعت و امنیت سامانه، یک به&zwnj;روزرسانی فنی برنامه&zwnj;ریزی شده است.\r\n\r\n📅 **تاریخ قطعی:** سه&zwnj;شنبه ۳۰ خرداد ۱۴۰۵  \r\n🕑 **بازه زمانی:** ۲ بامداد تا ۶ صبح  \r\n⚠️ **تأثیر:** در این بازه، ورود به پنل کاربری، مشاهده محتوا، ارسال تکالیف و چت موقتاً غیرفعال است.\r\n\r\nلطفاً پیش از این تاریخ، فایل&zwnj;های مهم خود را ذخیره و تکالیف ضروری را ارسال کنید.\r\n\r\nپس از بروزرسانی، امکانات جدیدی در دسترس خواهد بود. از شکیبایی شما سپاسگزاریم.</pre>', 'announcements/82a66cdcb292d065b0bbe16e1baee54c.jpg', NULL, 1, 0, 3, '2026-06-19 07:14:00', '2026-06-19 07:14:07', '2026-06-21 21:00:15'),
(17, 'مسابقه برنامه نویسی', NULL, '', '<pre>🎉 **اولین مسابقه برنامه&zwnj;نویسی دانشجویی فن&zwnj;آموز**  \r\nبه مناسبت روز ملی فناوری اطلاعات برگزار می&zwnj;گردد.\r\n\r\n📌 **زمان:** پنج&zwnj;شنبه ۱ مرداد ۱۴۰۵، ساعت ۱۵ تا ۱۹  \r\n📌 **مکان:** آنلاین &ndash; از طریق پنل دانشجویی  \r\n📌 **شرکت&zwnj;کنندگان:** تمامی دانشجویان فعال فن&zwnj;آموز  \r\n📌 **زبان مسابقه:** پایتون &ndash; شامل ۵ سوال الگوریتمی  \r\n\r\n🏆 **جوایز:**  \r\n- نفر اول: یک دستگاه لپ&zwnj;تاپ + گواهی طلایی  \r\n- نفر دوم: هدفون بی&zwnj;سیم + گواهی نقره&zwnj;ای  \r\n- نفر سوم: کتاب برنامه&zwnj;نویسی + گواهی برنزی  \r\n\r\n📝 ثبت&zwnj;نام: تا ۲۸ تیر از طریق صفحه مسابقه (در پنل شما).  </pre>', 'announcements/2165453e41c9b0a1cb05e2d664cf33fb.jpg', NULL, 1, 0, 4, '2026-06-21 21:01:00', '2026-06-21 21:01:58', '2026-06-21 21:01:58');

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
  `status` enum('draft','published','archived','disabled') NOT NULL DEFAULT 'draft',
  `featured_on_home` tinyint(1) NOT NULL DEFAULT 0,
  `home_sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `teacher_id`, `category_id`, `province_id`, `institution_id`, `title`, `slug`, `description`, `image`, `price`, `is_paid`, `min_pass_grade`, `session_count`, `duration_hours`, `session_duration_minutes`, `session_days`, `start_date`, `end_date`, `schedule_notes`, `status`, `featured_on_home`, `home_sort_order`, `created_at`, `updated_at`) VALUES
(25, 14, 1, NULL, NULL, 'آموزش جامع PHP و MySQL', 'php-mysql', 'PHP و MySQL ستون فقرات میلیون‌ها وب‌سایت پویا در جهان هستند. در این دوره، از صفر مطلق شروع می‌کنیم: ابتدا با سینتکس PHP و مفاهیم پایه (متغیرها، آرایه‌ها، توابع، شرط‌ها و حلقه‌ها) آشنا می‌شوید. سپس به سراغ طراحی پایگاه داده‌های رابطه‌ای با MySQL می‌رویم و یاد می‌گیرید چگونه جداول را طراحی، نرمال‌سازی و مدیریت کنید.\r\n\r\nآنچه خواهید آموخت:\r\n\r\nنصب و راه‌اندازی محیط توسعه (XAMPP)\r\n\r\nاصول برنامه‌نویسی سمت سرور با PHP\r\n\r\nمدیریت فرم‌ها و اعتبارسنجی ورودی\r\n\r\nکار با Session و Cookie\r\n\r\nاتصال به MySQL با PDO و اجرای امن کوئری‌ها\r\n\r\nعملیات CRUD (ایجاد، خواندن، بروزرسانی، حذف)\r\n\r\nپیاده‌سازی سیستم ثبت‌نام و ورود کاربران\r\n\r\nطراحی پنل مدیریت\r\n\r\nساخت یک فروشگاه اینترنتی کامل با سبد خرید\r\n\r\nارسال ایمیل و مدیریت فایل\r\n\r\nتصویر ۱: محیط برنامه‌نویسی و دیتابیس phpMyAdmin\r\nتصویر ۲: نمای خروجی نهایی فروشگاه\r\n\r\nپیش‌نیازها:\r\n\r\nآشنایی ابتدایی با HTML و CSS (مفاهیم پایه)\r\n\r\nعلاقه به برنامه‌نویسی و حل مسئله\r\n\r\nقالب دوره:\r\n\r\n۱۰ جلسه ۲ ساعته\r\n\r\nهر هفته ۲ جلسه (شنبه‌ها و سه‌شنبه‌ها ۱۶ تا ۱۸)\r\n\r\nترکیبی از آموزش عملی، پروژه‌محور و پرسش و پاسخ زنده\r\n\r\nدستاورد نهایی:\r\n\r\nدر پایان دوره، یک فروشگاه اینترنتی کامل با پنل مدیریت، سبد خرید و درگاه پرداخت (آفلاین) خواهید ساخت و دانش لازم برای ورود به بازار کار برنامه‌نویسی PHP را کسب می‌کنید.', 'courses/ab01bb9d5c51b44fe8e08faf8ad66f8e.jpg', 2500000, 1, 65.00, 10, 0, 90, NULL, '2026-07-01', '2026-08-05', NULL, 'published', 1, 1, '2026-06-19 06:48:50', '2026-06-21 20:38:47'),
(26, 14, 2, NULL, NULL, 'شبکه‌های کامپیوتری پیشرفته', 'network-adv', '<p><strong>توضیحات:</strong></p>\r\n<p>اگر می&zwnj;خواهید شبکه&zwnj;های کامپیوتری را نه&zwnj;تنها بفهمید، بلکه در عمل پیاده&zwnj;سازی کنید، این دوره دقیقاً برای شما طراحی شده است. از مفاهیم لایه&zwnj;های OSI و TCP/IP تا پیکربندی روترها و سوئیچ&zwnj;ها، همه را با مثال&zwnj;های واقعی و شبیه&zwnj;سازی&zwnj;شده فرا می&zwnj;گیرید.</p>\r\n<p><strong>آنچه خواهید آموخت:</strong></p>\r\n<ul>\r\n<li>\r\n<p>مبانی شبکه و آدرس&zwnj;دهی IPv4/IPv6</p>\r\n</li>\r\n<li>\r\n<p>Subnetting و VLSM</p>\r\n</li>\r\n<li>\r\n<p>مفاهیم Switching و VLAN</p>\r\n</li>\r\n<li>\r\n<p>پروتکل&zwnj;های Spanning Tree</p>\r\n</li>\r\n<li>\r\n<p>Routing Protocols (RIP, OSPF, EIGRP)</p>\r\n</li>\r\n<li>\r\n<p>امنیت شبکه و Access Control Lists (ACL)</p>\r\n</li>\r\n<li>\r\n<p>مفاهیم NAT و DHCP</p>\r\n</li>\r\n<li>\r\n<p>عیب&zwnj;یابی عملی با ابزارهای Cisco</p>\r\n</li>\r\n<li>\r\n<p>آشنایی با Wireshark برای تحلیل بسته&zwnj;ها</p>\r\n</li>\r\n</ul>\r\n<p><strong>تصویر ۱:</strong>&nbsp;شماتیک یک شبکه با سوئیچ و روتر<br><strong>تصویر ۲:</strong>&nbsp;محیط شبیه&zwnj;ساز Cisco Packet Tracer</p>\r\n<p><strong>پیش&zwnj;نیازها:</strong></p>\r\n<ul>\r\n<li>\r\n<p>آشنایی اولیه با کامپیوتر و اینترنت</p>\r\n</li>\r\n<li>\r\n<p>نیاز به مدرک خاصی نیست؛ از صفر شروع می&zwnj;شود</p>\r\n</li>\r\n</ul>\r\n<p><strong>قالب دوره:</strong></p>\r\n<ul>\r\n<li>\r\n<p>۸ جلسه ۲ ساعته</p>\r\n</li>\r\n<li>\r\n<p>یک&zwnj;شنبه&zwnj;ها و چهارشنبه&zwnj;ها ۱۰ تا ۱۲</p>\r\n</li>\r\n<li>\r\n<p>تمرین&zwnj;های آزمایشگاهی در Packet Tracer + پرسش و پاسخ</p>\r\n</li>\r\n</ul>\r\n<p><strong>دستاورد نهایی:</strong></p>\r\n<p>پس از این دوره، توانایی طراحی، پیکربندی و عیب&zwnj;یابی شبکه&zwnj;های متوسط را خواهید داشت و برای آزمون CCNA کاملاً آماده می&zwnj;شوید.</p>\r\n<p><br><br></p>', 'courses/c036dc362181d5394447854ae9933b99.webp', 3000000, 1, 70.00, 8, 0, 90, NULL, '2026-07-05', '2026-08-20', NULL, 'published', 1, 2, '2026-06-19 06:48:51', '2026-06-21 20:44:14'),
(27, 14, 3, NULL, NULL, 'امنیت سایبری مقدماتی', 'cyber-sec', '<p>هر روز اخبار جدیدی از حملات سایبری می&zwnj;شنویم. این دوره شما را با مفاهیم اساسی امنیت اطلاعات، ابزارهای تست نفوذ و روش&zwnj;های دفاعی آشنا می&zwnj;کند. رویکرد ما کاملاً عملی است: از Kali Linux برای اجرای حملات کنترل&zwnj;شده و از ابزارهای امنیتی برای محافظت از سیستم&zwnj;ها استفاده می&zwnj;کنیم.</p>\r\n<p><strong>مباحث کلیدی:</strong></p>\r\n<ul>\r\n<li>\r\n<p>مثلث CIA (محرمانگی، یکپارچگی، دسترس&zwnj;پذیری)</p>\r\n</li>\r\n<li>\r\n<p>رمزنگاری و الگوریتم&zwnj;های متقارن/نامتقارن</p>\r\n</li>\r\n<li>\r\n<p>فایروال&zwnj;ها و سیستم&zwnj;های تشخیص نفوذ (IDS/IPS)</p>\r\n</li>\r\n<li>\r\n<p>حملات رایج: SQL Injection, XSS, CSRF</p>\r\n</li>\r\n<li>\r\n<p>امنیت شبکه&zwnj;های بی&zwnj;سیم</p>\r\n</li>\r\n<li>\r\n<p>مفاهیم اولیه امنیت وب و روش&zwnj;های جلوگیری</p>\r\n</li>\r\n<li>\r\n<p>اسکن آسیب&zwnj;پذیری با Nmap و Nessus</p>\r\n</li>\r\n<li>\r\n<p>پیاده&zwnj;سازی VPN</p>\r\n</li>\r\n</ul>\r\n<p><strong>تصویر ۱:</strong>&nbsp;محیط Kali Linux با ابزارهای تست نفوذ<br><strong>تصویر ۲:</strong>&nbsp;گزارش اسکن آسیب&zwnj;پذیری یک وب&zwnj;سایت</p>\r\n<p><strong>پیش&zwnj;نیازها:</strong></p>\r\n<ul>\r\n<li>\r\n<p>آشنایی اولیه با سیستم&zwnj;عامل (ویندوز یا لینوکس)</p>\r\n</li>\r\n<li>\r\n<p>اطلاعات پایه شبکه (IP، پورت) مفید است اما اجباری نیست</p>\r\n</li>\r\n</ul>\r\n<p><strong>قالب دوره:</strong></p>\r\n<ul>\r\n<li>\r\n<p>۶ جلسه ۳ ساعته</p>\r\n</li>\r\n<li>\r\n<p>جمعه&zwnj;ها ۹ تا ۱۲</p>\r\n</li>\r\n<li>\r\n<p>کارگاه&zwnj;های عملی + جلسات پرسش و پاسخ</p>\r\n</li>\r\n</ul>\r\n<p><strong>دستاورد نهایی:</strong></p>\r\n<p>در پایان این دوره، می&zwnj;توانید آسیب&zwnj;پذیری&zwnj;های متداول را شناسایی، تحلیل و راه&zwnj;حل&zwnj;های مناسب ارائه دهید و پایه&zwnj;ای محکم برای ادامه مسیر در هک اخلاق&zwnj;مدار یا تحلیل امنیت داشته باشید.</p>\r\n<p><br><br></p>', 'courses/7e3d20cf1a64315725f511a7709f8e9c.jpeg', 2000000, 1, 80.00, 6, 0, 90, NULL, '2026-07-10', '2026-08-15', NULL, 'published', 1, 3, '2026-06-19 06:48:52', '2026-06-21 20:45:01'),
(28, 14, 18, NULL, NULL, 'پایتون برای هوش مصنوعی', 'python-ai', '<p>پایتون امروزه زبان اول توسعه در حوزه هوش مصنوعی و یادگیری ماشین است. در این دوره، از مفاهیم برنامه&zwnj;نویسی پایتون شروع می&zwnj;کنیم و با کتابخانه&zwnj;های قدرتمندی مانند NumPy، Pandas و Matplotlib آشنا می&zwnj;شوید. در ادامه، به سراغ پیاده&zwnj;سازی الگوریتم&zwnj;های ساده یادگیری ماشین می&zwnj;رویم و یک پروژه تحلیل داده واقعی را اجرا می&zwnj;کنیم.</p>\r\n<p><strong>سرفصل&zwnj;های اصلی:</strong></p>\r\n<ul>\r\n<li>\r\n<p>اصول برنامه&zwnj;نویسی پایتون: انواع داده، ساختارهای کنترلی، توابع</p>\r\n</li>\r\n<li>\r\n<p>کار با کتابخانه NumPy برای محاسبات عددی</p>\r\n</li>\r\n<li>\r\n<p>کار با Pandas برای خواندن، پاکسازی و تحلیل داده&zwnj;ها</p>\r\n</li>\r\n<li>\r\n<p>تصویرسازی داده با Matplotlib و Seaborn</p>\r\n</li>\r\n<li>\r\n<p>مفاهیم اولیه یادگیری ماشین: دسته&zwnj;بندی، رگرسیون</p>\r\n</li>\r\n<li>\r\n<p>پیاده&zwnj;سازی یک پروژه تحلیل داده از صفر تا صد</p>\r\n</li>\r\n</ul>\r\n<p><strong>تصویر ۱:</strong>&nbsp;نمودارهای تعاملی ایجادشده با Matplotlib<br><strong>تصویر ۲:</strong>&nbsp;خروجی آنالیز داده&zwnj;های یک دیتاست واقعی</p>\r\n<p><strong>پیش&zwnj;نیازها:</strong></p>\r\n<ul>\r\n<li>\r\n<p>آشنایی ابتدایی با منطق برنامه&zwnj;نویسی (هر زبانی)</p>\r\n</li>\r\n<li>\r\n<p>ریاضیات دبیرستان (جبر و آمار مقدماتی)</p>\r\n</li>\r\n</ul>\r\n<p><strong>قالب دوره:</strong></p>\r\n<ul>\r\n<li>\r\n<p>۱۲ جلسه ۱.۵ ساعته</p>\r\n</li>\r\n<li>\r\n<p>دوشنبه&zwnj;ها ۱۴ تا ۱۶</p>\r\n</li>\r\n<li>\r\n<p>تمرین&zwnj;های برنامه&zwnj;نویسی زنده + پروژه نهایی</p>\r\n</li>\r\n</ul>\r\n<p><strong>دستاورد نهایی:</strong></p>\r\n<p>با اتمام این دوره، می&zwnj;توانید داده&zwnj;های خام را دریافت، تحلیل و بصری&zwnj;سازی کنید و آماده ورود به دوره&zwnj;های تخصصی&zwnj;تر یادگیری ماشین شوید.</p>\r\n<p><br><br></p>', 'courses/2c75c540adabdfc8d257545c5f7cffe8.png', 1800000, 1, 60.00, 12, 0, 90, NULL, '2026-07-15', '2026-09-01', NULL, 'published', 1, 4, '2026-06-19 06:48:52', '2026-06-21 20:47:07'),
(29, 14, 19, NULL, NULL, 'یادگیری ماشین با Scikit-Learn', 'ml-sklearn', '<p>یادگیری ماشین دیگر یک رؤیا نیست؛ شما می&zwnj;توانید مدل&zwnj;های پیش&zwnj;بینی قدرتمند بسازید. در این دوره با کتابخانه Scikit-Learn که استاندارد طلایی ML در پایتون است، کار می&zwnj;کنیم. مفاهیم کلیدی مانند رگرسیون، طبقه&zwnj;بندی، خوشه&zwnj;بندی و کاهش ابعاد را با دیتاست&zwnj;های واقعی تمرین می&zwnj;کنید.</p>\r\n<p><strong>موضوعات دوره:</strong></p>\r\n<ul>\r\n<li>\r\n<p>مرور سریع پایتون و کتابخانه&zwnj;های لازم</p>\r\n</li>\r\n<li>\r\n<p>پیش&zwnj;پردازش داده: مقیاس&zwnj;سازی، Encoding، مدیریت داده گمشده</p>\r\n</li>\r\n<li>\r\n<p>رگرسیون خطی و لجستیک</p>\r\n</li>\r\n<li>\r\n<p>درخت تصمیم و جنگل تصادفی</p>\r\n</li>\r\n<li>\r\n<p>ماشین بردار پشتیبان (SVM)</p>\r\n</li>\r\n<li>\r\n<p>خوشه&zwnj;بندی با K-Means</p>\r\n</li>\r\n<li>\r\n<p>کاهش ابعاد با PCA</p>\r\n</li>\r\n<li>\r\n<p>ارزیابی مدل: cross-validation, confusion matrix</p>\r\n</li>\r\n</ul>\r\n<p><strong>تصویر ۱:</strong>&nbsp;نمودار مرز تصمیم یک مدل طبقه&zwnj;بندی<br><strong>تصویر ۲:</strong>&nbsp;ماتریس درهم&zwnj;ریختگی (Confusion Matrix) یک مدل</p>\r\n<p><strong>پیش&zwnj;نیازها:</strong></p>\r\n<ul>\r\n<li>\r\n<p>تسلط نسبی به پایتون (معادل دوره قبلی &laquo;پایتون برای هوش مصنوعی&raquo;)</p>\r\n</li>\r\n<li>\r\n<p>آشنایی با مبانی ریاضی (مشتق، احتمال)</p>\r\n</li>\r\n</ul>\r\n<p><strong>قالب دوره:</strong></p>\r\n<ul>\r\n<li>\r\n<p>۱۰ جلسه ۱.۵ ساعته</p>\r\n</li>\r\n<li>\r\n<p>سه&zwnj;شنبه&zwnj;ها ۱۷ تا ۱۹</p>\r\n</li>\r\n<li>\r\n<p>تدریس تئوری + پیاده&zwnj;سازی کد زنده + پروژه پایانی</p>\r\n</li>\r\n</ul>\r\n<p><strong>دستاورد نهایی:</strong></p>\r\n<p>شما یک متخصص یادگیری ماشین نمی&zwnj;شوید، اما مسیر را می&zwnj;شناسید و می&zwnj;توانید مسائل واقعی را با مدل&zwnj;های ML حل کنید.</p>', 'courses/428ade69deebe8217727e29fec7bbc48.jpg', 3500000, 1, 70.00, 10, 0, 90, NULL, '2026-07-20', '2026-08-30', NULL, 'published', 1, 5, '2026-06-19 06:48:52', '2026-06-21 20:48:14'),
(30, 14, 20, NULL, NULL, 'داده‌کاوی کاربردی', 'data-mining', '<p>داده&zwnj;های خام همه جا هستند، اما ارزش در الگوهای پنهان آنهاست. این دوره شما را با فرایند کامل داده&zwnj;کاوی آشنا می&zwnj;کند: از پاکسازی داده تا استخراج قوانین انجمنی و ساخت مدل&zwnj;های پیش&zwnj;بینی. از ابزارهای Python و Weka استفاده می&zwnj;کنیم.</p>\r\n<p><strong>فهرست مهارت&zwnj;های کسب&zwnj;شده:</strong></p>\r\n<ul>\r\n<li>\r\n<p>آشنایی با مراحل CRISP-DM</p>\r\n</li>\r\n<li>\r\n<p>پاکسازی و تبدیل داده&zwnj;ها</p>\r\n</li>\r\n<li>\r\n<p>تحلیل اکتشافی داده (EDA)</p>\r\n</li>\r\n<li>\r\n<p>قوانین انجمنی (Apriori)</p>\r\n</li>\r\n<li>\r\n<p>درخت تصمیم و Rule-Based Classifiers</p>\r\n</li>\r\n<li>\r\n<p>خوشه&zwnj;بندی (K-Means, DBSCAN)</p>\r\n</li>\r\n<li>\r\n<p>ارزیابی مدل&zwnj;ها با معیارهای مختلف</p>\r\n</li>\r\n</ul>\r\n<p><strong>تصویر ۱:</strong>&nbsp;گراف قوانین انجمنی استخراج&zwnj;شده از یک فروشگاه<br><strong>تصویر ۲:</strong>&nbsp;تفکیک خوشه&zwnj;ها در یک دیتاست</p>\r\n<p><strong>پیش&zwnj;نیازها:</strong></p>\r\n<ul>\r\n<li>\r\n<p>آشنایی با پایتون و کتابخانه Pandas (سطح مقدماتی)</p>\r\n</li>\r\n<li>\r\n<p>مبانی آمار توصیفی</p>\r\n</li>\r\n</ul>\r\n<p><strong>قالب دوره:</strong></p>\r\n<ul>\r\n<li>\r\n<p>۸ جلسه ۱.۵ ساعته</p>\r\n</li>\r\n<li>\r\n<p>پنج&zwnj;شنبه&zwnj;ها ۱۰ تا ۱۲</p>\r\n</li>\r\n<li>\r\n<p>کارگاه&zwnj;های عملی با دیتاست&zwnj;های واقعی</p>\r\n</li>\r\n</ul>\r\n<p><strong>دستاورد نهایی:</strong></p>\r\n<p>پس از این دوره، می&zwnj;توانید یک پروژه داده&zwnj;کاوی کامل را از جمع&zwnj;آوری داده تا ارائه نتایج اجرا کنید و بینش&zwnj;های ارزشمندی برای کسب&zwnj;وکارها استخراج نمایید.</p>', 'courses/9a65972ef5e8af7f58e74afe6f470771.jpg', 2200000, 1, 65.00, 8, 0, 90, NULL, '2026-08-01', '2026-09-10', NULL, 'published', 1, 6, '2026-06-19 06:48:52', '2026-06-21 20:49:14'),
(31, 35, 2, NULL, NULL, 'دوره تستیییی', 'دوره-تستیییی', '<p>لذذبلذسقذل دوره سبرسببطبز</p>', 'courses/7e308914fe60fdea68cf37acb54cf08b.jpg', 3500000, 1, 88.00, 10, 0, 90, NULL, NULL, NULL, '<p>شنبه ها و سه شنبه ها</p>', 'published', 1, 2, '2026-06-26 11:27:31', '2026-06-26 11:27:31');

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
(10, 29, 14, 'غللادبز', NULL, '2026-06-21 20:13:26'),
(11, 29, 14, 'ابزسزتی', NULL, '2026-06-21 20:13:31'),
(12, 29, 14, '—', 'chat/2411b997153cc3c7fb4efe1c2e6d77c5.png', '2026-06-21 20:13:38'),
(13, 25, 9, 'hi', NULL, '2026-06-22 02:25:20'),
(14, 25, 9, 'hello', NULL, '2026-06-22 05:07:07'),
(15, 25, 9, 'درود نگار جان', NULL, '2026-06-22 05:09:16'),
(16, 25, 14, 'درود دانشجوی عزیز', NULL, '2026-06-22 05:09:48'),
(17, 25, 14, 'این هم جزوه شما', 'chat/8010d32a7321ed07d76f47f02d53ef60.pdf', '2026-06-22 05:10:31');

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
(9, 25, 17, '2026-06-22 05:29:26'),
(14, 25, 17, '2026-06-22 05:10:31'),
(14, 29, 12, '2026-06-21 20:13:38');

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
(3, 25, 'نصب و راه‌اندازی XAMPP و معرفی PHP', 1, 'آشنایی با محیط توسعه، نوشتن اولین اسکریپت', NULL, NULL, NULL, 1, NULL, '2026-07-01 16:00:00', '2026-06-21 21:02:55', '2026-06-21 21:02:55'),
(4, 25, 'متغیرها، آرایه‌ها و توابع', 2, 'مبانی زبان PHP و ساختار داده‌ها', NULL, NULL, NULL, 2, NULL, '2026-07-05 16:00:00', '2026-06-21 21:02:55', '2026-06-21 21:02:55'),
(5, 25, 'کار با فرم‌ها و GET/POST', 3, 'دریافت داده از کاربر و اعتبارسنجی', NULL, NULL, NULL, 3, NULL, '2026-07-08 16:00:00', '2026-06-21 21:02:55', '2026-06-21 21:02:55'),
(6, 25, 'اتصال به MySQL با PDO', 4, 'ایجاد دیتابیس، کوئری‌های SELECT', NULL, NULL, NULL, 4, NULL, '2026-07-12 16:00:00', '2026-06-21 21:02:55', '2026-06-21 21:02:55'),
(7, 25, 'پروژه فروشگاه – بخش اول', 5, 'ایجاد جداول محصولات و کاربران', NULL, NULL, NULL, 5, NULL, '2026-07-15 16:00:00', '2026-06-21 21:02:55', '2026-06-21 21:02:55'),
(8, 25, 'پروژه فروشگاه – بخش دوم', 6, 'سبد خرید و نهایی‌سازی', NULL, NULL, NULL, 6, NULL, '2026-07-19 16:00:00', '2026-06-21 21:02:55', '2026-06-21 21:02:55'),
(9, 26, 'مبانی شبکه و مدل OSI', 1, 'لایه‌های شبکه، پروتکل‌ها', NULL, NULL, NULL, 1, NULL, '2026-07-03 10:00:00', '2026-06-21 21:02:55', '2026-06-21 21:02:55'),
(10, 26, 'IP Addressing و Subnetting', 2, 'محاسبه و تقسیم‌بندی', NULL, NULL, NULL, 2, NULL, '2026-07-07 10:00:00', '2026-06-21 21:02:55', '2026-06-21 21:02:55'),
(11, 26, 'پیکربندی سوئیچ و VLAN', 3, 'شبیه‌سازی در Packet Tracer', NULL, NULL, NULL, 3, NULL, '2026-07-10 10:00:00', '2026-06-21 21:02:55', '2026-06-21 21:02:55'),
(12, 26, 'مسیریابی و پروتکل OSPF', 4, 'تنظیم روترها', NULL, NULL, NULL, 4, NULL, '2026-07-14 10:00:00', '2026-06-21 21:02:55', '2026-06-21 21:02:55'),
(13, 27, 'مفاهیم پایه امنیت', 1, 'مثلث CIA، تهدیدات', NULL, NULL, NULL, 1, NULL, '2026-07-11 09:00:00', '2026-06-21 21:02:56', '2026-06-21 21:02:56'),
(14, 27, 'رمزنگاری و گواهی دیجیتال', 2, 'الگوریتم‌های متقارن و نامتقارن', NULL, NULL, NULL, 2, NULL, '2026-07-18 09:00:00', '2026-06-21 21:02:56', '2026-06-21 21:02:56'),
(15, 27, 'اسکن با Nmap', 3, 'شناسایی سرویس‌ها و پورت‌ها', NULL, NULL, NULL, 3, NULL, '2026-07-25 09:00:00', '2026-06-21 21:02:56', '2026-06-21 21:02:56'),
(16, 28, 'نصب پایتون و معرفی IDE', 1, 'کار با Jupyter Notebook', NULL, NULL, NULL, 1, NULL, '2026-07-06 14:00:00', '2026-06-21 21:02:56', '2026-06-21 21:02:56'),
(17, 28, 'NumPy و آرایه‌ها', 2, 'محاسبات علمی', NULL, NULL, NULL, 2, NULL, '2026-07-13 14:00:00', '2026-06-21 21:02:56', '2026-06-21 21:02:56'),
(18, 28, 'Pandas و تحلیل داده', 3, 'خواندن CSV، پاکسازی', NULL, NULL, NULL, 3, NULL, '2026-07-20 14:00:00', '2026-06-21 21:02:56', '2026-06-21 21:02:56'),
(19, 28, 'تصویرسازی با Matplotlib', 4, 'نمودارهای خطی، میله‌ای، پراکندگی', NULL, NULL, NULL, 4, NULL, '2026-07-27 14:00:00', '2026-06-21 21:02:56', '2026-06-21 21:02:56'),
(20, 29, 'مقدمه‌ای بر یادگیری ماشین', 1, 'انواع یادگیری، کتابخانه Scikit-Learn', NULL, NULL, NULL, 1, NULL, '2026-07-08 17:00:00', '2026-06-21 21:02:56', '2026-06-21 21:02:56'),
(21, 29, 'رگرسیون خطی', 2, 'پیاده‌سازی و ارزیابی', NULL, NULL, NULL, 2, NULL, '2026-07-15 17:00:00', '2026-06-21 21:02:56', '2026-06-21 21:02:56'),
(22, 29, 'طبقه‌بندی با درخت تصمیم', 3, 'Decision Tree و Random Forest', NULL, NULL, NULL, 3, NULL, '2026-07-22 17:00:00', '2026-06-21 21:02:56', '2026-06-21 21:02:56'),
(23, 29, 'خوشه‌بندی K-Means', 4, 'تحلیل و مصورسازی', NULL, NULL, NULL, 4, NULL, '2026-07-29 17:00:00', '2026-06-21 21:02:56', '2026-06-21 21:02:56'),
(24, 30, 'آشنایی با فرایند CRISP-DM', 1, 'مراحل داده‌کاوی', NULL, NULL, NULL, 1, NULL, '2026-08-03 10:00:00', '2026-06-21 21:02:56', '2026-06-21 21:02:56'),
(25, 30, 'پیش‌پردازش داده‌ها', 2, 'پاکسازی، تبدیل، نرمال‌سازی', NULL, NULL, NULL, 2, NULL, '2026-08-10 10:00:00', '2026-06-21 21:02:56', '2026-06-21 21:02:56'),
(26, 30, 'قوانین انجمنی Apriori', 3, 'تحلیل سبد خرید', NULL, NULL, NULL, 3, NULL, '2026-08-17 10:00:00', '2026-06-21 21:02:56', '2026-06-21 21:02:56'),
(27, 30, 'درخت تصمیم و ارزیابی مدل', 4, 'معیارهای دقت، صحت، فراخوانی', NULL, NULL, NULL, 4, NULL, '2026-08-24 10:00:00', '2026-06-21 21:02:56', '2026-06-21 21:02:56');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `status` enum('pending','active','completed','cancelled') NOT NULL DEFAULT 'pending',
  `final_grade` decimal(5,2) DEFAULT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `user_id`, `course_id`, `status`, `final_grade`, `enrolled_at`) VALUES
(10, 9, 25, 'active', NULL, '2026-06-19 09:11:19'),
(11, 9, 27, 'cancelled', NULL, '2026-06-19 09:11:29');

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
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(10) UNSIGNED NOT NULL,
  `enrollment_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `amount` decimal(12,0) NOT NULL,
  `gateway` varchar(50) NOT NULL DEFAULT 'zarinpal',
  `authority` varchar(100) DEFAULT NULL,
  `ref_id` varchar(100) DEFAULT NULL,
  `status` enum('pending','paid','failed','cancelled') NOT NULL DEFAULT 'pending',
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(2, 14, 'دکترای کامپیوتر دارم. و در دانشگاه شریف درس خوانده ام', 'در دانشگاه شریف درس میدهم', 'شبکه بلد هستم', 'teachers/resumes/3074a0c98e53e17caf86c12376870288.pdf', NULL, '2026-06-11 11:00:49', 1, '2026-06-11 11:00:07', '2026-06-11 11:00:49'),
(4, 35, 'لنعغذنعاععععععععععععععع', 'عذغغغغغغغغغغغغغغغغغغغغغغغغغ', 'دهتتتتتتتتتتتتتتتتتتتتتتتتتتتته', 'teachers/resumes/6dca0a8858548b68bb5153bc0a9077e6.docx', NULL, '2026-06-22 05:37:14', 1, '2026-06-22 05:36:32', '2026-06-22 05:37:14');

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
(34, 'student', '0311103708010', NULL, '$2y$10$XqhIYTfOcsZSjn5KDblRFuJZmmWE7ScYHxiCbpLsy/FrztCWmh1Du', 'محمد موسوی', '09120000019', NULL, 7, '0311103708010', NULL, '1234567810', 'none', 1, 1, '2026-06-19 07:41:25', '2026-06-19 07:41:25'),
(35, 'teacher', 'nr.ray.r@gmail.com', 'nr.ray.r@gmail.com', '$2y$10$R4LF/HJA.gm6XiKk3eq28eFgR27zDu5AXftc9NcY0pLzZkVGk9nau', 'Roma mills', '09369234656', 10, NULL, NULL, NULL, '2111232807', 'approved', 1, 1, '2026-06-22 05:36:31', '2026-06-22 05:37:14');

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `balance` decimal(12,0) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wallet_transactions`
--

CREATE TABLE `wallet_transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `amount` decimal(12,0) NOT NULL,
  `type` enum('deposit','withdraw','refund','teacher_earning','purchase') NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pay_enrollment` (`enrollment_id`),
  ADD KEY `fk_pay_user` (`user_id`);

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
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_wt_user` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `course_categories`
--
ALTER TABLE `course_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `course_messages`
--
ALTER TABLE `course_messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `course_sessions`
--
ALTER TABLE `course_sessions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `error_reports`
--
ALTER TABLE `error_reports`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `useful_links`
--
ALTER TABLE `useful_links`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

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
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_pay_enrollment` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pay_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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

--
-- Constraints for table `wallets`
--
ALTER TABLE `wallets`
  ADD CONSTRAINT `fk_wallet_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD CONSTRAINT `fk_wt_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
