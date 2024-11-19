<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241118123217 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_campaign (id INT AUTO_INCREMENT NOT NULL, campaign_id INT DEFAULT NULL, user_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_FF98F6DDF639F774 (campaign_id), INDEX IDX_FF98F6DDA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_campaign ADD CONSTRAINT FK_FF98F6DDF639F774 FOREIGN KEY (campaign_id) REFERENCES campaign (id)');
        $this->addSql('ALTER TABLE user_campaign ADD CONSTRAINT FK_FF98F6DDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_campaign DROP FOREIGN KEY FK_FF98F6DDF639F774');
        $this->addSql('ALTER TABLE user_campaign DROP FOREIGN KEY FK_FF98F6DDA76ED395');
        $this->addSql('DROP TABLE user_campaign');
    }
}
