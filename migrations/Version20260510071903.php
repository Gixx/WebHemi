<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260510071903 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add site_assignment table for site-scoped RBAC (user + site + role)';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE site_assignment (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, site_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_10CCCB7FA76ED395 (user_id), INDEX IDX_10CCCB7FF6BD1646 (site_id), INDEX IDX_10CCCB7FD60322AC (role_id), UNIQUE INDEX uniq_site_assignment_user_site (user_id, site_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE site_assignment ADD CONSTRAINT FK_10CCCB7FA76ED395 FOREIGN KEY (user_id) REFERENCES app_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE site_assignment ADD CONSTRAINT FK_10CCCB7FF6BD1646 FOREIGN KEY (site_id) REFERENCES site (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE site_assignment ADD CONSTRAINT FK_10CCCB7FD60322AC FOREIGN KEY (role_id) REFERENCES rbac_role (id)');
        $this->addSql('ALTER TABLE app_user CHANGE avatar_type avatar_type VARCHAR(16) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE site_assignment DROP FOREIGN KEY FK_10CCCB7FA76ED395');
        $this->addSql('ALTER TABLE site_assignment DROP FOREIGN KEY FK_10CCCB7FF6BD1646');
        $this->addSql('ALTER TABLE site_assignment DROP FOREIGN KEY FK_10CCCB7FD60322AC');
        $this->addSql('DROP TABLE site_assignment');
        $this->addSql('ALTER TABLE app_user CHANGE avatar_type avatar_type VARCHAR(16) DEFAULT \'default\' NOT NULL');
    }
}
