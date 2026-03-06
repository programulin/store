<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260306152450 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create products table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
            CREATE TABLE product (
                id BIGINT NOT NULL,
                name VARCHAR(255) NOT NULL,
                description TEXT DEFAULT NULL,
                cost INT NOT NULL,
                tax INT NOT NULL,
                version INT DEFAULT 1 NOT NULL,
                measurements_weight INT NOT NULL,
                measurements_height INT NOT NULL,
                measurements_width INT NOT NULL,
                measurements_length INT NOT NULL,
                PRIMARY KEY (id))
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE product');
    }
}
