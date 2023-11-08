<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231108142433 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE config ADD taux_tva INT DEFAULT NULL, DROP email_contact, DROP email_objet, DROP seo_title, DROP seo_description, DROP seo_keywords');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE config ADD email_contact VARCHAR(255) DEFAULT NULL, ADD email_objet VARCHAR(255) NOT NULL, ADD seo_title VARCHAR(255) DEFAULT NULL, ADD seo_description VARCHAR(255) DEFAULT NULL, ADD seo_keywords LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', DROP taux_tva');
    }
}
