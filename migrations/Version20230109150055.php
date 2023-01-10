<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230109150055 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking ADD id_client_id INT NOT NULL, ADD description LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE99DED506 FOREIGN KEY (id_client_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDE99DED506 ON booking (id_client_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE99DED506');
        $this->addSql('DROP INDEX IDX_E00CEDDE99DED506 ON booking');
        $this->addSql('ALTER TABLE booking DROP id_client_id, DROP description');
    }
}
