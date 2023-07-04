<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230703145720 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE seo (id INT AUTO_INCREMENT NOT NULL, langue_id INT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_6C71EC302AADBACD (langue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE seo ADD CONSTRAINT FK_6C71EC302AADBACD FOREIGN KEY (langue_id) REFERENCES language (id)');
        $this->addSql('ALTER TABLE category ADD seo_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C197E3DD86 FOREIGN KEY (seo_id) REFERENCES seo (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19C197E3DD86 ON category (seo_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C197E3DD86');
        $this->addSql('ALTER TABLE seo DROP FOREIGN KEY FK_6C71EC302AADBACD');
        $this->addSql('DROP TABLE seo');
        $this->addSql('DROP INDEX UNIQ_64C19C197E3DD86 ON category');
        $this->addSql('ALTER TABLE category DROP seo_id');
    }
}
