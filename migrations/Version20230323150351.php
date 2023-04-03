<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230323150351 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, article_id INT DEFAULT NULL, category_id INT DEFAULT NULL, content LONGTEXT DEFAULT NULL, title VARCHAR(255) NOT NULL, ordre INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_23A0E667294869C (article_id), INDEX IDX_23A0E6612469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, can_create TINYINT(1) NOT NULL, has_multi TINYINT(1) NOT NULL, has_theme TINYINT(1) NOT NULL, has_title TINYINT(1) NOT NULL, has_sub_title TINYINT(1) NOT NULL, has_content TINYINT(1) NOT NULL, has_seo TINYINT(1) NOT NULL, has_link TINYINT(1) NOT NULL, title VARCHAR(255) NOT NULL, ordre INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_64C19C112469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE langues (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, ordre INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, media VARCHAR(255) DEFAULT NULL, legend VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media_link (id INT AUTO_INCREMENT NOT NULL, mediaspec_id INT DEFAULT NULL, article_id INT DEFAULT NULL, category_id INT DEFAULT NULL, media_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_F5EB4622EFC5D9A6 (mediaspec_id), INDEX IDX_F5EB46227294869C (article_id), INDEX IDX_F5EB462212469DE2 (category_id), INDEX IDX_F5EB4622EA9FDD75 (media_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media_type (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, filetype VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mediaspec (id INT AUTO_INCREMENT NOT NULL, article_id INT DEFAULT NULL, category_id INT DEFAULT NULL, media_type_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, width INT NOT NULL, height INT NOT NULL, mandatory TINYINT(1) NOT NULL, haslegend TINYINT(1) NOT NULL, heritage INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_C6347C397294869C (article_id), INDEX IDX_C6347C3912469DE2 (category_id), INDEX IDX_C6347C39A49B0ADA (media_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE online (id INT AUTO_INCREMENT NOT NULL, langue_id INT NOT NULL, article_id INT DEFAULT NULL, category_id INT DEFAULT NULL, online TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9E32BEEA2AADBACD (langue_id), INDEX IDX_9E32BEEA7294869C (article_id), INDEX IDX_9E32BEEA12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E667294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E6612469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C112469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE media_link ADD CONSTRAINT FK_F5EB4622EFC5D9A6 FOREIGN KEY (mediaspec_id) REFERENCES mediaspec (id)');
        $this->addSql('ALTER TABLE media_link ADD CONSTRAINT FK_F5EB46227294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE media_link ADD CONSTRAINT FK_F5EB462212469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE media_link ADD CONSTRAINT FK_F5EB4622EA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id)');
        $this->addSql('ALTER TABLE mediaspec ADD CONSTRAINT FK_C6347C397294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE mediaspec ADD CONSTRAINT FK_C6347C3912469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE mediaspec ADD CONSTRAINT FK_C6347C39A49B0ADA FOREIGN KEY (media_type_id) REFERENCES media_type (id)');
        $this->addSql('ALTER TABLE online ADD CONSTRAINT FK_9E32BEEA2AADBACD FOREIGN KEY (langue_id) REFERENCES langues (id)');
        $this->addSql('ALTER TABLE online ADD CONSTRAINT FK_9E32BEEA7294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE online ADD CONSTRAINT FK_9E32BEEA12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E667294869C');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E6612469DE2');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C112469DE2');
        $this->addSql('ALTER TABLE media_link DROP FOREIGN KEY FK_F5EB4622EFC5D9A6');
        $this->addSql('ALTER TABLE media_link DROP FOREIGN KEY FK_F5EB46227294869C');
        $this->addSql('ALTER TABLE media_link DROP FOREIGN KEY FK_F5EB462212469DE2');
        $this->addSql('ALTER TABLE media_link DROP FOREIGN KEY FK_F5EB4622EA9FDD75');
        $this->addSql('ALTER TABLE mediaspec DROP FOREIGN KEY FK_C6347C397294869C');
        $this->addSql('ALTER TABLE mediaspec DROP FOREIGN KEY FK_C6347C3912469DE2');
        $this->addSql('ALTER TABLE mediaspec DROP FOREIGN KEY FK_C6347C39A49B0ADA');
        $this->addSql('ALTER TABLE online DROP FOREIGN KEY FK_9E32BEEA2AADBACD');
        $this->addSql('ALTER TABLE online DROP FOREIGN KEY FK_9E32BEEA7294869C');
        $this->addSql('ALTER TABLE online DROP FOREIGN KEY FK_9E32BEEA12469DE2');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE langues');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE media_link');
        $this->addSql('DROP TABLE media_type');
        $this->addSql('DROP TABLE mediaspec');
        $this->addSql('DROP TABLE online');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
