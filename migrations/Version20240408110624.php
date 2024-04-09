<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240408110624 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE config ADD email_billetterie VARCHAR(255) DEFAULT NULL, ADD email_comptabilite VARCHAR(255) NOT NULL, ADD email_partenariats VARCHAR(255) DEFAULT NULL, ADD email_communication VARCHAR(255) DEFAULT NULL, ADD email_projets VARCHAR(255) DEFAULT NULL, ADD email_programmation VARCHAR(255) DEFAULT NULL, ADD email_technique VARCHAR(255) DEFAULT NULL, ADD email_billetterie_object VARCHAR(255) DEFAULT NULL, ADD email_comptabilite_object VARCHAR(255) DEFAULT NULL, ADD email_partenariats_object VARCHAR(255) DEFAULT NULL, ADD email_communication_object VARCHAR(255) DEFAULT NULL, ADD email_projets_object VARCHAR(255) DEFAULT NULL, ADD email_programmation_object VARCHAR(255) DEFAULT NULL, ADD email_technique_object VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE config DROP email_billetterie, DROP email_comptabilite, DROP email_partenariats, DROP email_communication, DROP email_projets, DROP email_programmation, DROP email_technique, DROP email_billetterie_object, DROP email_comptabilite_object, DROP email_partenariats_object, DROP email_communication_object, DROP email_projets_object, DROP email_programmation_object, DROP email_technique_object');
    }
}
