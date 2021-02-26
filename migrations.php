<?php
/** @var \PDO $pdo */
require_once './pdo_ini.php';

$sql = <<<'SQL'
CREATE TABLE IF NOT EXISTS `todo_lists` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `created_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`))
ENGINE = InnoDB;
);
SQL;
$pdo->exec($sql);

$sql = <<<'SQL'
CREATE TABLE IF NOT EXISTS `todo_tasks` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `todo_lists_id` INT UNSIGNED NOT NULL,
    `is_done` TINYINT(1) DEFAULT 0,
    `title` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NOT NULL,
    `created_at` DATETIME NOT NULL,
    `position` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`todo_lists_id`)
    REFERENCES `todo_lists` (`id`))
ENGINE = InnoDB;
SQL;
$pdo->exec($sql);
