<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230706225748 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category_data (id INT AUTO_INCREMENT NOT NULL, language_id INT DEFAULT NULL, object_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', field_key VARCHAR(255) NOT NULL, field_value LONGTEXT NOT NULL, INDEX IDX_2668892482F1BAF4 (language_id), INDEX IDX_26688924232D562B (object_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category_data ADD CONSTRAINT FK_2668892482F1BAF4 FOREIGN KEY (language_id) REFERENCES language (id)');
        $this->addSql('ALTER TABLE category_data ADD CONSTRAINT FK_26688924232D562B FOREIGN KEY (object_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE online DROP FOREIGN KEY FK_9E32BEEA2AADBACD');
        $this->addSql('DROP INDEX IDX_9E32BEEA2AADBACD ON online');
        $this->addSql('ALTER TABLE online CHANGE langue_id language_id INT NOT NULL');
        $this->addSql('ALTER TABLE online ADD CONSTRAINT FK_9E32BEEA82F1BAF4 FOREIGN KEY (language_id) REFERENCES language (id)');
        $this->addSql('CREATE INDEX IDX_9E32BEEA82F1BAF4 ON online (language_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category_data DROP FOREIGN KEY FK_2668892482F1BAF4');
        $this->addSql('ALTER TABLE category_data DROP FOREIGN KEY FK_26688924232D562B');
        $this->addSql('DROP TABLE category_data');
        $this->addSql('ALTER TABLE online DROP FOREIGN KEY FK_9E32BEEA82F1BAF4');
        $this->addSql('DROP INDEX IDX_9E32BEEA82F1BAF4 ON online');
        $this->addSql('ALTER TABLE online CHANGE language_id langue_id INT NOT NULL');
        $this->addSql('ALTER TABLE online ADD CONSTRAINT FK_9E32BEEA2AADBACD FOREIGN KEY (langue_id) REFERENCES language (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_9E32BEEA2AADBACD ON online (langue_id)');
    }
}
