<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191223143525 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categories_events (categories_id INT NOT NULL, events_id INT NOT NULL, INDEX IDX_5BEE6972A21214B7 (categories_id), INDEX IDX_5BEE69729D6A1065 (events_id), PRIMARY KEY(categories_id, events_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE events (id INT AUTO_INCREMENT NOT NULL, createur_id INT NOT NULL, name VARCHAR(255) NOT NULL, date DATETIME NOT NULL, city VARCHAR(255) NOT NULL, nb_participant INT DEFAULT NULL, url_image VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, INDEX IDX_5387574A73A201E5 (createur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE events_users (events_id INT NOT NULL, users_id INT NOT NULL, INDEX IDX_A43F6DCF9D6A1065 (events_id), INDEX IDX_A43F6DCF67B3B43D (users_id), PRIMARY KEY(events_id, users_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, surname VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, mail VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE categories_events ADD CONSTRAINT FK_5BEE6972A21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE categories_events ADD CONSTRAINT FK_5BEE69729D6A1065 FOREIGN KEY (events_id) REFERENCES events (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A73A201E5 FOREIGN KEY (createur_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE events_users ADD CONSTRAINT FK_A43F6DCF9D6A1065 FOREIGN KEY (events_id) REFERENCES events (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE events_users ADD CONSTRAINT FK_A43F6DCF67B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE categories_events DROP FOREIGN KEY FK_5BEE6972A21214B7');
        $this->addSql('ALTER TABLE categories_events DROP FOREIGN KEY FK_5BEE69729D6A1065');
        $this->addSql('ALTER TABLE events_users DROP FOREIGN KEY FK_A43F6DCF9D6A1065');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A73A201E5');
        $this->addSql('ALTER TABLE events_users DROP FOREIGN KEY FK_A43F6DCF67B3B43D');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE categories_events');
        $this->addSql('DROP TABLE events');
        $this->addSql('DROP TABLE events_users');
        $this->addSql('DROP TABLE users');
    }
}
