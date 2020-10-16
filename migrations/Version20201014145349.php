<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201014145349 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE absence DROP FOREIGN KEY FK_765AE0C9217BBB47');
        $this->addSql('ALTER TABLE conge DROP FOREIGN KEY FK_2ED89348217BBB47');
        $this->addSql('ALTER TABLE permission DROP FOREIGN KEY FK_E04992AA217BBB47');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP INDEX IDX_765AE0C9217BBB47 ON absence');
        $this->addSql('ALTER TABLE absence DROP person_id');
        $this->addSql('DROP INDEX IDX_2ED89348217BBB47 ON conge');
        $this->addSql('ALTER TABLE conge DROP person_id');
        $this->addSql('DROP INDEX IDX_E04992AA217BBB47 ON permission');
        $this->addSql('ALTER TABLE permission DROP person_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE person (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, prenom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, fonction VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, email VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, telephone VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, nb_conges INT DEFAULT NULL, nb_permissions INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE absence ADD person_id INT NOT NULL');
        $this->addSql('ALTER TABLE absence ADD CONSTRAINT FK_765AE0C9217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('CREATE INDEX IDX_765AE0C9217BBB47 ON absence (person_id)');
        $this->addSql('ALTER TABLE conge ADD person_id INT NOT NULL');
        $this->addSql('ALTER TABLE conge ADD CONSTRAINT FK_2ED89348217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('CREATE INDEX IDX_2ED89348217BBB47 ON conge (person_id)');
        $this->addSql('ALTER TABLE permission ADD person_id INT NOT NULL');
        $this->addSql('ALTER TABLE permission ADD CONSTRAINT FK_E04992AA217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('CREATE INDEX IDX_E04992AA217BBB47 ON permission (person_id)');
    }
}
