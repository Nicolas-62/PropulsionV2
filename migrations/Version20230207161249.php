<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230207161249 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, media_type_id_id INT NOT NULL, objet VARCHAR(255) NOT NULL, legende VARCHAR(255) DEFAULT NULL, fichier VARCHAR(255) NOT NULL, width INT DEFAULT NULL, height INT DEFAULT NULL, date_creation DATE NOT NULL, date_modification DATE NOT NULL, INDEX IDX_6A2CA10C7B6DD734 (media_type_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE medias_types (id INT AUTO_INCREMENT NOT NULL, libellé VARCHAR(255) NOT NULL, filetype VARCHAR(255) NOT NULL, date_creation DATE NOT NULL, date_modification DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C7B6DD734 FOREIGN KEY (media_type_id_id) REFERENCES medias_types (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C7B6DD734');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE medias_types');
    }
}
