<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230704134650 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C197E3DD86');
        $this->addSql('DROP INDEX UNIQ_64C19C197E3DD86 ON category');
        $this->addSql('ALTER TABLE category DROP seo_id');
        $this->addSql('ALTER TABLE seo ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE seo ADD CONSTRAINT FK_6C71EC3012469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_6C71EC3012469DE2 ON seo (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category ADD seo_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C197E3DD86 FOREIGN KEY (seo_id) REFERENCES seo (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19C197E3DD86 ON category (seo_id)');
        $this->addSql('ALTER TABLE seo DROP FOREIGN KEY FK_6C71EC3012469DE2');
        $this->addSql('DROP INDEX IDX_6C71EC3012469DE2 ON seo');
        $this->addSql('ALTER TABLE seo DROP category_id');
    }
}
