<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230214092745 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE onlines (id INT AUTO_INCREMENT NOT NULL, langue_id INT NOT NULL, online TINYINT(1) NOT NULL, date_creation DATE NOT NULL, date_modification DATE NOT NULL, INDEX IDX_5B4FF6D32AADBACD (langue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE onlines ADD CONSTRAINT FK_5B4FF6D32AADBACD FOREIGN KEY (langue_id) REFERENCES langues (id)');
        $this->addSql('ALTER TABLE article ADD onlines_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E664D0B5438 FOREIGN KEY (onlines_id) REFERENCES onlines (id)');
        $this->addSql('CREATE INDEX IDX_23A0E664D0B5438 ON article (onlines_id)');
        $this->addSql('ALTER TABLE category ADD onlines_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C14D0B5438 FOREIGN KEY (onlines_id) REFERENCES onlines (id)');
        $this->addSql('CREATE INDEX IDX_64C19C14D0B5438 ON category (onlines_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E664D0B5438');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C14D0B5438');
        $this->addSql('ALTER TABLE onlines DROP FOREIGN KEY FK_5B4FF6D32AADBACD');
        $this->addSql('DROP TABLE onlines');
        $this->addSql('DROP INDEX IDX_23A0E664D0B5438 ON article');
        $this->addSql('ALTER TABLE article DROP onlines_id');
        $this->addSql('DROP INDEX IDX_64C19C14D0B5438 ON category');
        $this->addSql('ALTER TABLE category DROP onlines_id');
    }
}
