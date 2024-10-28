<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241020082453 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE matiere DROP FOREIGN KEY FK_9014574ABAB22EE9');
        $this->addSql('DROP INDEX IDX_9014574ABAB22EE9 ON matiere');
        $this->addSql('ALTER TABLE matiere DROP professeur_id');
        $this->addSql('ALTER TABLE professeur ADD matiere_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE professeur ADD CONSTRAINT FK_17A55299F46CD258 FOREIGN KEY (matiere_id) REFERENCES matiere (id)');
        $this->addSql('CREATE INDEX IDX_17A55299F46CD258 ON professeur (matiere_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE matiere ADD professeur_id INT NOT NULL');
        $this->addSql('ALTER TABLE matiere ADD CONSTRAINT FK_9014574ABAB22EE9 FOREIGN KEY (professeur_id) REFERENCES professeur (id)');
        $this->addSql('CREATE INDEX IDX_9014574ABAB22EE9 ON matiere (professeur_id)');
        $this->addSql('ALTER TABLE professeur DROP FOREIGN KEY FK_17A55299F46CD258');
        $this->addSql('DROP INDEX IDX_17A55299F46CD258 ON professeur');
        $this->addSql('ALTER TABLE professeur DROP matiere_id');
    }
}
