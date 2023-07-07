<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230706161729 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE seo DROP FOREIGN KEY FK_6C71EC302AADBACD');
        $this->addSql('DROP INDEX IDX_6C71EC302AADBACD ON seo');
        $this->addSql('ALTER TABLE seo CHANGE langue_id language_id INT NOT NULL');
        $this->addSql('ALTER TABLE seo ADD CONSTRAINT FK_6C71EC3082F1BAF4 FOREIGN KEY (language_id) REFERENCES language (id)');
        $this->addSql('CREATE INDEX IDX_6C71EC3082F1BAF4 ON seo (language_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE seo DROP FOREIGN KEY FK_6C71EC3082F1BAF4');
        $this->addSql('DROP INDEX IDX_6C71EC3082F1BAF4 ON seo');
        $this->addSql('ALTER TABLE seo CHANGE language_id langue_id INT NOT NULL');
        $this->addSql('ALTER TABLE seo ADD CONSTRAINT FK_6C71EC302AADBACD FOREIGN KEY (langue_id) REFERENCES language (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_6C71EC302AADBACD ON seo (langue_id)');
    }
}
