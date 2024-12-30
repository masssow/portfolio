<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241218172805 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE posts_categorie_posts (posts_id INT NOT NULL, categorie_posts_id INT NOT NULL, INDEX IDX_F86135C7D5E258C5 (posts_id), INDEX IDX_F86135C7ED6519AF (categorie_posts_id), PRIMARY KEY(posts_id, categorie_posts_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE posts_categorie_posts ADD CONSTRAINT FK_F86135C7D5E258C5 FOREIGN KEY (posts_id) REFERENCES posts (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE posts_categorie_posts ADD CONSTRAINT FK_F86135C7ED6519AF FOREIGN KEY (categorie_posts_id) REFERENCES categorie_posts (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE posts_categorie_posts DROP FOREIGN KEY FK_F86135C7D5E258C5');
        $this->addSql('ALTER TABLE posts_categorie_posts DROP FOREIGN KEY FK_F86135C7ED6519AF');
        $this->addSql('DROP TABLE posts_categorie_posts');
    }
}
