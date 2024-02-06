<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240130120905 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments RENAME INDEX idx_5f9e962a67b3b43d TO IDX_5F9E962AA76ED395');
        $this->addSql('ALTER TABLE medias ADD tricks_id INT DEFAULT NULL, ADD media VARCHAR(120) NOT NULL, ADD type VARCHAR(10) NOT NULL, DROP picture, DROP video');
        $this->addSql('ALTER TABLE medias ADD CONSTRAINT FK_12D2AF813B153154 FOREIGN KEY (tricks_id) REFERENCES tricks (id)');
        $this->addSql('CREATE INDEX IDX_12D2AF813B153154 ON medias (tricks_id)');
        $this->addSql('ALTER TABLE tricks DROP FOREIGN KEY FK_E1D902C1C7F4A74B');
        $this->addSql('DROP INDEX IDX_E1D902C1C7F4A74B ON tricks');
        $this->addSql('ALTER TABLE tricks DROP medias_id');
        $this->addSql('ALTER TABLE users CHANGE avatar avatar VARCHAR(120) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE medias DROP FOREIGN KEY FK_12D2AF813B153154');
        $this->addSql('DROP INDEX IDX_12D2AF813B153154 ON medias');
        $this->addSql('ALTER TABLE medias ADD video VARCHAR(120) NOT NULL, DROP tricks_id, DROP type, CHANGE media picture VARCHAR(120) NOT NULL');
        $this->addSql('ALTER TABLE comments RENAME INDEX idx_5f9e962aa76ed395 TO IDX_5F9E962A67B3B43D');
        $this->addSql('DROP INDEX UNIQ_1483A5E9E7927C74 ON users');
        $this->addSql('ALTER TABLE users CHANGE avatar avatar VARCHAR(120) DEFAULT NULL');
        $this->addSql('ALTER TABLE tricks ADD medias_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tricks ADD CONSTRAINT FK_E1D902C1C7F4A74B FOREIGN KEY (medias_id) REFERENCES medias (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_E1D902C1C7F4A74B ON tricks (medias_id)');
    }
}
