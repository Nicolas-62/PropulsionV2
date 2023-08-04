<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230804092252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP content');
        $this->addSql('ALTER TABLE category_data DROP FOREIGN KEY FK_26688924232D562B');
        $this->addSql('ALTER TABLE category_data ADD CONSTRAINT FK_26688924232D562B FOREIGN KEY (object_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article ADD content LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE category_data DROP FOREIGN KEY FK_26688924232D562B');
        $this->addSql('ALTER TABLE category_data ADD CONSTRAINT FK_26688924232D562B FOREIGN KEY (object_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
    }
}
