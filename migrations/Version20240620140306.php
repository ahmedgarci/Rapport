<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240620140306 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dbsource (id INT AUTO_INCREMENT NOT NULL, client_id_id INT DEFAULT NULL, driver VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, host VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, db VARCHAR(255) NOT NULL, INDEX IDX_C3E2F234DC2902E0 (client_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE dbsource ADD CONSTRAINT FK_C3E2F234DC2902E0 FOREIGN KEY (client_id_id) REFERENCES clients (id)');
        $this->addSql('ALTER TABLE rapports ADD report_path VARCHAR(255) NOT NULL, DROP content');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dbsource DROP FOREIGN KEY FK_C3E2F234DC2902E0');
        $this->addSql('DROP TABLE dbsource');
        $this->addSql('ALTER TABLE rapports ADD content LONGBLOB DEFAULT NULL, DROP report_path');
    }
}
