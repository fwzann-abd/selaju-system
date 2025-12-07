/*
 Navicat Premium Data Transfer

 Source Server         : selaju dev
 Source Server Type    : MySQL
 Source Server Version : 110803 (11.8.3-MariaDB-log)
 Source Host           : 145.79.14.47:3306
 Source Schema         : u482064254_learn_test_db

 Target Server Type    : MySQL
 Target Server Version : 110803 (11.8.3-MariaDB-log)
 File Encoding         : 65001

 Date: 01/12/2025 06:46:19
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for article_categories
-- ----------------------------
DROP TABLE IF EXISTS `article_categories`;
CREATE TABLE `article_categories` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `row_order` int(11) DEFAULT NULL,
  `created_by` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` char(36) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `article_categories_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of article_categories
-- ----------------------------
BEGIN;
INSERT INTO `article_categories` (`id`, `name`, `slug`, `description`, `status`, `row_order`, `created_by`, `created_at`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES ('019a76c6-812f-7254-88ec-cae129899f09', 'Kegiatan', 'kegiatan', 'Artikel tentang kegiatan sekolah dan OSIS', 1, NULL, NULL, '2025-11-11 23:35:12', '2025-11-11 23:35:12', NULL, NULL, NULL);
INSERT INTO `article_categories` (`id`, `name`, `slug`, `description`, `status`, `row_order`, `created_by`, `created_at`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES ('019a76c6-8132-7115-8818-ac377575017b', 'Teknologi', 'teknologi', 'Artikel tentang teknologi dan inovasi digital', 1, NULL, NULL, '2025-11-11 23:35:12', '2025-11-11 23:35:12', NULL, NULL, NULL);
INSERT INTO `article_categories` (`id`, `name`, `slug`, `description`, `status`, `row_order`, `created_by`, `created_at`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES ('019a76c6-8134-713c-b146-b341bc679ea5', 'Alumni', 'alumni', 'Cerita dan artikel dari alumni SMKN 1 Garut', 1, NULL, NULL, '2025-11-11 23:35:12', '2025-11-11 23:35:12', NULL, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for articles
-- ----------------------------
DROP TABLE IF EXISTS `articles`;
CREATE TABLE `articles` (
  `id` char(36) NOT NULL,
  `category_id` char(36) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `excerpt` text DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` char(36) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `articles_slug_unique` (`slug`),
  KEY `articles_category_id_foreign` (`category_id`),
  CONSTRAINT `articles_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `article_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of articles
-- ----------------------------
BEGIN;
INSERT INTO `articles` (`id`, `category_id`, `title`, `slug`, `content`, `excerpt`, `featured_image`, `status`, `created_by`, `created_at`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES ('019a773c-dddd-73ed-b9b3-7b383accf920', '019a76c6-8134-713c-b146-b341bc679ea5', 'Coba', 'coba', 'Coba', NULL, NULL, 1, NULL, '2025-11-12 01:44:29', '2025-11-12 01:44:29', NULL, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for authors
-- ----------------------------
DROP TABLE IF EXISTS `authors`;
CREATE TABLE `authors` (
  `uuid` char(36) NOT NULL,
  `participant_id` char(36) NOT NULL,
  `author_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uuid`),
  KEY `authors_participant_id_foreign` (`participant_id`),
  CONSTRAINT `authors_participant_id_foreign` FOREIGN KEY (`participant_id`) REFERENCES `participants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of authors
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for books
-- ----------------------------
DROP TABLE IF EXISTS `books`;
CREATE TABLE `books` (
  `uuid` char(36) NOT NULL,
  `author_id` char(36) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `desc` text DEFAULT NULL,
  `language` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `color_hex` varchar(10) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uuid`),
  UNIQUE KEY `books_slug_unique` (`slug`),
  KEY `books_author_id_foreign` (`author_id`),
  CONSTRAINT `books_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `authors` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of books
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for books_categories_pivots
-- ----------------------------
DROP TABLE IF EXISTS `books_categories_pivots`;
CREATE TABLE `books_categories_pivots` (
  `uuid` char(36) NOT NULL,
  `category_id` char(36) NOT NULL,
  `book_id` char(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uuid`),
  KEY `books_categories_pivots_category_id_foreign` (`category_id`),
  KEY `books_categories_pivots_book_id_foreign` (`book_id`),
  CONSTRAINT `books_categories_pivots_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `books_categories_pivots_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `books_category` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of books_categories_pivots
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for books_category
-- ----------------------------
DROP TABLE IF EXISTS `books_category`;
CREATE TABLE `books_category` (
  `uuid` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of books_category
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for cache
-- ----------------------------
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of cache
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for cache_locks
-- ----------------------------
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of cache_locks
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of failed_jobs
-- ----------------------------
BEGIN;
INSERT INTO `failed_jobs` (`id`, `uuid`, `connection`, `queue`, `payload`, `exception`, `failed_at`) VALUES (1, '5ec1a9fe-7d23-44ab-b1c1-61a8d35bd07b', 'database', 'default', '{\"uuid\":\"5ec1a9fe-7d23-44ab-b1c1-61a8d35bd07b\",\"displayName\":\"App\\\\Events\\\\OrderStatusUpdated\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":17:{s:5:\\\"event\\\";O:29:\\\"App\\\\Events\\\\OrderStatusUpdated\\\":1:{s:5:\\\"order\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:23:\\\"App\\\\Models\\\\SejajanOrder\\\";s:2:\\\"id\\\";s:36:\\\"019ad413-465e-733b-8141-1b4d9f26687e\\\";s:9:\\\"relations\\\";a:4:{i:0;s:5:\\\"items\\\";i:1;s:13:\\\"items.product\\\";i:2;s:11:\\\"participant\\\";i:3;s:7:\\\"sejajan\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";b:1;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1764510587,\"delay\":null}', 'Illuminate\\Broadcasting\\BroadcastException: Pusher error: cURL error 7: Failed to connect to localhost port 8080 after 0 ms: Couldn\'t connect to server (see https://curl.haxx.se/libcurl/c/libcurl-errors.html) for http://localhost:8080/apps/609785/events?auth_key=pfbihuevjhdssqtm6nkv&auth_timestamp=1764510797&auth_version=1.0&body_md5=1c2814e536aad02f1b946cc78679097c&auth_signature=91e745cd628e7d6a44b9866ddedbfa63d4f902841c0ea6cab37e9f4be57e2591. in /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/laravel/framework/src/Illuminate/Broadcasting/Broadcasters/PusherBroadcaster.php:163\nStack trace:\n#0 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/laravel/framework/src/Illuminate/Broadcasting/BroadcastEvent.php(100): Illuminate\\Broadcasting\\Broadcasters\\PusherBroadcaster->broadcast()\n#1 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Broadcasting\\BroadcastEvent->handle()\n#2 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#3 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#4 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#5 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#6 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(129): Illuminate\\Container\\Container->call()\n#7 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Bus\\Dispatcher->Illuminate\\Bus\\{closure}()\n#8 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#9 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(133): Illuminate\\Pipeline\\Pipeline->then()\n#10 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(134): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#11 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Queue\\CallQueuedHandler->Illuminate\\Queue\\{closure}()\n#12 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#13 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(127): Illuminate\\Pipeline\\Pipeline->then()\n#14 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(68): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#15 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#16 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(451): Illuminate\\Queue\\Jobs\\Job->fire()\n#17 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(401): Illuminate\\Queue\\Worker->process()\n#18 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(344): Illuminate\\Queue\\Worker->runJob()\n#19 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(148): Illuminate\\Queue\\Worker->runNextJob()\n#20 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#21 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#22 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#23 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#24 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#25 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/laravel/framework/src/Illuminate/Container/Container.php(836): Illuminate\\Container\\BoundMethod::call()\n#26 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#27 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/symfony/console/Command/Command.php(318): Illuminate\\Console\\Command->execute()\n#28 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#29 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/symfony/console/Application.php(1073): Illuminate\\Console\\Command->run()\n#30 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/symfony/console/Application.php(356): Symfony\\Component\\Console\\Application->doRunCommand()\n#31 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/symfony/console/Application.php(195): Symfony\\Component\\Console\\Application->doRun()\n#32 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(197): Symfony\\Component\\Console\\Application->run()\n#33 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle()\n#34 /media/jarss/Data_Ubuntu/Projek/Laravel/selaju-system/artisan(16): Illuminate\\Foundation\\Application->handleCommand()\n#35 {main}', '2025-11-30 13:53:18');
COMMIT;

-- ----------------------------
-- Table structure for generations
-- ----------------------------
DROP TABLE IF EXISTS `generations`;
CREATE TABLE `generations` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `start_years` int(11) NOT NULL,
  `end_years` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of generations
-- ----------------------------
BEGIN;
INSERT INTO `generations` (`id`, `name`, `start_years`, `end_years`, `is_active`, `created_at`, `updated_at`) VALUES ('019a8fbc-e436-720a-857d-618d53b5fd7b', 'Angkatan 27', 2026, 2027, 0, '2025-11-17 02:55:12', '2025-11-17 02:55:31');
INSERT INTO `generations` (`id`, `name`, `start_years`, `end_years`, `is_active`, `created_at`, `updated_at`) VALUES ('19cbde56-0141-47e4-bf73-5e2d3c42058d', 'Angkatan 26', 2025, 2026, 1, '2025-11-17 02:27:12', '2025-11-17 02:55:31');
COMMIT;

-- ----------------------------
-- Table structure for job_batches
-- ----------------------------
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of job_batches
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for jobs
-- ----------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of jobs
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for menus
-- ----------------------------
DROP TABLE IF EXISTS `menus`;
CREATE TABLE `menus` (
  `id` char(36) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `row_order` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` char(36) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of menus
-- ----------------------------
BEGIN;
INSERT INTO `menus` (`id`, `code`, `name`, `icon`, `row_order`, `status`, `created_by`, `created_at`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES ('019a76c6-8141-7279-ab85-2271b0d3717d', 'dashboard', 'Dashboard', 'fa-home', 1, 1, NULL, '2025-11-11 23:35:12', '2025-11-12 00:52:13', NULL, NULL, NULL);
INSERT INTO `menus` (`id`, `code`, `name`, `icon`, `row_order`, `status`, `created_by`, `created_at`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES ('019a76c6-8143-73e6-9427-ab68d37107ea', 'konten', 'Konten', 'fa-newspaper', 2, 1, NULL, '2025-11-11 23:35:12', '2025-11-12 00:42:03', NULL, NULL, NULL);
INSERT INTO `menus` (`id`, `code`, `name`, `icon`, `row_order`, `status`, `created_by`, `created_at`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES ('019a76c6-8145-73b0-8a89-257385044ea8', 'pengaturan', 'Pengaturan', 'fa-cog', 3, 1, NULL, '2025-11-11 23:35:12', '2025-11-11 23:35:12', NULL, NULL, NULL);
INSERT INTO `menus` (`id`, `code`, `name`, `icon`, `row_order`, `status`, `created_by`, `created_at`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES ('019a7b21-65a9-71ef-892a-a8f530e33a7c', 'user-management', 'User Management', 'fa-users', 2, 1, NULL, '2025-11-13 02:52:57', '2025-11-13 02:53:20', NULL, NULL, NULL);
INSERT INTO `menus` (`id`, `code`, `name`, `icon`, `row_order`, `status`, `created_by`, `created_at`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES ('019a7b5b-3129-7317-a00e-f2086df625ec', 'participant', 'Peserta', 'fas fa-users', 1, 1, NULL, '2025-11-13 03:56:05', '2025-11-13 04:03:05', NULL, NULL, NULL);
INSERT INTO `menus` (`id`, `code`, `name`, `icon`, `row_order`, `status`, `created_by`, `created_at`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES ('019ad6ec-2194-73f6-8248-355f99c0e21b', 'sejajan', 'Sejajan', 'fa-store', 4, 1, NULL, '2025-11-30 22:39:50', '2025-11-30 22:39:50', NULL, NULL, NULL);
INSERT INTO `menus` (`id`, `code`, `name`, `icon`, `row_order`, `status`, `created_by`, `created_at`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES ('019ad6ec-22af-738e-9a64-15e025545f82', 'sekolah', 'Sekolah', 'fa-school', 5, 1, NULL, '2025-11-30 22:39:50', '2025-11-30 22:39:50', NULL, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of migrations
-- ----------------------------
BEGIN;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23, '0001_01_01_000000_create_users_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24, '0001_01_01_000001_create_cache_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25, '0001_01_01_000002_create_jobs_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26, '2024_11_12_000000_add_user_group_id_to_users_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27, '2024_11_20_000000_create_user_groups_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28, '2024_11_20_000100_create_user_group_permissions_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29, '2024_11_20_000200_create_menus_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30, '2024_11_20_000300_create_modules_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31, '2024_11_20_000400_create_module_accesses_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32, '2024_11_20_000500_create_article_categories_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34, '2024_11_20_000600_create_articles_table', 2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35, '2025_11_12_064949_modify_users_table_to_uuid', 3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36, '2025_11_12_070906_remove_uuid_from_users_and_sessions', 4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37, '2025_11_12_000001_add_uuid_to_users_table', 5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38, '2025_11_12_000002_update_module_urls', 5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40, '2025_11_13_030054_create_schools_table', 6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41, '2025_11_13_035438_create_participants_table', 7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42, '2025_11_13_120000_add_api_token_to_participants_table', 8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43, '2025_11_13_130000_drop_api_token_from_participants_table', 9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44, '2025_11_13_140000_create_personal_access_tokens_table', 10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45, '2025_11_13_150000_modify_personal_access_tokens_tokenable_id_to_string', 11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46, '2025_11_13_160000_add_id_to_participants_table', 12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47, '2025_11_13_171000_convert_participants_uuid_to_id', 13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48, '2025_11_14_120000_add_email_verified_at_to_participants_table', 14);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49, '2025_11_14_065519_create_sejajans_table', 15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50, '2025_11_14_065520_create_sejajan_products_table', 15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (51, '2025_11_14_065521_create_sejajan_orders_table', 15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (52, '2025_11_14_065522_create_sejajan_order_items_table', 16);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (57, '2025_11_17_020946_create_generations_table', 17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (58, '2025_11_17_021003_add_generation_id_to_participants_table', 18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (59, '2025_11_17_022526_populate_generation_id_for_existing_participants', 18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (60, '2025_11_17_022550_make_generation_id_not_nullable_on_participants', 18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (61, '2025_11_23_011919_create_books_category_table', 19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (62, '2025_11_23_012018_create_authors_table', 19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (63, '2025_11_23_012042_create_books_table', 19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (64, '2025_11_23_012210_create_books_categories_pivots_table', 19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (65, '2025_11_24_143036_create_sejajan_categories_table', 19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (66, '2025_11_24_143149_add_category_id_to_sejajan_products_table', 19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (67, '2025_11_29_000300_add_location_pickup_to_sejajan_orders_table', 19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (68, '2025_11_29_020000_create_sejajan_cart_items_table', 19);
COMMIT;

-- ----------------------------
-- Table structure for modules
-- ----------------------------
DROP TABLE IF EXISTS `modules`;
CREATE TABLE `modules` (
  `id` char(36) NOT NULL,
  `menu_id` char(36) DEFAULT NULL,
  `identifiers` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `row_order` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `modules_identifiers_unique` (`identifiers`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of modules
-- ----------------------------
BEGIN;
INSERT INTO `modules` (`id`, `menu_id`, `identifiers`, `name`, `url`, `icon`, `row_order`, `is_active`, `created_by`, `created_at`, `updated_at`, `updated_by`) VALUES ('019a76c6-8149-721d-8c18-de4142fc916e', '019a76c6-8141-7279-ab85-2271b0d3717d', 'dashboard-main', 'Dashboard Utama', '/admin/dashboard', 'fa-home', 1, 1, NULL, '2025-11-11 23:35:12', '2025-11-12 00:20:52', NULL);
INSERT INTO `modules` (`id`, `menu_id`, `identifiers`, `name`, `url`, `icon`, `row_order`, `is_active`, `created_by`, `created_at`, `updated_at`, `updated_by`) VALUES ('019a76c6-814d-70c8-bcdd-cde58e02dcce', '019a76c6-8143-73e6-9427-ab68d37107ea', 'artikel-list', 'Artikel', '/admin/articles', 'fa-newspaper', 2, 1, NULL, '2025-11-11 23:35:12', '2025-11-12 00:47:15', NULL);
INSERT INTO `modules` (`id`, `menu_id`, `identifiers`, `name`, `url`, `icon`, `row_order`, `is_active`, `created_by`, `created_at`, `updated_at`, `updated_by`) VALUES ('019a76c6-814f-7042-adb3-ee5a2718a0b0', '019a76c6-8143-73e6-9427-ab68d37107ea', 'kategori-artikel', 'Kategori Artikel', '/admin/article-categories', 'fa-folder', 1, 1, NULL, '2025-11-11 23:35:12', '2025-11-12 00:47:22', NULL);
INSERT INTO `modules` (`id`, `menu_id`, `identifiers`, `name`, `url`, `icon`, `row_order`, `is_active`, `created_by`, `created_at`, `updated_at`, `updated_by`) VALUES ('019a76c6-8151-73a6-9cab-3b5f8f0a26c6', '019a7b21-65a9-71ef-892a-a8f530e33a7c', 'user-management', 'Users', '/admin/users', 'fa-users', 1, 1, NULL, '2025-11-11 23:35:12', '2025-11-13 02:53:30', NULL);
INSERT INTO `modules` (`id`, `menu_id`, `identifiers`, `name`, `url`, `icon`, `row_order`, `is_active`, `created_by`, `created_at`, `updated_at`, `updated_by`) VALUES ('019a76c6-8153-71fe-bbb9-b42e0c0044bd', '019a76c6-8145-73b0-8a89-257385044ea8', 'menu-management', 'Menus', '/admin/menus', 'fa-bars', 2, 1, NULL, '2025-11-11 23:35:12', '2025-11-13 02:50:57', NULL);
INSERT INTO `modules` (`id`, `menu_id`, `identifiers`, `name`, `url`, `icon`, `row_order`, `is_active`, `created_by`, `created_at`, `updated_at`, `updated_by`) VALUES ('019a76c6-8154-72f2-bf9d-78331d1891ea', '019a76c6-8145-73b0-8a89-257385044ea8', 'module-management', 'Modules', '/admin/modules', 'fa-cubes', 3, 1, NULL, '2025-11-11 23:35:12', '2025-11-13 02:51:08', NULL);
INSERT INTO `modules` (`id`, `menu_id`, `identifiers`, `name`, `url`, `icon`, `row_order`, `is_active`, `created_by`, `created_at`, `updated_at`, `updated_by`) VALUES ('019a76ed-1c68-737a-a7a1-4be48b47922b', '019a7b21-65a9-71ef-892a-a8f530e33a7c', 'user-group', 'User Group', '/admin/user-groups', 'fas fa-users', 1, 1, NULL, '2025-11-12 00:17:22', '2025-11-13 02:53:47', NULL);
INSERT INTO `modules` (`id`, `menu_id`, `identifiers`, `name`, `url`, `icon`, `row_order`, `is_active`, `created_by`, `created_at`, `updated_at`, `updated_by`) VALUES ('019a7b1e-dd58-725c-977f-f070de999fca', '019a76c6-8145-73b0-8a89-257385044ea8', 'schools', 'School', '/admin/schools', NULL, 1, 1, NULL, '2025-11-13 02:50:11', '2025-11-13 02:50:11', NULL);
INSERT INTO `modules` (`id`, `menu_id`, `identifiers`, `name`, `url`, `icon`, `row_order`, `is_active`, `created_by`, `created_at`, `updated_at`, `updated_by`) VALUES ('019a7b62-0d77-7226-8d78-4e8248c4ddce', '019a7b5b-3129-7317-a00e-f2086df625ec', 'participant-management', 'Peserta', '/admin/participants', 'fa-users', 1, 1, NULL, '2025-11-13 04:03:34', '2025-11-17 02:04:58', NULL);
INSERT INTO `modules` (`id`, `menu_id`, `identifiers`, `name`, `url`, `icon`, `row_order`, `is_active`, `created_by`, `created_at`, `updated_at`, `updated_by`) VALUES ('019a8f91-8f90-7135-b9c2-85ff5ac00f62', '019a76c6-8145-73b0-8a89-257385044ea8', 'generations', 'Generasi', '/admin/generations', 'fa-folder', 1, 1, NULL, '2025-11-17 02:07:52', '2025-11-17 02:07:52', NULL);
COMMIT;

-- ----------------------------
-- Table structure for modules_access
-- ----------------------------
DROP TABLE IF EXISTS `modules_access`;
CREATE TABLE `modules_access` (
  `id` char(36) NOT NULL,
  `module_id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'action',
  `identifiers` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `module_access_identifiers_unique` (`module_id`,`identifiers`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of modules_access
-- ----------------------------
BEGIN;
INSERT INTO `modules_access` (`id`, `module_id`, `type`, `identifiers`, `name`, `created_at`, `updated_at`) VALUES ('019a76c6-8160-72a2-9ff9-6b8c2005388d', '019a76c6-8149-721d-8c18-de4142fc916e', 'action', 'dashboard-main-view', 'View Dashboard Utama', '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `modules_access` (`id`, `module_id`, `type`, `identifiers`, `name`, `created_at`, `updated_at`) VALUES ('019a76c6-8162-737f-a868-ccd95894e82f', '019a76c6-8149-721d-8c18-de4142fc916e', 'action', 'dashboard-main-create', 'Create Dashboard Utama', '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `modules_access` (`id`, `module_id`, `type`, `identifiers`, `name`, `created_at`, `updated_at`) VALUES ('019a76c6-8164-7019-80e1-c8dd73af0d10', '019a76c6-8149-721d-8c18-de4142fc916e', 'action', 'dashboard-main-edit', 'Edit Dashboard Utama', '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `modules_access` (`id`, `module_id`, `type`, `identifiers`, `name`, `created_at`, `updated_at`) VALUES ('019a76c6-8166-7395-be05-9c6dade8dd24', '019a76c6-8149-721d-8c18-de4142fc916e', 'action', 'dashboard-main-delete', 'Delete Dashboard Utama', '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `modules_access` (`id`, `module_id`, `type`, `identifiers`, `name`, `created_at`, `updated_at`) VALUES ('019a76c6-8168-7292-91fe-4bf5ecdceb93', '019a76c6-814c-71b3-8cf3-4473f252483c', 'action', 'dashboard-analytics-view', 'View Analytics', '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `modules_access` (`id`, `module_id`, `type`, `identifiers`, `name`, `created_at`, `updated_at`) VALUES ('019a76c6-8169-7373-9de3-39df9f5e4a39', '019a76c6-814c-71b3-8cf3-4473f252483c', 'action', 'dashboard-analytics-create', 'Create Analytics', '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `modules_access` (`id`, `module_id`, `type`, `identifiers`, `name`, `created_at`, `updated_at`) VALUES ('019a76c6-816b-73cf-92a8-602dc49a5bf6', '019a76c6-814c-71b3-8cf3-4473f252483c', 'action', 'dashboard-analytics-edit', 'Edit Analytics', '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `modules_access` (`id`, `module_id`, `type`, `identifiers`, `name`, `created_at`, `updated_at`) VALUES ('019a76c6-816d-7088-bfcc-0d121b7be0d7', '019a76c6-814c-71b3-8cf3-4473f252483c', 'action', 'dashboard-analytics-delete', 'Delete Analytics', '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `modules_access` (`id`, `module_id`, `type`, `identifiers`, `name`, `created_at`, `updated_at`) VALUES ('019a76c6-816e-7139-bb30-4106ba15022e', '019a76c6-814d-70c8-bcdd-cde58e02dcce', 'action', 'artikel-list-view', 'View Artikel', '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `modules_access` (`id`, `module_id`, `type`, `identifiers`, `name`, `created_at`, `updated_at`) VALUES ('019a76c6-8170-7220-82cd-fe1b39f95810', '019a76c6-814d-70c8-bcdd-cde58e02dcce', 'action', 'artikel-list-create', 'Create Artikel', '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `modules_access` (`id`, `module_id`, `type`, `identifiers`, `name`, `created_at`, `updated_at`) VALUES ('019a76c6-8172-738d-972e-98f42961961f', '019a76c6-814d-70c8-bcdd-cde58e02dcce', 'action', 'artikel-list-edit', 'Edit Artikel', '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `modules_access` (`id`, `module_id`, `type`, `identifiers`, `name`, `created_at`, `updated_at`) VALUES ('019a76c6-8173-7276-b6c7-df8a3f5d3115', '019a76c6-814d-70c8-bcdd-cde58e02dcce', 'action', 'artikel-list-delete', 'Delete Artikel', '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `modules_access` (`id`, `module_id`, `type`, `identifiers`, `name`, `created_at`, `updated_at`) VALUES ('019a76c6-8176-7215-abdd-8b3fa8aace35', '019a76c6-814f-7042-adb3-ee5a2718a0b0', 'action', 'kategori-artikel-view', 'View Kategori Artikel', '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `modules_access` (`id`, `module_id`, `type`, `identifiers`, `name`, `created_at`, `updated_at`) VALUES ('019a76c6-8178-7201-bad6-f72729848bb1', '019a76c6-814f-7042-adb3-ee5a2718a0b0', 'action', 'kategori-artikel-create', 'Create Kategori Artikel', '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `modules_access` (`id`, `module_id`, `type`, `identifiers`, `name`, `created_at`, `updated_at`) VALUES ('019a76c6-817a-73de-874a-a8a20890f956', '019a76c6-814f-7042-adb3-ee5a2718a0b0', 'action', 'kategori-artikel-edit', 'Edit Kategori Artikel', '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `modules_access` (`id`, `module_id`, `type`, `identifiers`, `name`, `created_at`, `updated_at`) VALUES ('019a76c6-817b-7055-aef2-46354c8ac810', '019a76c6-814f-7042-adb3-ee5a2718a0b0', 'action', 'kategori-artikel-delete', 'Delete Kategori Artikel', '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `modules_access` (`id`, `module_id`, `type`, `identifiers`, `name`, `created_at`, `updated_at`) VALUES ('019a76c6-817d-710e-b6ec-9dd5d1a06018', '019a76c6-8151-73a6-9cab-3b5f8f0a26c6', 'action', 'user-management-view', 'View Manajemen Pengguna', '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `modules_access` (`id`, `module_id`, `type`, `identifiers`, `name`, `created_at`, `updated_at`) VALUES ('019a76c6-817f-7209-ac8e-9600e28e21de', '019a76c6-8151-73a6-9cab-3b5f8f0a26c6', 'action', 'user-management-create', 'Create Manajemen Pengguna', '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `modules_access` (`id`, `module_id`, `type`, `identifiers`, `name`, `created_at`, `updated_at`) VALUES ('019a76c6-8181-72d8-a8e7-727112c14ed5', '019a76c6-8151-73a6-9cab-3b5f8f0a26c6', 'action', 'user-management-edit', 'Edit Manajemen Pengguna', '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `modules_access` (`id`, `module_id`, `type`, `identifiers`, `name`, `created_at`, `updated_at`) VALUES ('019a76c6-8183-731d-9157-030dcd04424d', '019a76c6-8151-73a6-9cab-3b5f8f0a26c6', 'action', 'user-management-delete', 'Delete Manajemen Pengguna', '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `modules_access` (`id`, `module_id`, `type`, `identifiers`, `name`, `created_at`, `updated_at`) VALUES ('019a76c6-8184-724c-b022-0e2fc17a1f24', '019a76c6-8153-71fe-bbb9-b42e0c0044bd', 'action', 'menu-management-view', 'View Manajemen Menu', '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `modules_access` (`id`, `module_id`, `type`, `identifiers`, `name`, `created_at`, `updated_at`) VALUES ('019a76c6-8186-72de-9adf-d930d05be239', '019a76c6-8153-71fe-bbb9-b42e0c0044bd', 'action', 'menu-management-create', 'Create Manajemen Menu', '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `modules_access` (`id`, `module_id`, `type`, `identifiers`, `name`, `created_at`, `updated_at`) VALUES ('019a76c6-8187-7030-934d-89af45b388b0', '019a76c6-8153-71fe-bbb9-b42e0c0044bd', 'action', 'menu-management-edit', 'Edit Manajemen Menu', '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `modules_access` (`id`, `module_id`, `type`, `identifiers`, `name`, `created_at`, `updated_at`) VALUES ('019a76c6-8189-73c6-8ead-3c94b04661cd', '019a76c6-8153-71fe-bbb9-b42e0c0044bd', 'action', 'menu-management-delete', 'Delete Manajemen Menu', '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `modules_access` (`id`, `module_id`, `type`, `identifiers`, `name`, `created_at`, `updated_at`) VALUES ('019a76c6-818b-70bb-bd8f-62c01ad877a6', '019a76c6-8154-72f2-bf9d-78331d1891ea', 'action', 'module-management-view', 'View Manajemen Module', '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `modules_access` (`id`, `module_id`, `type`, `identifiers`, `name`, `created_at`, `updated_at`) VALUES ('019a76c6-818d-7178-a8bb-feecbfedb1cf', '019a76c6-8154-72f2-bf9d-78331d1891ea', 'action', 'module-management-create', 'Create Manajemen Module', '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `modules_access` (`id`, `module_id`, `type`, `identifiers`, `name`, `created_at`, `updated_at`) VALUES ('019a76c6-818e-72b8-a87b-ab70592384e6', '019a76c6-8154-72f2-bf9d-78331d1891ea', 'action', 'module-management-edit', 'Edit Manajemen Module', '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `modules_access` (`id`, `module_id`, `type`, `identifiers`, `name`, `created_at`, `updated_at`) VALUES ('019a76c6-8191-733f-a593-894416d7c959', '019a76c6-8154-72f2-bf9d-78331d1891ea', 'action', 'module-management-delete', 'Delete Manajemen Module', '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `modules_access` (`id`, `module_id`, `type`, `identifiers`, `name`, `created_at`, `updated_at`) VALUES ('03b3a080-25c7-4c6f-981d-239f17fb3b17', '019a7b62-0d77-7226-8d78-4e8248c4ddce', 'action', 'participant-management-edit', 'Edit Peserta', '2025-11-13 04:11:53', '2025-11-13 04:11:53');
INSERT INTO `modules_access` (`id`, `module_id`, `type`, `identifiers`, `name`, `created_at`, `updated_at`) VALUES ('58578fda-1047-46ec-b85e-e28822dd3f5b', '019a7b62-0d77-7226-8d78-4e8248c4ddce', 'action', 'participant-management-view', 'View Peserta', '2025-11-13 04:11:52', '2025-11-13 04:11:52');
INSERT INTO `modules_access` (`id`, `module_id`, `type`, `identifiers`, `name`, `created_at`, `updated_at`) VALUES ('95ed6af6-c85a-445b-a443-898c487fdf41', '019a7b62-0d77-7226-8d78-4e8248c4ddce', 'action', 'participant-management-delete', 'Delete Peserta', '2025-11-13 04:11:53', '2025-11-13 04:11:53');
INSERT INTO `modules_access` (`id`, `module_id`, `type`, `identifiers`, `name`, `created_at`, `updated_at`) VALUES ('c382aa23-a29a-4b67-af8e-e77bc458c283', '019a7b62-0d77-7226-8d78-4e8248c4ddce', 'action', 'participant-management-create', 'Create Peserta', '2025-11-13 04:11:53', '2025-11-13 04:11:53');
COMMIT;

-- ----------------------------
-- Table structure for participants
-- ----------------------------
DROP TABLE IF EXISTS `participants`;
CREATE TABLE `participants` (
  `id` varchar(36) NOT NULL,
  `nomor_participant` varchar(255) DEFAULT NULL,
  `school_id` char(36) DEFAULT NULL,
  `generation_id` char(36) NOT NULL,
  `username` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `birth_date` date DEFAULT NULL,
  `no_telp` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `participants_username_unique` (`username`),
  UNIQUE KEY `participants_email_unique` (`email`),
  UNIQUE KEY `participants_nomor_participant_unique` (`nomor_participant`),
  KEY `participants_school_id_foreign` (`school_id`),
  KEY `participants_generation_id_foreign` (`generation_id`),
  CONSTRAINT `participants_generation_id_foreign` FOREIGN KEY (`generation_id`) REFERENCES `generations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `participants_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of participants
-- ----------------------------
BEGIN;
INSERT INTO `participants` (`id`, `nomor_participant`, `school_id`, `generation_id`, `username`, `name`, `birth_date`, `no_telp`, `email`, `email_verified_at`, `photo`, `password`, `is_active`, `created_at`, `updated_at`) VALUES ('019a8fc2-5dff-70d5-96f8-b80980a18cc1', '82268909', '019a7bd0-17d1-70d3-bede-9ee7e348d27b', '19cbde56-0141-47e4-bf73-5e2d3c42058d', 'jarss_pajar', 'AGUNG PAJAR', NULL, '833146422729', 'presetpjr@gmail.com', '2025-11-30 08:25:09', NULL, '$2y$12$g.N0YSQ.r2ubw5PCppEauOwJfp7OfDXwj87VlNqjTCDtgxvAzzYhq', 1, '2025-11-17 03:01:11', '2025-11-30 08:25:09');
INSERT INTO `participants` (`id`, `nomor_participant`, `school_id`, `generation_id`, `username`, `name`, `birth_date`, `no_telp`, `email`, `email_verified_at`, `photo`, `password`, `is_active`, `created_at`, `updated_at`) VALUES ('019ad507-9482-70bd-9f45-da49eaea2d09', '91999497', '019a7bd0-17d1-70d3-bede-9ee7e348d27b', '19cbde56-0141-47e4-bf73-5e2d3c42058d', 'agung9935', 'Agung Pajar', NULL, '83146422729', 'agungpajar@gmail.com', NULL, NULL, '$2y$12$fEwhhcVTI93BKxVWXPjEiucC2z3duDpJzzdcqwgdIXVqqpbOHYRum', 1, '2025-11-30 13:50:35', '2025-11-30 13:50:37');
COMMIT;

-- ----------------------------
-- Table structure for password_reset_tokens
-- ----------------------------
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of password_reset_tokens
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for personal_access_tokens
-- ----------------------------
DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` varchar(191) NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of personal_access_tokens
-- ----------------------------
BEGIN;
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES (1, 'App\\Models\\Participant', '019a7c3d-c98d-7236-9ac1-19d006873d0b', 'default', '4a175348ccb050b3d143d10a67d3b6de5c70685942a70c0c93da8965592a4c40', '[\"*\"]', '2025-11-13 08:03:35', NULL, '2025-11-13 08:03:35', '2025-11-13 08:03:35');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES (3, 'App\\Models\\Participant', '019a7c41-6870-72b3-bc22-b500eeebb61b', 'default', '8bef97c46a2f4f2b9278eab8d1140e0f2c6400e6accd5203541243a2f576167f', '[\"*\"]', NULL, NULL, '2025-11-13 08:07:32', '2025-11-13 08:07:32');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES (10, 'App\\Models\\Participant', '019a7c7d-4b22-7021-baa1-f28bdfb99f4e', 'default', '5ce17b53396cedbac6b2dd21ec437e87fd353a1fe08de553bb5f795783698c58', '[\"*\"]', '2025-11-14 03:08:27', NULL, '2025-11-13 09:12:57', '2025-11-14 03:08:27');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES (12, 'App\\Models\\Participant', '019a8fc0-f66e-7200-8917-726024c13e3e', 'default', 'a527024a72933c3c1faa1530839bf0f9360fe5e546d1a8de57869fe24066ec1e', '[\"*\"]', NULL, NULL, '2025-11-17 02:59:39', '2025-11-17 02:59:39');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES (14, 'App\\Models\\Participant', '019a8fc2-5dff-70d5-96f8-b80980a18cc1', 'default', 'd283d565629a4188a79c034ca0cf38fb850b3b1353d4d261ed78e321cecdeb1d', '[\"*\"]', '2025-11-30 14:19:02', NULL, '2025-11-30 08:19:54', '2025-11-30 14:19:02');
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES (16, 'App\\Models\\Participant', '019ad507-9482-70bd-9f45-da49eaea2d09', 'default', '7563bbfc19e58591013dc122d02559aab14276d7c6f8b59436b2a4c0f2934628', '[\"*\"]', '2025-11-30 14:16:18', NULL, '2025-11-30 13:54:03', '2025-11-30 14:16:18');
COMMIT;

-- ----------------------------
-- Table structure for schools
-- ----------------------------
DROP TABLE IF EXISTS `schools`;
CREATE TABLE `schools` (
  `id` char(36) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `schools_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of schools
-- ----------------------------
BEGIN;
INSERT INTO `schools` (`id`, `slug`, `name`, `created_at`, `updated_at`) VALUES ('019a7bd0-17d1-70d3-bede-9ee7e348d27b', 'smk-negeri-1-garut', 'SMK Negeri 1 Garut', '2025-11-13 06:03:46', '2025-11-13 06:03:46');
INSERT INTO `schools` (`id`, `slug`, `name`, `created_at`, `updated_at`) VALUES ('01c614d0-314e-4127-bfd5-e36da0a95c0d', 'sma-swasta-bina-nusantara', 'SMA Swasta Bina Nusantara', '2025-11-13 06:11:23', '2025-11-13 06:11:23');
INSERT INTO `schools` (`id`, `slug`, `name`, `created_at`, `updated_at`) VALUES ('2236907c-5969-415e-80e6-c3c522e8ca5d', 'sma-negeri-1-jakarta', 'SMA Negeri 1 Jakarta', '2025-11-13 06:11:23', '2025-11-13 06:11:23');
INSERT INTO `schools` (`id`, `slug`, `name`, `created_at`, `updated_at`) VALUES ('ac0dbb82-7a11-4055-b93f-d56e28838437', 'sma-negeri-2-jakarta', 'SMA Negeri 2 Jakarta', '2025-11-13 06:11:23', '2025-11-13 06:11:23');
INSERT INTO `schools` (`id`, `slug`, `name`, `created_at`, `updated_at`) VALUES ('c6abf250-103c-4b5c-a213-06266b67bf09', 'sma-swasta-al-azhar', 'SMA Swasta Al-Azhar', '2025-11-13 06:11:23', '2025-11-13 06:11:23');
COMMIT;

-- ----------------------------
-- Table structure for sejajan_cart_items
-- ----------------------------
DROP TABLE IF EXISTS `sejajan_cart_items`;
CREATE TABLE `sejajan_cart_items` (
  `id` char(36) NOT NULL,
  `participant_id` char(36) NOT NULL,
  `sejajan_id` char(36) NOT NULL,
  `sejajan_product_id` char(36) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sejajan_cart_items_participant_id_sejajan_product_id_unique` (`participant_id`,`sejajan_product_id`),
  KEY `sejajan_cart_items_participant_id_index` (`participant_id`),
  KEY `sejajan_cart_items_sejajan_id_index` (`sejajan_id`),
  KEY `sejajan_cart_items_sejajan_product_id_index` (`sejajan_product_id`),
  CONSTRAINT `sejajan_cart_items_participant_id_foreign` FOREIGN KEY (`participant_id`) REFERENCES `participants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sejajan_cart_items_sejajan_id_foreign` FOREIGN KEY (`sejajan_id`) REFERENCES `sejajans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sejajan_cart_items_sejajan_product_id_foreign` FOREIGN KEY (`sejajan_product_id`) REFERENCES `sejajan_products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of sejajan_cart_items
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for sejajan_categories
-- ----------------------------
DROP TABLE IF EXISTS `sejajan_categories`;
CREATE TABLE `sejajan_categories` (
  `id` char(36) NOT NULL,
  `sejajan_id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sejajan_categories_sejajan_id_slug_unique` (`sejajan_id`,`slug`),
  KEY `sejajan_categories_sejajan_id_index` (`sejajan_id`),
  CONSTRAINT `sejajan_categories_sejajan_id_foreign` FOREIGN KEY (`sejajan_id`) REFERENCES `sejajans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of sejajan_categories
-- ----------------------------
BEGIN;
INSERT INTO `sejajan_categories` (`id`, `sejajan_id`, `name`, `slug`, `description`, `order`, `is_active`, `created_at`, `updated_at`) VALUES ('019ad3f4-b2a1-73a0-bed2-269aa0825346', '019ad3e2-4722-707f-b8a4-d820e8cd9dfe', 'Minuman', 'minuman', NULL, 0, 1, '2025-11-30 08:50:20', '2025-11-30 08:50:20');
COMMIT;

-- ----------------------------
-- Table structure for sejajan_order_items
-- ----------------------------
DROP TABLE IF EXISTS `sejajan_order_items`;
CREATE TABLE `sejajan_order_items` (
  `id` char(36) NOT NULL,
  `sejajan_order_id` char(36) NOT NULL,
  `sejajan_product_id` char(36) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `price` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sejajan_order_items_sejajan_order_id_index` (`sejajan_order_id`),
  KEY `sejajan_order_items_sejajan_product_id_index` (`sejajan_product_id`),
  CONSTRAINT `sejajan_order_items_sejajan_order_id_foreign` FOREIGN KEY (`sejajan_order_id`) REFERENCES `sejajan_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sejajan_order_items_sejajan_product_id_foreign` FOREIGN KEY (`sejajan_product_id`) REFERENCES `sejajan_products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of sejajan_order_items
-- ----------------------------
BEGIN;
INSERT INTO `sejajan_order_items` (`id`, `sejajan_order_id`, `sejajan_product_id`, `qty`, `price`, `subtotal`, `created_at`, `updated_at`) VALUES ('019ad404-3640-7093-9461-ae91991a0a8b', '019ad404-3627-71b3-9b08-9bebc6fc9023', '019ad3f5-0770-7207-869e-8223556c1acb', 1, 5000.00, 5000.00, '2025-11-30 09:07:17', '2025-11-30 09:07:17');
INSERT INTO `sejajan_order_items` (`id`, `sejajan_order_id`, `sejajan_product_id`, `qty`, `price`, `subtotal`, `created_at`, `updated_at`) VALUES ('019ad413-466f-7103-a18b-10db1eafaa19', '019ad413-465e-733b-8141-1b4d9f26687e', '019ad3f5-0770-7207-869e-8223556c1acb', 1, 5000.00, 5000.00, '2025-11-30 09:23:44', '2025-11-30 09:23:44');
INSERT INTO `sejajan_order_items` (`id`, `sejajan_order_id`, `sejajan_product_id`, `qty`, `price`, `subtotal`, `created_at`, `updated_at`) VALUES ('019ad50c-659f-72e2-86a7-84c96b9b8d08', '019ad50c-6538-717d-84e0-de6439008699', '019ad3f5-0770-7207-869e-8223556c1acb', 2, 5000.00, 10000.00, '2025-11-30 13:55:50', '2025-11-30 13:55:50');
INSERT INTO `sejajan_order_items` (`id`, `sejajan_order_id`, `sejajan_product_id`, `qty`, `price`, `subtotal`, `created_at`, `updated_at`) VALUES ('019ad50d-b121-72c6-8aba-4466deebc3d3', '019ad50d-b0d3-7045-8a2b-64d18438f2fa', '019ad3f5-0770-7207-869e-8223556c1acb', 2, 5000.00, 10000.00, '2025-11-30 13:57:15', '2025-11-30 13:57:15');
INSERT INTO `sejajan_order_items` (`id`, `sejajan_order_id`, `sejajan_product_id`, `qty`, `price`, `subtotal`, `created_at`, `updated_at`) VALUES ('019ad510-49b2-711d-a026-10c860a0ebe7', '019ad510-4962-726b-a24e-48649a17e73e', '019ad3f5-0770-7207-869e-8223556c1acb', 3, 5000.00, 15000.00, '2025-11-30 14:00:05', '2025-11-30 14:00:05');
INSERT INTO `sejajan_order_items` (`id`, `sejajan_order_id`, `sejajan_product_id`, `qty`, `price`, `subtotal`, `created_at`, `updated_at`) VALUES ('019ad51b-76f6-71d6-a6e6-98cec139de22', '019ad51b-769b-7312-a8a4-fdf0b414f293', '019ad3f5-0770-7207-869e-8223556c1acb', 1, 5000.00, 5000.00, '2025-11-30 14:12:18', '2025-11-30 14:12:18');
INSERT INTO `sejajan_order_items` (`id`, `sejajan_order_id`, `sejajan_product_id`, `qty`, `price`, `subtotal`, `created_at`, `updated_at`) VALUES ('019ad51c-54d4-73d7-b3a9-7529d3fcb3c3', '019ad51c-548d-720d-a46b-4a45376fb3a6', '019ad3f5-0770-7207-869e-8223556c1acb', 1, 5000.00, 5000.00, '2025-11-30 14:13:15', '2025-11-30 14:13:15');
INSERT INTO `sejajan_order_items` (`id`, `sejajan_order_id`, `sejajan_product_id`, `qty`, `price`, `subtotal`, `created_at`, `updated_at`) VALUES ('019ad51f-1b38-702e-b5ad-2d2320356977', '019ad51f-1adb-7199-909e-b8458def282c', '019ad3f5-0770-7207-869e-8223556c1acb', 1, 5000.00, 5000.00, '2025-11-30 14:16:16', '2025-11-30 14:16:16');
COMMIT;

-- ----------------------------
-- Table structure for sejajan_orders
-- ----------------------------
DROP TABLE IF EXISTS `sejajan_orders`;
CREATE TABLE `sejajan_orders` (
  `id` char(36) NOT NULL,
  `sejajan_id` char(36) NOT NULL,
  `participant_id` char(36) NOT NULL,
  `status` enum('pending','paid','processing','ready','completed','cancelled') NOT NULL DEFAULT 'pending',
  `total_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `pickup_time` timestamp NULL DEFAULT NULL,
  `location_pickup` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sejajan_orders_sejajan_id_index` (`sejajan_id`),
  KEY `sejajan_orders_participant_id_index` (`participant_id`),
  CONSTRAINT `sejajan_orders_participant_id_foreign` FOREIGN KEY (`participant_id`) REFERENCES `participants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sejajan_orders_sejajan_id_foreign` FOREIGN KEY (`sejajan_id`) REFERENCES `sejajans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of sejajan_orders
-- ----------------------------
BEGIN;
INSERT INTO `sejajan_orders` (`id`, `sejajan_id`, `participant_id`, `status`, `total_price`, `notes`, `pickup_time`, `location_pickup`, `created_at`, `updated_at`) VALUES ('019ad404-3627-71b3-9b08-9bebc6fc9023', '019ad3e2-4722-707f-b8a4-d820e8cd9dfe', '019a8fc2-5dff-70d5-96f8-b80980a18cc1', 'completed', 5000.00, 'asdsad\nAlamat: XI PPLG 1', '2025-11-30 09:27:00', NULL, '2025-11-30 09:07:17', '2025-11-30 09:11:35');
INSERT INTO `sejajan_orders` (`id`, `sejajan_id`, `participant_id`, `status`, `total_price`, `notes`, `pickup_time`, `location_pickup`, `created_at`, `updated_at`) VALUES ('019ad413-465e-733b-8141-1b4d9f26687e', '019ad3e2-4722-707f-b8a4-d820e8cd9dfe', '019a8fc2-5dff-70d5-96f8-b80980a18cc1', 'cancelled', 5000.00, 'saadasd\nAlamat: sdas', '2025-11-30 09:22:00', NULL, '2025-11-30 09:23:44', '2025-11-30 13:49:46');
INSERT INTO `sejajan_orders` (`id`, `sejajan_id`, `participant_id`, `status`, `total_price`, `notes`, `pickup_time`, `location_pickup`, `created_at`, `updated_at`) VALUES ('019ad50c-6538-717d-84e0-de6439008699', '019ad3e2-4722-707f-b8a4-d820e8cd9dfe', '019ad507-9482-70bd-9f45-da49eaea2d09', 'cancelled', 10000.00, 'sdsd', '2025-11-30 13:53:00', NULL, '2025-11-30 13:55:50', '2025-11-30 13:56:39');
INSERT INTO `sejajan_orders` (`id`, `sejajan_id`, `participant_id`, `status`, `total_price`, `notes`, `pickup_time`, `location_pickup`, `created_at`, `updated_at`) VALUES ('019ad50d-b0d3-7045-8a2b-64d18438f2fa', '019ad3e2-4722-707f-b8a4-d820e8cd9dfe', '019ad507-9482-70bd-9f45-da49eaea2d09', 'cancelled', 10000.00, NULL, '2025-11-30 13:56:00', NULL, '2025-11-30 13:57:15', '2025-11-30 13:59:41');
INSERT INTO `sejajan_orders` (`id`, `sejajan_id`, `participant_id`, `status`, `total_price`, `notes`, `pickup_time`, `location_pickup`, `created_at`, `updated_at`) VALUES ('019ad510-4962-726b-a24e-48649a17e73e', '019ad3e2-4722-707f-b8a4-d820e8cd9dfe', '019ad507-9482-70bd-9f45-da49eaea2d09', 'cancelled', 15000.00, 'sdas', '2025-11-30 14:59:00', NULL, '2025-11-30 14:00:05', '2025-11-30 14:00:31');
INSERT INTO `sejajan_orders` (`id`, `sejajan_id`, `participant_id`, `status`, `total_price`, `notes`, `pickup_time`, `location_pickup`, `created_at`, `updated_at`) VALUES ('019ad51b-769b-7312-a8a4-fdf0b414f293', '019ad3e2-4722-707f-b8a4-d820e8cd9dfe', '019ad507-9482-70bd-9f45-da49eaea2d09', 'cancelled', 5000.00, 'sadasd', '2025-11-30 14:11:00', NULL, '2025-11-30 14:12:18', '2025-11-30 14:12:59');
INSERT INTO `sejajan_orders` (`id`, `sejajan_id`, `participant_id`, `status`, `total_price`, `notes`, `pickup_time`, `location_pickup`, `created_at`, `updated_at`) VALUES ('019ad51c-548d-720d-a46b-4a45376fb3a6', '019ad3e2-4722-707f-b8a4-d820e8cd9dfe', '019ad507-9482-70bd-9f45-da49eaea2d09', 'pending', 5000.00, 'asdas', '2025-11-30 14:12:00', NULL, '2025-11-30 14:13:15', '2025-11-30 14:13:15');
INSERT INTO `sejajan_orders` (`id`, `sejajan_id`, `participant_id`, `status`, `total_price`, `notes`, `pickup_time`, `location_pickup`, `created_at`, `updated_at`) VALUES ('019ad51f-1adb-7199-909e-b8458def282c', '019ad3e2-4722-707f-b8a4-d820e8cd9dfe', '019ad507-9482-70bd-9f45-da49eaea2d09', 'pending', 5000.00, NULL, '2025-11-30 14:15:00', NULL, '2025-11-30 14:16:16', '2025-11-30 14:16:16');
COMMIT;

-- ----------------------------
-- Table structure for sejajan_products
-- ----------------------------
DROP TABLE IF EXISTS `sejajan_products`;
CREATE TABLE `sejajan_products` (
  `id` char(36) NOT NULL,
  `sejajan_id` char(36) NOT NULL,
  `category_id` char(36) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(12,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `photo` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sejajan_products_sejajan_id_slug_unique` (`sejajan_id`,`slug`),
  KEY `sejajan_products_sejajan_id_index` (`sejajan_id`),
  KEY `sejajan_products_category_id_index` (`category_id`),
  CONSTRAINT `sejajan_products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `sejajan_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sejajan_products_sejajan_id_foreign` FOREIGN KEY (`sejajan_id`) REFERENCES `sejajans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of sejajan_products
-- ----------------------------
BEGIN;
INSERT INTO `sejajan_products` (`id`, `sejajan_id`, `category_id`, `name`, `slug`, `description`, `price`, `stock`, `photo`, `is_active`, `created_at`, `updated_at`) VALUES ('019ad3f5-0770-7207-869e-8223556c1acb', '019ad3e2-4722-707f-b8a4-d820e8cd9dfe', NULL, 'Teh Botol', 'teh-botol', 'minuman segar', 5000.00, 0, '1764492642_TEH-BOTOL-KOTAK-330ml.jpg', 1, '2025-11-30 08:50:42', '2025-11-30 14:16:17');
COMMIT;

-- ----------------------------
-- Table structure for sejajans
-- ----------------------------
DROP TABLE IF EXISTS `sejajans`;
CREATE TABLE `sejajans` (
  `id` char(36) NOT NULL,
  `participant_id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sejajans_slug_unique` (`slug`),
  KEY `sejajans_participant_id_index` (`participant_id`),
  CONSTRAINT `sejajans_participant_id_foreign` FOREIGN KEY (`participant_id`) REFERENCES `participants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of sejajans
-- ----------------------------
BEGIN;
INSERT INTO `sejajans` (`id`, `participant_id`, `name`, `slug`, `description`, `photo`, `is_active`, `created_at`, `updated_at`) VALUES ('019ad3e2-4722-707f-b8a4-d820e8cd9dfe', '019a8fc2-5dff-70d5-96f8-b80980a18cc1', 'Ajay', 'ajay', 'tesssssssss', '1764491413_WhatsApp_Image_2025-10-08_at_9.23.59_PM.jpg', 1, '2025-11-30 08:30:13', '2025-11-30 08:30:13');
COMMIT;

-- ----------------------------
-- Table structure for sessions
-- ----------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of sessions
-- ----------------------------
BEGIN;
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('GFlFWngWusInEIIk72hUFVvaOJI8wtnUqTb9MztX', 1, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiMU5kZWhlV2d6UjRYU0Y5TWFPQUxtRlA0VWJteDlHUWV5eWRXZzlQTyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM1OiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYWRtaW4vc2VqYWphbiI7czo1OiJyb3V0ZSI7czoxOToiYWRtaW4uc2VqYWphbi5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1764542120);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('RxAFVHopDTWpGvgPztTE2m6qIQuFnaSBOSBapHVc', 1, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZ1J5WFBTRW9UaElUcGlPMHE3UFhpOUtnc1FqdEQya283MFp1N2tmdyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hZG1pbi9zZWphamFuIjtzOjU6InJvdXRlIjtzOjE5OiJhZG1pbi5zZWphamFuLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1764500789);
COMMIT;

-- ----------------------------
-- Table structure for user_group_permissions
-- ----------------------------
DROP TABLE IF EXISTS `user_group_permissions`;
CREATE TABLE `user_group_permissions` (
  `id` char(36) NOT NULL,
  `user_group_id` char(36) NOT NULL,
  `module_access_id` char(36) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ug_module_access_idx` (`user_group_id`,`module_access_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of user_group_permissions
-- ----------------------------
BEGIN;
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76c6-81c4-71ed-8586-5576d710ca79', '019a76c6-815a-73df-9b0a-4bd4d100dbbe', '019a76c6-8160-72a2-9ff9-6b8c2005388d', 1, '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76c6-81c5-72d0-9c9e-3a43df7e0aa5', '019a76c6-815a-73df-9b0a-4bd4d100dbbe', '019a76c6-8162-737f-a868-ccd95894e82f', 1, '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76c6-81c7-723a-8230-05cc74926256', '019a76c6-815a-73df-9b0a-4bd4d100dbbe', '019a76c6-8164-7019-80e1-c8dd73af0d10', 1, '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76c6-81c8-7349-a569-9fa779605bd5', '019a76c6-815a-73df-9b0a-4bd4d100dbbe', '019a76c6-8166-7395-be05-9c6dade8dd24', 1, '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76c6-81ca-705c-8b1c-4e1ea5c80627', '019a76c6-815a-73df-9b0a-4bd4d100dbbe', '019a76c6-8168-7292-91fe-4bf5ecdceb93', 1, '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76c6-81cb-7360-8f84-7874255a5b32', '019a76c6-815a-73df-9b0a-4bd4d100dbbe', '019a76c6-8169-7373-9de3-39df9f5e4a39', 1, '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76c6-81cd-708b-9685-198901f7ba70', '019a76c6-815a-73df-9b0a-4bd4d100dbbe', '019a76c6-816b-73cf-92a8-602dc49a5bf6', 1, '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76c6-81ce-70d4-97ee-71bc922111a6', '019a76c6-815a-73df-9b0a-4bd4d100dbbe', '019a76c6-816d-7088-bfcc-0d121b7be0d7', 1, '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76c6-81d3-734c-95bd-122c1d18b729', '019a76c6-815a-73df-9b0a-4bd4d100dbbe', '019a76c6-816e-7139-bb30-4106ba15022e', 1, '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76c6-81d5-7054-ac91-502804cb0793', '019a76c6-815a-73df-9b0a-4bd4d100dbbe', '019a76c6-8170-7220-82cd-fe1b39f95810', 1, '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76c6-81d7-72df-b154-a43ce5fdac11', '019a76c6-815a-73df-9b0a-4bd4d100dbbe', '019a76c6-8172-738d-972e-98f42961961f', 1, '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76c6-81d9-72b9-9c8d-639912fd6ce6', '019a76c6-815a-73df-9b0a-4bd4d100dbbe', '019a76c6-8173-7276-b6c7-df8a3f5d3115', 1, '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76c6-81da-72a9-8ff5-9085bb6c5e18', '019a76c6-815a-73df-9b0a-4bd4d100dbbe', '019a76c6-8176-7215-abdd-8b3fa8aace35', 1, '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76c6-81db-724e-9188-8fc788b3a18d', '019a76c6-815a-73df-9b0a-4bd4d100dbbe', '019a76c6-8178-7201-bad6-f72729848bb1', 1, '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76c6-81dd-709a-aae8-cec7c465ef68', '019a76c6-815a-73df-9b0a-4bd4d100dbbe', '019a76c6-817a-73de-874a-a8a20890f956', 1, '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76c6-81e0-71d8-8f17-4ddda32fdd1a', '019a76c6-815a-73df-9b0a-4bd4d100dbbe', '019a76c6-817b-7055-aef2-46354c8ac810', 1, '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76c6-81e2-704f-9544-c90a7d2d5ce5', '019a76c6-815a-73df-9b0a-4bd4d100dbbe', '019a76c6-817d-710e-b6ec-9dd5d1a06018', 1, '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76c6-81e4-701c-b875-c0f52514f730', '019a76c6-815a-73df-9b0a-4bd4d100dbbe', '019a76c6-817f-7209-ac8e-9600e28e21de', 1, '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76c6-81e6-735e-bb6e-3727a559d202', '019a76c6-815a-73df-9b0a-4bd4d100dbbe', '019a76c6-8181-72d8-a8e7-727112c14ed5', 1, '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76c6-81e7-7318-8fda-f884a65fca61', '019a76c6-815a-73df-9b0a-4bd4d100dbbe', '019a76c6-8183-731d-9157-030dcd04424d', 1, '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76c6-81e8-7277-bcfb-e9cd55be70e6', '019a76c6-815a-73df-9b0a-4bd4d100dbbe', '019a76c6-818b-70bb-bd8f-62c01ad877a6', 1, '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76c6-81ea-7130-b1b0-69f4e8f7a7f4', '019a76c6-815a-73df-9b0a-4bd4d100dbbe', '019a76c6-818d-7178-a8bb-feecbfedb1cf', 1, '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76c6-81eb-704b-a0a7-fa986ccf7d7d', '019a76c6-815a-73df-9b0a-4bd4d100dbbe', '019a76c6-818e-72b8-a87b-ab70592384e6', 1, '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76c6-81ed-70ba-a065-85285f0650cc', '019a76c6-815a-73df-9b0a-4bd4d100dbbe', '019a76c6-8191-733f-a593-894416d7c959', 1, '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76c6-81ee-7355-b889-f5d46d4759f6', '019a76c6-815d-73f0-bcf7-bcb246cab3bb', '019a76c6-816e-7139-bb30-4106ba15022e', 1, '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76c6-81f0-731c-b716-70e5ef5de46b', '019a76c6-815d-73f0-bcf7-bcb246cab3bb', '019a76c6-8170-7220-82cd-fe1b39f95810', 1, '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76c6-81f1-73b0-91a5-dacd648389a7', '019a76c6-815d-73f0-bcf7-bcb246cab3bb', '019a76c6-8172-738d-972e-98f42961961f', 1, '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76c6-81f3-723f-9bdb-371f3d0f6829', '019a76c6-815d-73f0-bcf7-bcb246cab3bb', '019a76c6-8173-7276-b6c7-df8a3f5d3115', 1, '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76ea-42f8-72d4-8ae1-1293c6800866', '019a76c6-8157-725b-8c8a-37c64c1d861c', '019a76c6-8160-72a2-9ff9-6b8c2005388d', 1, '2025-11-12 00:14:15', '2025-11-12 00:14:15');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76ea-42fb-7093-abc7-abf8c79935f0', '019a76c6-8157-725b-8c8a-37c64c1d861c', '019a76c6-8162-737f-a868-ccd95894e82f', 1, '2025-11-12 00:14:15', '2025-11-12 00:14:15');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76ea-42fd-7191-a626-3c250119a79b', '019a76c6-8157-725b-8c8a-37c64c1d861c', '019a76c6-8164-7019-80e1-c8dd73af0d10', 1, '2025-11-12 00:14:15', '2025-11-12 00:14:15');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76ea-42ff-7085-a702-19ad46ae6c86', '019a76c6-8157-725b-8c8a-37c64c1d861c', '019a76c6-8166-7395-be05-9c6dade8dd24', 1, '2025-11-12 00:14:15', '2025-11-12 00:14:15');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76ea-4300-72ef-a6b1-f4fb1bebc276', '019a76c6-8157-725b-8c8a-37c64c1d861c', '019a76c6-816e-7139-bb30-4106ba15022e', 1, '2025-11-12 00:14:15', '2025-11-12 00:14:15');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76ea-4302-7080-81fc-1176be066410', '019a76c6-8157-725b-8c8a-37c64c1d861c', '019a76c6-8170-7220-82cd-fe1b39f95810', 1, '2025-11-12 00:14:15', '2025-11-12 00:14:15');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76ea-4303-70d0-99a8-d095f0b8cdd7', '019a76c6-8157-725b-8c8a-37c64c1d861c', '019a76c6-8172-738d-972e-98f42961961f', 1, '2025-11-12 00:14:15', '2025-11-12 00:14:15');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76ea-4305-731f-8270-39643e32d888', '019a76c6-8157-725b-8c8a-37c64c1d861c', '019a76c6-8173-7276-b6c7-df8a3f5d3115', 1, '2025-11-12 00:14:15', '2025-11-12 00:14:15');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76ea-4306-72bb-8ae6-a237304809ce', '019a76c6-8157-725b-8c8a-37c64c1d861c', '019a76c6-817d-710e-b6ec-9dd5d1a06018', 1, '2025-11-12 00:14:15', '2025-11-12 00:14:15');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76ea-4308-7033-8941-8125b46d6ac4', '019a76c6-8157-725b-8c8a-37c64c1d861c', '019a76c6-817f-7209-ac8e-9600e28e21de', 1, '2025-11-12 00:14:15', '2025-11-12 00:14:15');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76ea-4309-7317-ba6b-eca06b962554', '019a76c6-8157-725b-8c8a-37c64c1d861c', '019a76c6-8181-72d8-a8e7-727112c14ed5', 1, '2025-11-12 00:14:15', '2025-11-12 00:14:15');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76ea-430b-73b7-98c6-f5a56e7cfd1c', '019a76c6-8157-725b-8c8a-37c64c1d861c', '019a76c6-8183-731d-9157-030dcd04424d', 1, '2025-11-12 00:14:15', '2025-11-12 00:14:15');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76ea-430e-72c4-81aa-c0c39b4a4303', '019a76c6-8157-725b-8c8a-37c64c1d861c', '019a76c6-8176-7215-abdd-8b3fa8aace35', 1, '2025-11-12 00:14:15', '2025-11-12 00:14:15');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76ea-4310-7048-a1b4-7d26411cd53a', '019a76c6-8157-725b-8c8a-37c64c1d861c', '019a76c6-8178-7201-bad6-f72729848bb1', 1, '2025-11-12 00:14:15', '2025-11-12 00:14:15');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76ea-4311-71ef-a8da-08c7fa01f79c', '019a76c6-8157-725b-8c8a-37c64c1d861c', '019a76c6-817a-73de-874a-a8a20890f956', 1, '2025-11-12 00:14:15', '2025-11-12 00:14:15');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76ea-4313-7395-9279-aea31aa5b520', '019a76c6-8157-725b-8c8a-37c64c1d861c', '019a76c6-817b-7055-aef2-46354c8ac810', 1, '2025-11-12 00:14:15', '2025-11-12 00:14:15');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76ea-4314-7350-80ec-14c180dfbae6', '019a76c6-8157-725b-8c8a-37c64c1d861c', '019a76c6-8184-724c-b022-0e2fc17a1f24', 1, '2025-11-12 00:14:15', '2025-11-12 00:14:15');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76ea-4315-72fe-a92d-a9d60acdc333', '019a76c6-8157-725b-8c8a-37c64c1d861c', '019a76c6-8186-72de-9adf-d930d05be239', 1, '2025-11-12 00:14:15', '2025-11-12 00:14:15');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76ea-4317-73fd-a864-460f55b5bcf5', '019a76c6-8157-725b-8c8a-37c64c1d861c', '019a76c6-8187-7030-934d-89af45b388b0', 1, '2025-11-12 00:14:15', '2025-11-12 00:14:15');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76ea-4319-7007-b5bb-63ebb726eee4', '019a76c6-8157-725b-8c8a-37c64c1d861c', '019a76c6-8189-73c6-8ead-3c94b04661cd', 1, '2025-11-12 00:14:15', '2025-11-12 00:14:15');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76ea-431a-70ac-bfb2-0120a1eb03d5', '019a76c6-8157-725b-8c8a-37c64c1d861c', '019a76c6-818b-70bb-bd8f-62c01ad877a6', 1, '2025-11-12 00:14:15', '2025-11-12 00:14:15');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76ea-431b-70af-a847-e7164435ecc9', '019a76c6-8157-725b-8c8a-37c64c1d861c', '019a76c6-818d-7178-a8bb-feecbfedb1cf', 1, '2025-11-12 00:14:15', '2025-11-12 00:14:15');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76ea-431d-72b3-9ec2-3e473ba89e34', '019a76c6-8157-725b-8c8a-37c64c1d861c', '019a76c6-818e-72b8-a87b-ab70592384e6', 1, '2025-11-12 00:14:15', '2025-11-12 00:14:15');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('019a76ea-431e-7085-a4a4-59380ddff978', '019a76c6-8157-725b-8c8a-37c64c1d861c', '019a76c6-8191-733f-a593-894416d7c959', 1, '2025-11-12 00:14:15', '2025-11-12 00:14:15');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('6a194c3a-25d9-481b-bd68-b79a8d782463', '019a76c6-8157-725b-8c8a-37c64c1d861c', '03b3a080-25c7-4c6f-981d-239f17fb3b17', 1, '2025-11-13 04:11:53', '2025-11-13 04:11:53');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('722c8ce0-69e8-46c8-8722-4db2093c4f95', '019a76c6-8157-725b-8c8a-37c64c1d861c', 'c382aa23-a29a-4b67-af8e-e77bc458c283', 1, '2025-11-13 04:11:53', '2025-11-13 04:11:53');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('cb87a8b9-5b5d-4cdd-881f-bc7d6e0da448', '019a76c6-8157-725b-8c8a-37c64c1d861c', '95ed6af6-c85a-445b-a443-898c487fdf41', 1, '2025-11-13 04:11:53', '2025-11-13 04:11:53');
INSERT INTO `user_group_permissions` (`id`, `user_group_id`, `module_access_id`, `status`, `created_at`, `updated_at`) VALUES ('d6974011-a707-4339-96d8-049422c52644', '019a76c6-8157-725b-8c8a-37c64c1d861c', '58578fda-1047-46ec-b85e-e28822dd3f5b', 1, '2025-11-13 04:11:53', '2025-11-13 04:11:53');
COMMIT;

-- ----------------------------
-- Table structure for user_groups
-- ----------------------------
DROP TABLE IF EXISTS `user_groups`;
CREATE TABLE `user_groups` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of user_groups
-- ----------------------------
BEGIN;
INSERT INTO `user_groups` (`id`, `name`, `status`, `created_by`, `created_at`, `updated_at`, `updated_by`) VALUES ('019a76c6-8157-725b-8c8a-37c64c1d861c', 'Super Admin', 1, NULL, '2025-11-11 23:35:12', '2025-11-11 23:35:12', NULL);
INSERT INTO `user_groups` (`id`, `name`, `status`, `created_by`, `created_at`, `updated_at`, `updated_by`) VALUES ('019a76c6-815a-73df-9b0a-4bd4d100dbbe', 'Admin', 1, NULL, '2025-11-11 23:35:12', '2025-11-11 23:35:12', NULL);
INSERT INTO `user_groups` (`id`, `name`, `status`, `created_by`, `created_at`, `updated_at`, `updated_by`) VALUES ('019a76c6-815d-73f0-bcf7-bcb246cab3bb', 'Editor', 1, NULL, '2025-11-11 23:35:12', '2025-11-11 23:35:12', NULL);
COMMIT;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `user_group_id` char(36) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_uuid_unique` (`uuid`),
  KEY `users_user_group_id_foreign` (`user_group_id`),
  CONSTRAINT `users_user_group_id_foreign` FOREIGN KEY (`user_group_id`) REFERENCES `user_groups` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of users
-- ----------------------------
BEGIN;
INSERT INTO `users` (`id`, `uuid`, `name`, `email`, `email_verified_at`, `password`, `user_group_id`, `remember_token`, `created_at`, `updated_at`) VALUES (1, '31a10c55-c03d-11f0-bbb7-5855a024d5bf', 'Super Admin', 'dev@gncs.dev', '2025-11-11 23:35:12', '$2y$12$7hJS7vPPvZ1pnXqhxY8Zw.wD83Dc73uREn.7QsKvVA2SRPS52sHJW', '019a76c6-8157-725b-8c8a-37c64c1d861c', NULL, '2025-11-11 23:35:12', '2025-11-11 23:35:12');
INSERT INTO `users` (`id`, `uuid`, `name`, `email`, `email_verified_at`, `password`, `user_group_id`, `remember_token`, `created_at`, `updated_at`) VALUES (2, '31a10d98-c03d-11f0-bbb7-5855a024d5bf', 'Agung', 'dev@gnc.com', NULL, '$2y$12$qtQvIVzwmtlE1iUezGIBteECStYNMK4mn8PqFNBDCATVz/kqnss22', '019a76c6-8157-725b-8c8a-37c64c1d861c', NULL, '2025-11-12 00:14:15', '2025-11-12 00:14:15');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
