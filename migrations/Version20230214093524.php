<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230214093524 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE onlines ADD category_id INT NOT NULL');
        $this->addSql('ALTER TABLE onlines ADD CONSTRAINT FK_5B4FF6D312469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_5B4FF6D312469DE2 ON onlines (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE onlines DROP FOREIGN KEY FK_5B4FF6D312469DE2');
        $this->addSql('DROP INDEX IDX_5B4FF6D312469DE2 ON onlines');
        $this->addSql('ALTER TABLE onlines DROP category_id');
    }
}
