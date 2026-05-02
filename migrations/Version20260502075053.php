<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260502075053 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE site (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(64) NOT NULL, name VARCHAR(128) NOT NULL, is_enabled TINYINT NOT NULL, UNIQUE INDEX uniq_site_slug (slug), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE site_host (id INT AUTO_INCREMENT NOT NULL, host VARCHAR(191) NOT NULL, surface VARCHAR(16) NOT NULL, is_active TINYINT NOT NULL, site_id INT NOT NULL, INDEX IDX_F4BDAE04F6BD1646 (site_id), UNIQUE INDEX uniq_site_host_host (host), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE site_host ADD CONSTRAINT FK_F4BDAE04F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE site_host DROP FOREIGN KEY FK_F4BDAE04F6BD1646');
        $this->addSql('DROP TABLE site');
        $this->addSql('DROP TABLE site_host');
    }
}
