<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240614102616 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE data_source ADD file_name VARCHAR(255) DEFAULT NULL, ADD mime_type VARCHAR(255) DEFAULT NULL, DROP url');
        $this->addSql('ALTER TABLE rapports CHANGE title title VARCHAR(255) DEFAULT NULL, CHANGE date date DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE data_source ADD url VARCHAR(255) DEFAULT \'NULL\', DROP file_name, DROP mime_type');
        $this->addSql('ALTER TABLE rapports CHANGE title title VARCHAR(255) DEFAULT \'NULL\', CHANGE date date DATETIME DEFAULT \'NULL\'');
    }
}
