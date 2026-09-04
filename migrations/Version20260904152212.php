<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260904152212 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $hashedPassword = '$2y$13$9mlPP4f8tJZK55JhRbNJauRcLCtFpOWvfHXKZ7ieYsrexQ5XWf5Dy';

        $this->addSql(sprintf(
            "UPDATE users SET password = '%s' WHERE email IN ('user@demo.com', 'demo@user.com')",
            $hashedPassword
        ));
    }

    public function down(Schema $schema): void
    {
        $this->addSql("UPDATE users SET password = '12345678' WHERE email IN ('user@demo.com', 'demo@user.com')");
    }
}
