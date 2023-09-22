<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230922183959 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE race (id UUID NOT NULL, title VARCHAR(255) NOT NULL, race_date DATE NOT NULL, average_finish_time_for_long_distance INT DEFAULT 0 NOT NULL, average_finish_time_for_medium_distance INT DEFAULT 0 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN race.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN race.race_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('CREATE TABLE race_results (id UUID NOT NULL, race_id UUID DEFAULT NULL, racer_full_name VARCHAR(255) NOT NULL, distance VARCHAR(255) NOT NULL, finish_time INT NOT NULL, age_category VARCHAR(255) NOT NULL, overall_placement INT DEFAULT NULL, age_category_placement INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_801331646E59D40D ON race_results (race_id)');
        $this->addSql('COMMENT ON COLUMN race_results.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN race_results.race_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE race_results ADD CONSTRAINT FK_801331646E59D40D FOREIGN KEY (race_id) REFERENCES race (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE race_results DROP CONSTRAINT FK_801331646E59D40D');
        $this->addSql('DROP TABLE race');
        $this->addSql('DROP TABLE race_results');
    }
}
