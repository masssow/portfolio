<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250914071726 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create base tables if missing and add FKs only if absent (idempotent).';
    }

    public function up(Schema $schema): void
    {
        $sm = $this->connection->createSchemaManager();

        // --- Tables ---
        if (!$sm->tablesExist(['admin'])) {
            $this->addSql("CREATE TABLE `admin` (
                id INT AUTO_INCREMENT NOT NULL,
                username VARCHAR(180) NOT NULL,
                roles JSON NOT NULL,
                password VARCHAR(255) NOT NULL,
                UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        }

        if (!$sm->tablesExist(['categorie_posts'])) {
            $this->addSql("CREATE TABLE categorie_posts (
                id INT AUTO_INCREMENT NOT NULL,
                name VARCHAR(255) DEFAULT NULL,
                description LONGTEXT DEFAULT NULL,
                image_name VARCHAR(255) DEFAULT NULL,
                image_size INT DEFAULT NULL,
                updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        }

        if (!$sm->tablesExist(['consent_log'])) {
            $this->addSql("CREATE TABLE consent_log (
                id INT AUTO_INCREMENT NOT NULL,
                consent_id VARCHAR(255) DEFAULT NULL,
                status VARCHAR(255) DEFAULT NULL,
                categories LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)',
                ip_hash VARCHAR(255) DEFAULT NULL,
                user_agent VARCHAR(255) DEFAULT NULL,
                policy_version VARCHAR(255) DEFAULT NULL,
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        }

        if (!$sm->tablesExist(['message'])) {
            $this->addSql("CREATE TABLE message (
                id INT AUTO_INCREMENT NOT NULL,
                post_id INT DEFAULT NULL,
                name VARCHAR(255) DEFAULT NULL,
                email VARCHAR(255) NOT NULL,
                content LONGTEXT NOT NULL,
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                INDEX IDX_B6BD307F4B89032C (post_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        }

        if (!$sm->tablesExist(['posts'])) {
            $this->addSql("CREATE TABLE posts (
                id INT AUTO_INCREMENT NOT NULL,
                title VARCHAR(255) DEFAULT NULL,
                content LONGTEXT DEFAULT NULL,
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                image_name VARCHAR(255) DEFAULT NULL,
                image_size INT DEFAULT NULL,
                image_two_name VARCHAR(255) DEFAULT NULL,
                image_two_size INT DEFAULT NULL,
                image_three_name VARCHAR(255) DEFAULT NULL,
                image_three_size INT DEFAULT NULL,
                paragraphe2 LONGTEXT DEFAULT NULL,
                paragraphe3 LONGTEXT DEFAULT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        }

        if (!$sm->tablesExist(['posts_categorie_posts'])) {
            $this->addSql("CREATE TABLE posts_categorie_posts (
                posts_id INT NOT NULL,
                categorie_posts_id INT NOT NULL,
                INDEX IDX_F86135C7D5E258C5 (posts_id),
                INDEX IDX_F86135C7ED6519AF (categorie_posts_id),
                PRIMARY KEY(posts_id, categorie_posts_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        }

        if (!$sm->tablesExist(['quote'])) {
            $this->addSql("CREATE TABLE quote (
                id INT AUTO_INCREMENT NOT NULL,
                name VARCHAR(255) NOT NULL,
                company VARCHAR(255) DEFAULT NULL,
                phone VARCHAR(255) DEFAULT NULL,
                email VARCHAR(255) NOT NULL,
                pack VARCHAR(255) DEFAULT NULL,
                budget VARCHAR(255) DEFAULT NULL,
                message LONGTEXT NOT NULL,
                locale VARCHAR(255) NOT NULL,
                ip VARCHAR(255) NOT NULL,
                user_agent VARCHAR(255) DEFAULT NULL,
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                status VARCHAR(255) NOT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        }

        if (!$sm->tablesExist(['messenger_messages'])) {
            $this->addSql("CREATE TABLE messenger_messages (
                id BIGINT AUTO_INCREMENT NOT NULL,
                body LONGTEXT NOT NULL,
                headers LONGTEXT NOT NULL,
                queue_name VARCHAR(190) NOT NULL,
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                INDEX IDX_75EA56E0FB7336F0 (queue_name),
                INDEX IDX_75EA56E0E3BD61CE (available_at),
                INDEX IDX_75EA56E016BA31DB (delivered_at),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        }

        // --- Foreign keys (only if missing) ---
        if ($sm->tablesExist(['message'])) {
            $existingFks = array_map(fn($fk) => $fk->getName(), $sm->listTableForeignKeys('message'));
            if (!in_array('FK_B6BD307F4B89032C', $existingFks, true) && $sm->tablesExist(['posts'])) {
                $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F4B89032C FOREIGN KEY (post_id) REFERENCES posts (id)');
            }
        }

        if ($sm->tablesExist(['posts_categorie_posts'])) {
            $existingFks = array_map(fn($fk) => $fk->getName(), $sm->listTableForeignKeys('posts_categorie_posts'));
            if (!in_array('FK_F86135C7D5E258C5', $existingFks, true) && $sm->tablesExist(['posts'])) {
                $this->addSql('ALTER TABLE posts_categorie_posts ADD CONSTRAINT FK_F86135C7D5E258C5 FOREIGN KEY (posts_id) REFERENCES posts (id) ON DELETE CASCADE');
            }
            if (!in_array('FK_F86135C7ED6519AF', $existingFks, true) && $sm->tablesExist(['categorie_posts'])) {
                $this->addSql('ALTER TABLE posts_categorie_posts ADD CONSTRAINT FK_F86135C7ED6519AF FOREIGN KEY (categorie_posts_id) REFERENCES categorie_posts (id) ON DELETE CASCADE');
            }
        }
    }

    public function down(Schema $schema): void
    {
        // Ordre sûr (tables dépendantes d'abord)
        $this->addSql('DROP TABLE IF EXISTS posts_categorie_posts');
        $this->addSql('DROP TABLE IF EXISTS message');
        $this->addSql('DROP TABLE IF EXISTS posts');
        $this->addSql('DROP TABLE IF EXISTS categorie_posts');
        $this->addSql('DROP TABLE IF EXISTS consent_log');
        $this->addSql('DROP TABLE IF EXISTS quote');
        $this->addSql('DROP TABLE IF EXISTS messenger_messages');
        $this->addSql('DROP TABLE IF EXISTS `admin`');
    }
}
