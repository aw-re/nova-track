-- Migration to add created_by column to tasks table
CREATE TABLE IF NOT EXISTS `migrations` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `migration` varchar(255) NOT NULL,
    `batch` int(11) NOT NULL,
    PRIMARY KEY (`id`)
);

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2025_04_15_023000_add_created_by_to_tasks_table', (SELECT COALESCE(MAX(`batch`), 0) + 1 FROM `migrations` as temp));

ALTER TABLE `tasks` ADD COLUMN IF NOT EXISTS `created_by` bigint(20) UNSIGNED DEFAULT NULL AFTER `project_id`;
ALTER TABLE `tasks` ADD CONSTRAINT `tasks_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- Migration to add type column to resources table
INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2025_04_15_023001_add_type_to_resources_table', (SELECT COALESCE(MAX(`batch`), 0) FROM `migrations`));

ALTER TABLE `resources` ADD COLUMN IF NOT EXISTS `type` varchar(255) DEFAULT 'material' AFTER `name`;
