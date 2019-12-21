<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191221163537 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A6BB0CC12');
        $this->addSql('DROP INDEX IDX_5387574A6BB0CC12 ON events');
        $this->addSql('ALTER TABLE events CHANGE id_createur_id createur_id INT NOT NULL');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A73A201E5 FOREIGN KEY (createur_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_5387574A73A201E5 ON events (createur_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A73A201E5');
        $this->addSql('DROP INDEX IDX_5387574A73A201E5 ON events');
        $this->addSql('ALTER TABLE events CHANGE createur_id id_createur_id INT NOT NULL');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A6BB0CC12 FOREIGN KEY (id_createur_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_5387574A6BB0CC12 ON events (id_createur_id)');
    }
}
