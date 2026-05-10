<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260510075903 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Seed ROLE_SITE_ADMIN as a read-only system role with site-scoped permissions';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            INSERT IGNORE INTO rbac_role (name, label, is_read_only)
            VALUES ('ROLE_SITE_ADMIN', 'Site Administrator', 1)
        SQL);

        $this->addSql(<<<'SQL'
            INSERT IGNORE INTO role_permission (role_id, permission_id)
            SELECT r.id, p.id
            FROM rbac_role r, rbac_permission p
            WHERE r.name = 'ROLE_SITE_ADMIN'
              AND p.name IN ('dashboard.view', 'site.list', 'site.view', 'site.edit')
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            DELETE rp FROM role_permission rp
            JOIN rbac_role r ON r.id = rp.role_id
            WHERE r.name = 'ROLE_SITE_ADMIN'
        SQL);

        $this->addSql(<<<'SQL'
            DELETE FROM rbac_role WHERE name = 'ROLE_SITE_ADMIN'
        SQL);
    }
}
