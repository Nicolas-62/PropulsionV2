<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230214093401 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE onlines ADD article_id INT NOT NULL');
        $this->addSql('ALTER TABLE onlines ADD CONSTRAINT FK_5B4FF6D37294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('CREATE INDEX IDX_5B4FF6D37294869C ON onlines (article_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE onlines DROP FOREIGN KEY FK_5B4FF6D37294869C');
        $this->addSql('DROP INDEX IDX_5B4FF6D37294869C ON onlines');
        $this->addSql('ALTER TABLE onlines DROP article_id');
    }
}
