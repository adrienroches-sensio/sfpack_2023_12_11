<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231213133110 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Movie] Add rated column.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE movie ADD COLUMN rated VARCHAR(6) DEFAULT \'G\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__movie AS SELECT id, slug, title, plot, released_at, poster FROM movie');
        $this->addSql('DROP TABLE movie');
        $this->addSql('CREATE TABLE movie (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, slug VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, plot CLOB NOT NULL, released_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , poster VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO movie (id, slug, title, plot, released_at, poster) SELECT id, slug, title, plot, released_at, poster FROM __temp__movie');
        $this->addSql('DROP TABLE __temp__movie');
        $this->addSql('CREATE UNIQUE INDEX movie_unique_slug ON movie (slug)');
    }
}
