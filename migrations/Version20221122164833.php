<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221122164833 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE personne DROP FOREIGN KEY FK_FCEC9EFCCFA12B8');
        $this->addSql('CREATE TABLE hobby (id INT AUTO_INCREMENT NOT NULL, designation VARCHAR(70) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE personne_hobby (personne_id INT NOT NULL, hobby_id INT NOT NULL, INDEX IDX_2D85E25EA21BD112 (personne_id), INDEX IDX_2D85E25E322B2123 (hobby_id), PRIMARY KEY(personne_id, hobby_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profile (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) NOT NULL, rs VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE personne_hobby ADD CONSTRAINT FK_2D85E25EA21BD112 FOREIGN KEY (personne_id) REFERENCES personne (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE personne_hobby ADD CONSTRAINT FK_2D85E25E322B2123 FOREIGN KEY (hobby_id) REFERENCES hobby (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE personne_hobies DROP FOREIGN KEY FK_7DD284A68AF36D36');
        $this->addSql('ALTER TABLE personne_hobies DROP FOREIGN KEY FK_7DD284A6A21BD112');
        $this->addSql('DROP TABLE hobies');
        $this->addSql('DROP TABLE personne_hobies');
        $this->addSql('DROP TABLE profil');
        $this->addSql('ALTER TABLE personne DROP INDEX FK_FCEC9EFCCFA12B8, ADD UNIQUE INDEX UNIQ_FCEC9EFCCFA12B8 (profile_id)');
        $this->addSql('ALTER TABLE personne DROP FOREIGN KEY FK_FCEC9EFCCFA12B8');
        $this->addSql('ALTER TABLE personne CHANGE profile_id profile_id INT NOT NULL');
        $this->addSql('ALTER TABLE personne ADD CONSTRAINT FK_FCEC9EFCCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE personne RENAME INDEX fk_fcec9efbe04ea9 TO IDX_FCEC9EFBE04EA9');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE personne DROP FOREIGN KEY FK_FCEC9EFCCFA12B8');
        $this->addSql('CREATE TABLE hobies (id INT AUTO_INCREMENT NOT NULL, designation VARCHAR(70) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE personne_hobies (personne_id INT NOT NULL, hobies_id INT NOT NULL, INDEX IDX_7DD284A6A21BD112 (personne_id), INDEX IDX_7DD284A68AF36D36 (hobies_id), PRIMARY KEY(personne_id, hobies_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE profil (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, rs VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE personne_hobies ADD CONSTRAINT FK_7DD284A68AF36D36 FOREIGN KEY (hobies_id) REFERENCES hobies (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE personne_hobies ADD CONSTRAINT FK_7DD284A6A21BD112 FOREIGN KEY (personne_id) REFERENCES personne (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE personne_hobby DROP FOREIGN KEY FK_2D85E25EA21BD112');
        $this->addSql('ALTER TABLE personne_hobby DROP FOREIGN KEY FK_2D85E25E322B2123');
        $this->addSql('DROP TABLE hobby');
        $this->addSql('DROP TABLE personne_hobby');
        $this->addSql('DROP TABLE profile');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE personne DROP INDEX UNIQ_FCEC9EFCCFA12B8, ADD INDEX FK_FCEC9EFCCFA12B8 (profile_id)');
        $this->addSql('ALTER TABLE personne DROP FOREIGN KEY FK_FCEC9EFCCFA12B8');
        $this->addSql('ALTER TABLE personne CHANGE profile_id profile_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE personne ADD CONSTRAINT FK_FCEC9EFCCFA12B8 FOREIGN KEY (profile_id) REFERENCES profil (id)');
        $this->addSql('ALTER TABLE personne RENAME INDEX idx_fcec9efbe04ea9 TO FK_FCEC9EFBE04EA9');
    }
}
