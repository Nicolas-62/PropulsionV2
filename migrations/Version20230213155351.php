<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230213155351 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mediaspecs ADD article_id INT DEFAULT NULL, ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mediaspecs ADD CONSTRAINT FK_44CD737F7294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE mediaspecs ADD CONSTRAINT FK_44CD737F12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_44CD737F7294869C ON mediaspecs (article_id)');
        $this->addSql('CREATE INDEX IDX_44CD737F12469DE2 ON mediaspecs (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mediaspecs DROP FOREIGN KEY FK_44CD737F7294869C');
        $this->addSql('ALTER TABLE mediaspecs DROP FOREIGN KEY FK_44CD737F12469DE2');
        $this->addSql('DROP INDEX IDX_44CD737F7294869C ON mediaspecs');
        $this->addSql('DROP INDEX IDX_44CD737F12469DE2 ON mediaspecs');
        $this->addSql('ALTER TABLE mediaspecs DROP article_id, DROP category_id');
    }
}
