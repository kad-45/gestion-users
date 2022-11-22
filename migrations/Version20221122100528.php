<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221122100528 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE job (id INT AUTO_INCREMENT NOT NULL, designation VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE personne_hobies (personne_id INT NOT NULL, hobies_id INT NOT NULL, INDEX IDX_7DD284A6A21BD112 (personne_id), INDEX IDX_7DD284A68AF36D36 (hobies_id), PRIMARY KEY(personne_id, hobies_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profil (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) NOT NULL, rs VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE personne_hobies ADD CONSTRAINT FK_7DD284A6A21BD112 FOREIGN KEY (personne_id) REFERENCES personne (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE personne_hobies ADD CONSTRAINT FK_7DD284A68AF36D36 FOREIGN KEY (hobies_id) REFERENCES hobies (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE personne ADD profile_id INT DEFAULT NULL, ADD job_id INT DEFAULT NULL, DROP job');
        $this->addSql('ALTER TABLE personne ADD CONSTRAINT FK_FCEC9EFCCFA12B8 FOREIGN KEY (profile_id) REFERENCES profil (id)');
        $this->addSql('ALTER TABLE personne ADD CONSTRAINT FK_FCEC9EFBE04EA9 FOREIGN KEY (job_id) REFERENCES job (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FCEC9EFCCFA12B8 ON personne (profile_id)');
        $this->addSql('CREATE INDEX IDX_FCEC9EFBE04EA9 ON personne (job_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE personne DROP FOREIGN KEY FK_FCEC9EFBE04EA9');
        $this->addSql('ALTER TABLE personne DROP FOREIGN KEY FK_FCEC9EFCCFA12B8');
        $this->addSql('ALTER TABLE personne_hobies DROP FOREIGN KEY FK_7DD284A6A21BD112');
        $this->addSql('ALTER TABLE personne_hobies DROP FOREIGN KEY FK_7DD284A68AF36D36');
        $this->addSql('DROP TABLE job');
        $this->addSql('DROP TABLE personne_hobies');
        $this->addSql('DROP TABLE profil');
        $this->addSql('DROP INDEX UNIQ_FCEC9EFCCFA12B8 ON personne');
        $this->addSql('DROP INDEX IDX_FCEC9EFBE04EA9 ON personne');
        $this->addSql('ALTER TABLE personne ADD job VARCHAR(50) DEFAULT NULL, DROP profile_id, DROP job_id');
    }
}
