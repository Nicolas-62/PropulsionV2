<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230821160913 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP can_create, DROP has_multi, DROP has_title, DROP has_sub_title, DROP has_content, DROP has_link');
        $this->addSql('ALTER TABLE mediaspec CHANGE heritage heritage VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mediaspec CHANGE heritage heritage INT NOT NULL');
        $this->addSql('ALTER TABLE category ADD can_create TINYINT(1) NOT NULL, ADD has_multi TINYINT(1) NOT NULL, ADD has_title TINYINT(1) NOT NULL, ADD has_sub_title TINYINT(1) NOT NULL, ADD has_content TINYINT(1) NOT NULL, ADD has_link TINYINT(1) NOT NULL');
    }
}
