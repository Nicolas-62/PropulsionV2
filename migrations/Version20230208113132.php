<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230208113132 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media ADD article_id INT DEFAULT NULL, ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C7294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6A2CA10C7294869C ON media (article_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6A2CA10C12469DE2 ON media (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C7294869C');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C12469DE2');
        $this->addSql('DROP INDEX UNIQ_6A2CA10C7294869C ON media');
        $this->addSql('DROP INDEX UNIQ_6A2CA10C12469DE2 ON media');
        $this->addSql('ALTER TABLE media DROP article_id, DROP category_id');
    }
}
