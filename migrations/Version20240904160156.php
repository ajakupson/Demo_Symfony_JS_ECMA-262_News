<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240904160156 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE news (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, short_description LONGTEXT NOT NULL, content LONGTEXT NOT NULL, insert_date DATETIME NOT NULL, picture VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE news_to_category (news_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_E2203120B5A459A0 (news_id), INDEX IDX_E220312012469DE2 (category_id), PRIMARY KEY(news_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE news_category (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE news_comments (id INT AUTO_INCREMENT NOT NULL, news_id INT NOT NULL, content LONGTEXT NOT NULL, username VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_16A0357BB5A459A0 (news_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE news_to_category ADD CONSTRAINT FK_E2203120B5A459A0 FOREIGN KEY (news_id) REFERENCES news (id)');
        $this->addSql('ALTER TABLE news_to_category ADD CONSTRAINT FK_E220312012469DE2 FOREIGN KEY (category_id) REFERENCES news_category (id)');
        $this->addSql('ALTER TABLE news_comments ADD CONSTRAINT FK_16A0357BB5A459A0 FOREIGN KEY (news_id) REFERENCES news (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE news_to_category DROP FOREIGN KEY FK_E2203120B5A459A0');
        $this->addSql('ALTER TABLE news_to_category DROP FOREIGN KEY FK_E220312012469DE2');
        $this->addSql('ALTER TABLE news_comments DROP FOREIGN KEY FK_16A0357BB5A459A0');
        $this->addSql('DROP TABLE news');
        $this->addSql('DROP TABLE news_to_category');
        $this->addSql('DROP TABLE news_category');
        $this->addSql('DROP TABLE news_comments');
    }
}
