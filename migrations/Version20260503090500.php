<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260503090500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add read-only flags for roles and permissions, and ensure built-in ROLE_ADMIN is protected.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE rbac_role ADD is_read_only TINYINT(1) NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE rbac_permission ADD is_read_only TINYINT(1) NOT NULL DEFAULT 0');

        $this->addSql(
            "INSERT INTO rbac_role (name, label, is_read_only)\n"
            . "SELECT 'ROLE_ADMIN', 'Administrator', 1\n"
            . "WHERE NOT EXISTS (SELECT 1 FROM rbac_role WHERE name = 'ROLE_ADMIN')",
        );

        $this->addSql("UPDATE rbac_role SET is_read_only = 1 WHERE name = 'ROLE_ADMIN'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE rbac_role DROP is_read_only');
        $this->addSql('ALTER TABLE rbac_permission DROP is_read_only');
    }
}
