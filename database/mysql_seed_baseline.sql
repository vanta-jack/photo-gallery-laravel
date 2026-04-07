-- MySQL 8+ compatible compliance dump
-- Source: freshly rebuilt Laravel seeded baseline
SET NAMES utf8mb4;
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `album_photo`;
CREATE TABLE `album_photo` (
  `album_id` BIGINT UNSIGNED NOT NULL,
  `photo_id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`album_id`, `photo_id`),
  CONSTRAINT `fk_album_photo_0` FOREIGN KEY (`photo_id`) REFERENCES `photos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_album_photo_1` FOREIGN KEY (`album_id`) REFERENCES `albums` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `albums`;
CREATE TABLE `albums` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `cover_photo_id` BIGINT UNSIGNED NULL,
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `description` TEXT NULL,
  `is_private` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `is_favorite` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_albums_0` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` VARCHAR(255) NOT NULL,
  `value` TEXT NOT NULL,
  `expiration` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` VARCHAR(255) NOT NULL,
  `owner` VARCHAR(255) NOT NULL,
  `expiration` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` VARCHAR(255) NOT NULL,
  `connection` TEXT NOT NULL,
  `queue` TEXT NOT NULL,
  `payload` TEXT NOT NULL,
  `exception` TEXT NOT NULL,
  `failed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `guestbook_entries`;
CREATE TABLE `guestbook_entries` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id` BIGINT UNSIGNED NOT NULL,
  `photo_id` BIGINT UNSIGNED NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `guestbook_entries_post_id_unique` (`post_id`),
  CONSTRAINT `fk_guestbook_entries_0` FOREIGN KEY (`photo_id`) REFERENCES `photos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_guestbook_entries_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `total_jobs` BIGINT UNSIGNED NOT NULL,
  `pending_jobs` BIGINT UNSIGNED NOT NULL,
  `failed_jobs` BIGINT UNSIGNED NOT NULL,
  `failed_job_ids` TEXT NOT NULL,
  `options` TEXT NULL,
  `cancelled_at` BIGINT UNSIGNED NULL,
  `created_at` BIGINT UNSIGNED NOT NULL,
  `finished_at` BIGINT UNSIGNED NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` VARCHAR(255) NOT NULL,
  `payload` TEXT NOT NULL,
  `attempts` BIGINT UNSIGNED NOT NULL,
  `reserved_at` BIGINT UNSIGNED NULL,
  `available_at` BIGINT UNSIGNED NOT NULL,
  `created_at` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` VARCHAR(255) NOT NULL,
  `batch` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1, '0001_01_01_000000_create_users_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2, '0001_01_01_000001_create_cache_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3, '0001_01_01_000002_create_jobs_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4, '2026_04_04_195853_create_photos_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5, '2026_04_04_205521_create_albums_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6, '2026_04_04_205522_create_milestones_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7, '2026_04_04_205522_create_posts_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8, '2026_04_04_205620_create_photo_ratings_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9, '2026_04_04_205623_create_photo_comments_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10, '2026_04_04_205625_create_album_photo_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11, '2026_04_04_205627_create_post_votes_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12, '2026_04_04_205629_create_guestbook_entries_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13, '2026_04_04_205756_add_profile_photo_foreign_key_to_users_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14, '2026_04_05_111800_create_sessions_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15, '2026_04_06_165533_add_is_favorite_to_albums_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16, '2026_04_06_203126_add_cv_fields_to_users_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17, '2026_04_07_000001_add_is_public_to_milestones_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18, '2026_04_07_071037_make_posts_user_id_nullable', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19, '2026_04_07_071235_change_milestones_stage_to_string', 1);

DROP TABLE IF EXISTS `milestones`;
CREATE TABLE `milestones` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `photo_id` BIGINT UNSIGNED NULL,
  `stage` VARCHAR(255) NOT NULL,
  `label` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `is_public` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_milestones_0` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_milestones_1` FOREIGN KEY (`photo_id`) REFERENCES `photos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `milestones` (`id`, `user_id`, `photo_id`, `stage`, `label`, `description`, `created_at`, `updated_at`, `is_public`) VALUES (1, 2, NULL, 'baby', 'Baby · First Smile', 'A gentle newborn milestone used as an early-life demo placeholder.', '2004-04-07 00:00:00', '2004-04-07 00:00:00', 1);
INSERT INTO `milestones` (`id`, `user_id`, `photo_id`, `stage`, `label`, `description`, `created_at`, `updated_at`, `is_public`) VALUES (2, 2, NULL, 'toddler', 'Toddler · First Steps', 'Toddler stage placeholder for first independent steps.', '2005-04-17 00:00:00', '2005-04-17 00:00:00', 0);
INSERT INTO `milestones` (`id`, `user_id`, `photo_id`, `stage`, `label`, `description`, `created_at`, `updated_at`, `is_public`) VALUES (3, 2, NULL, 'preschool', 'Preschool · First Day of Preschool', 'Preschool transition milestone placeholder.', '2007-04-27 00:00:00', '2007-04-27 00:00:00', 1);
INSERT INTO `milestones` (`id`, `user_id`, `photo_id`, `stage`, `label`, `description`, `created_at`, `updated_at`, `is_public`) VALUES (4, 2, NULL, 'grade_school', 'Grade School · Grade 1 Kickoff', 'Elementary school start marker for demo timelines.', '2009-05-07 00:00:00', '2009-05-07 00:00:00', 0);
INSERT INTO `milestones` (`id`, `user_id`, `photo_id`, `stage`, `label`, `description`, `created_at`, `updated_at`, `is_public`) VALUES (5, 2, NULL, 'middle_school', 'Middle School · Science Fair Finalist', 'Middle school achievement placeholder.', '2014-05-17 00:00:00', '2014-05-17 00:00:00', 1);
INSERT INTO `milestones` (`id`, `user_id`, `photo_id`, `stage`, `label`, `description`, `created_at`, `updated_at`, `is_public`) VALUES (6, 2, NULL, 'high_school', 'High School · Freshman Orientation', 'High school entry marker in the normalized lifecycle sequence.', '2018-05-27 00:00:00', '2018-05-27 00:00:00', 0);
INSERT INTO `milestones` (`id`, `user_id`, `photo_id`, `stage`, `label`, `description`, `created_at`, `updated_at`, `is_public`) VALUES (7, 2, NULL, 'college', 'College · Capstone Presentation', 'College completion milestone placeholder.', '2023-06-06 00:00:00', '2023-06-06 00:00:00', 1);
INSERT INTO `milestones` (`id`, `user_id`, `photo_id`, `stage`, `label`, `description`, `created_at`, `updated_at`, `is_public`) VALUES (8, 2, NULL, 'adult', 'Adult · First Career Role', 'Adult-phase placeholder milestone for post-college progression.', '2025-06-16 00:00:00', '2025-06-16 00:00:00', 0);
INSERT INTO `milestones` (`id`, `user_id`, `photo_id`, `stage`, `label`, `description`, `created_at`, `updated_at`, `is_public`) VALUES (9, 1, NULL, 'baby', 'Baby · First Smile', 'A gentle newborn milestone used as an early-life demo placeholder.', '2004-04-07 00:00:00', '2004-04-07 00:00:00', 1);
INSERT INTO `milestones` (`id`, `user_id`, `photo_id`, `stage`, `label`, `description`, `created_at`, `updated_at`, `is_public`) VALUES (10, 1, NULL, 'toddler', 'Toddler · First Steps', 'Toddler stage placeholder for first independent steps.', '2005-04-17 00:00:00', '2005-04-17 00:00:00', 0);
INSERT INTO `milestones` (`id`, `user_id`, `photo_id`, `stage`, `label`, `description`, `created_at`, `updated_at`, `is_public`) VALUES (11, 1, NULL, 'preschool', 'Preschool · First Day of Preschool', 'Preschool transition milestone placeholder.', '2007-04-27 00:00:00', '2007-04-27 00:00:00', 1);
INSERT INTO `milestones` (`id`, `user_id`, `photo_id`, `stage`, `label`, `description`, `created_at`, `updated_at`, `is_public`) VALUES (12, 1, NULL, 'grade_school', 'Grade School · Grade 1 Kickoff', 'Elementary school start marker for demo timelines.', '2009-05-07 00:00:00', '2009-05-07 00:00:00', 0);
INSERT INTO `milestones` (`id`, `user_id`, `photo_id`, `stage`, `label`, `description`, `created_at`, `updated_at`, `is_public`) VALUES (13, 1, NULL, 'middle_school', 'Middle School · Science Fair Finalist', 'Middle school achievement placeholder.', '2014-05-17 00:00:00', '2014-05-17 00:00:00', 1);
INSERT INTO `milestones` (`id`, `user_id`, `photo_id`, `stage`, `label`, `description`, `created_at`, `updated_at`, `is_public`) VALUES (14, 1, NULL, 'high_school', 'High School · Freshman Orientation', 'High school entry marker in the normalized lifecycle sequence.', '2018-05-27 00:00:00', '2018-05-27 00:00:00', 0);
INSERT INTO `milestones` (`id`, `user_id`, `photo_id`, `stage`, `label`, `description`, `created_at`, `updated_at`, `is_public`) VALUES (15, 1, NULL, 'college', 'College · Capstone Presentation', 'College completion milestone placeholder.', '2023-06-06 00:00:00', '2023-06-06 00:00:00', 1);
INSERT INTO `milestones` (`id`, `user_id`, `photo_id`, `stage`, `label`, `description`, `created_at`, `updated_at`, `is_public`) VALUES (16, 1, NULL, 'adult', 'Adult · First Career Role', 'Adult-phase placeholder milestone for post-college progression.', '2025-06-16 00:00:00', '2025-06-16 00:00:00', 0);

DROP TABLE IF EXISTS `photo_comments`;
CREATE TABLE `photo_comments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `photo_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `body` TEXT NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_photo_comments_0` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_photo_comments_1` FOREIGN KEY (`photo_id`) REFERENCES `photos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `photo_ratings`;
CREATE TABLE `photo_ratings` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `photo_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `rating` BIGINT UNSIGNED NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `photo_ratings_photo_id_user_id_unique` (`photo_id`, `user_id`),
  CONSTRAINT `fk_photo_ratings_0` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_photo_ratings_1` FOREIGN KEY (`photo_id`) REFERENCES `photos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `photos`;
CREATE TABLE `photos` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `path` VARCHAR(255) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_photos_0` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `post_votes`;
CREATE TABLE `post_votes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `post_votes_post_id_user_id_unique` (`post_id`, `user_id`),
  CONSTRAINT `fk_post_votes_0` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_post_votes_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_posts_0` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` VARCHAR(255) NOT NULL,
  `user_id` BIGINT UNSIGNED NULL,
  `ip_address` VARCHAR(255) NULL,
  `user_agent` TEXT NULL,
  `payload` TEXT NOT NULL,
  `last_activity` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_last_activity_index` (`last_activity`),
  KEY `sessions_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `role` VARCHAR(255) NOT NULL DEFAULT 'guest',
  `email` VARCHAR(255) NULL,
  `first_name` VARCHAR(255) NULL,
  `last_name` VARCHAR(255) NULL,
  `password` VARCHAR(255) NULL,
  `profile_photo_id` BIGINT UNSIGNED NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `bio` TEXT NULL,
  `phone` VARCHAR(255) NULL,
  `phone_public` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `linkedin` VARCHAR(255) NULL,
  `academic_history` TEXT NULL,
  `professional_experience` TEXT NULL,
  `skills` TEXT NULL,
  `certifications` TEXT NULL,
  `orcid_id` VARCHAR(255) NULL,
  `github` VARCHAR(255) NULL,
  `other_links` TEXT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  CONSTRAINT `fk_users_0` FOREIGN KEY (`profile_photo_id`) REFERENCES `photos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `users` (`id`, `role`, `email`, `first_name`, `last_name`, `password`, `profile_photo_id`, `created_at`, `updated_at`, `bio`, `phone`, `phone_public`, `linkedin`, `academic_history`, `professional_experience`, `skills`, `certifications`, `orcid_id`, `github`, `other_links`) VALUES (1, 'user', 'user@domain.com', 'George', 'Schinner', '$2y$12$2z5oNHOzNHgj7Ar7c0qwGuwl0Ybjy5wJg3gsHNX8vUmbciUnLDg5a', NULL, '2026-04-07 07:19:20', '2026-04-07 07:19:20', 'SOMEBODY ought to eat or drink anything; so I''ll just see what was on the trumpet, and called out, ''First witness!'' The first question of course you know that cats COULD grin.'' ''They all can,'' said the King, and the Gryphon whispered in reply, ''for fear they should forget them before the trial''s over!'' thought Alice.', '+13197004633', 0, 'https://linkedin.com/in/eum-sint', '[{"degree":"BSc Computer Science","institution":"Mitchell-Macejkovic University","graduation_date":"1998-09-25"},{"degree":"MSc Data Science","institution":"Von-Hoppe Institute","graduation_date":"1970-11-05"}]', '[{"title":"Product Designer","company":"Swaniawski and Sons","start_date":"2010-03-27","end_date":"2011-12-26","description":"Quasi nihil molestias iste corporis id officiis fuga quod voluptatem excepturi sed eius."},{"title":"Senior Engineer","company":"Hettinger, Kihn and Hamill","start_date":"1984-04-17","end_date":null,"description":"Repellendus ut doloribus omnis eaque eos dolorem ut est aut quisquam unde cupiditate autem cum modi nesciunt."}]', '["Testing","System Design","Laravel","SQL","CI\/CD"]', '[{"name":"AWS Certified Developer","issuer":"Prohaska Group","awarded_on":"2021-09-04"}]', '7562-9434-0420-0345', 'https://github.com/america04', '[{"label":"Portfolio","url":"https:\/\/example.com\/laboriosam-adipisci-quia-fuga-eum-aperiam-amet-neque"},{"label":"Talks","url":"https:\/\/example.org\/dolorem-corporis-amet-sit-voluptates-odio-et"}]');
INSERT INTO `users` (`id`, `role`, `email`, `first_name`, `last_name`, `password`, `profile_photo_id`, `created_at`, `updated_at`, `bio`, `phone`, `phone_public`, `linkedin`, `academic_history`, `professional_experience`, `skills`, `certifications`, `orcid_id`, `github`, `other_links`) VALUES (2, 'admin', 'admin@domain.com', 'London', 'Rosenbaum', '$2y$12$nJe5EtZdSJodQcwPNc1wcuqf/efE8ykR.kPY9IzcP/A9Uqbgw6UhG', NULL, '2026-04-07 07:19:20', '2026-04-07 07:19:20', 'Hatter, ''or you''ll be telling me next that you weren''t to talk about trouble!'' said the Caterpillar. Alice thought she might as well as she went out, but it did not look at the time he was obliged to have lessons to learn! No, I''ve made up my mind about it; and as for the moment she appeared; but she gained courage.', '+19132586594', 0, 'https://linkedin.com/in/deserunt-maiores', '[{"degree":"BEng Software Engineering","institution":"Hoppe Inc University","graduation_date":"2007-05-06"},{"degree":"MSc Information Systems","institution":"Torp, Bashirian and Cummerata Institute","graduation_date":"1999-01-12"}]', '[{"title":"Product Designer","company":"Harvey, Kilback and Hettinger","start_date":"2002-09-21","end_date":"1976-12-23","description":"Eos quod et inventore enim quidem placeat officiis accusantium quae odio eum qui quod eum."},{"title":"Lead Designer","company":"O''Conner-Smith","start_date":"2020-08-06","end_date":null,"description":"Sit alias sit quisquam officia reprehenderit inventore quod quasi perspiciatis ratione optio id dolor provident aut nisi iure."}]', '["System Design","Laravel","CI\/CD","Vue.js","JavaScript","Testing","PHP"]', '[{"name":"Google Cloud Professional","issuer":"Kulas, Terry and O''Keefe","awarded_on":"2000-09-20"}]', '7195-4547-3132-8034', 'https://github.com/aebert', '[{"label":"Portfolio","url":"https:\/\/example.com\/nihil-officia-dolorem-laudantium-temporibus-molestiae"},{"label":"Talks","url":"https:\/\/example.org\/ratione-expedita-occaecati-voluptates-praesentium-rerum-harum"}]');

SET FOREIGN_KEY_CHECKS = 1;
