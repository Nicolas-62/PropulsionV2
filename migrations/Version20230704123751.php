<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230704123751 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE seo ADD article_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE seo ADD CONSTRAINT FK_6C71EC307294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('CREATE INDEX IDX_6C71EC307294869C ON seo (article_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE seo DROP FOREIGN KEY FK_6C71EC307294869C');
        $this->addSql('DROP INDEX IDX_6C71EC307294869C ON seo');
        $this->addSql('ALTER TABLE seo DROP article_id');
    }
}
