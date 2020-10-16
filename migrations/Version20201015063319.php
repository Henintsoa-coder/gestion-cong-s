<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201015063319 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE absence ADD utilisateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE absence ADD CONSTRAINT FK_765AE0C9FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_765AE0C9FB88E14F ON absence (utilisateur_id)');
        $this->addSql('ALTER TABLE conge ADD utilisateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE conge ADD CONSTRAINT FK_2ED89348FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_2ED89348FB88E14F ON conge (utilisateur_id)');
        $this->addSql('ALTER TABLE permission ADD utilisateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE permission ADD CONSTRAINT FK_E04992AAFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_E04992AAFB88E14F ON permission (utilisateur_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE absence DROP FOREIGN KEY FK_765AE0C9FB88E14F');
        $this->addSql('DROP INDEX IDX_765AE0C9FB88E14F ON absence');
        $this->addSql('ALTER TABLE absence DROP utilisateur_id');
        $this->addSql('ALTER TABLE conge DROP FOREIGN KEY FK_2ED89348FB88E14F');
        $this->addSql('DROP INDEX IDX_2ED89348FB88E14F ON conge');
        $this->addSql('ALTER TABLE conge DROP utilisateur_id');
        $this->addSql('ALTER TABLE permission DROP FOREIGN KEY FK_E04992AAFB88E14F');
        $this->addSql('DROP INDEX IDX_E04992AAFB88E14F ON permission');
        $this->addSql('ALTER TABLE permission DROP utilisateur_id');
    }
}
