<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230213160357 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mediaspecs ADD media_type_id INT NOT NULL');
        $this->addSql('ALTER TABLE mediaspecs ADD CONSTRAINT FK_44CD737FA49B0ADA FOREIGN KEY (media_type_id) REFERENCES medias_types (id)');
        $this->addSql('CREATE INDEX IDX_44CD737FA49B0ADA ON mediaspecs (media_type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mediaspecs DROP FOREIGN KEY FK_44CD737FA49B0ADA');
        $this->addSql('DROP INDEX IDX_44CD737FA49B0ADA ON mediaspecs');
        $this->addSql('ALTER TABLE mediaspecs DROP media_type_id');
    }
}
