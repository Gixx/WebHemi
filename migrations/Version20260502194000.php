<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260502194000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add avatar type and optional avatar path to app_user.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            "ALTER TABLE app_user ADD avatar_type VARCHAR(16) NOT NULL DEFAULT 'default', "
            . 'ADD avatar_path VARCHAR(255) DEFAULT NULL',
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE app_user DROP avatar_type, DROP avatar_path');
    }
}
