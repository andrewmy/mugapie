<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @codeCoverageIgnore
 */
final class Version20200205235823 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create initial schema';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql(
            'CREATE TABLE order_items ('
            . 'id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\', '
            . 'order_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', '
            . 'product_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', '
            . 'incremental_id INT UNSIGNED AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, '
            . 'title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, '
            . 'sku VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, '
            . 'units INT UNSIGNED NOT NULL, '
            . 'type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, '
            . 'price_per_unit_amount VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, '
            . 'price_per_unit_currency_code CHAR(3) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, '
            . 'INDEX IDX_62809DB04584665A (product_id), '
            . 'INDEX IDX_62809DB08D9F6D38 (order_id), '
            . 'UNIQUE INDEX UNIQ_62809DB0FBD6FF99 (incremental_id), '
            . 'PRIMARY KEY(id)) '
            . 'DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' '
        );

        $this->addSql(
            'CREATE TABLE orders ('
            . 'id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\', '
            . 'user_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', '
            . 'incremental_id INT UNSIGNED AUTO_INCREMENT NOT NULL, '
            . 'created_at DATETIME NOT NULL, '
            . 'updated_at DATETIME NOT NULL, '
            . 'shipping_type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, '
            . 'shipping_address_country_code VARCHAR(2) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, '
            . 'shipping_address_region VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, '
            . 'shipping_address_city VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, '
            . 'shipping_address_street VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, '
            . 'shipping_address_address LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, '
            . 'shipping_address_zip VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, '
            . 'shipping_address_phone VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, '
            . 'shipping_address_full_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, '
            . 'order_cost_amount VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, '
            . 'order_cost_currency_code CHAR(3) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, '
            . 'status VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, '
            . 'INDEX IDX_E52FFDEEA76ED395 (user_id), '
            . 'UNIQUE INDEX UNIQ_E52FFDEEFBD6FF99 (incremental_id), PRIMARY KEY(id)) '
            . 'DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' '
        );

        $this->addSql(
            'CREATE TABLE products ('
            . 'id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\', '
            . 'user_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', '
            . 'incremental_id INT UNSIGNED AUTO_INCREMENT NOT NULL, '
            . 'created_at DATETIME NOT NULL, '
            . 'updated_at DATETIME NOT NULL, '
            . 'title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, '
            . 'sku VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, '
            . 'type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, '
            . 'cost_amount VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, '
            . 'cost_currency_code CHAR(3) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, '
            . 'INDEX IDX_B3BA5A5AA76ED395 (user_id), '
            . 'UNIQUE INDEX UNIQ_B3BA5A5AF9038C4 (sku), '
            . 'UNIQUE INDEX UNIQ_B3BA5A5AFBD6FF99 (incremental_id), PRIMARY KEY(id)) '
            . 'DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' '
        );

        $this->addSql(
            'CREATE TABLE transactions ('
            . 'id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\', '
            . 'user_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', '
            . 'order_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid_binary)\', '
            . 'incremental_id INT UNSIGNED AUTO_INCREMENT NOT NULL, '
            . 'created_at DATETIME NOT NULL, '
            . 'amount_amount VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, '
            . 'amount_currency_code CHAR(3) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, '
            . 'INDEX IDX_EAA81A4C8D9F6D38 (order_id), '
            . 'INDEX IDX_EAA81A4CA76ED395 (user_id), '
            . 'UNIQUE INDEX UNIQ_EAA81A4CFBD6FF99 (incremental_id), PRIMARY KEY(id)) '
            . 'DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' '
        );

        $this->addSql(
            'CREATE TABLE users ('
            . 'id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid_binary)\', '
            . 'created_at DATETIME NOT NULL, '
            . 'updated_at DATETIME NOT NULL, '
            . 'nickname VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, '
            . 'balance_amount VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, '
            . 'balance_currency_code CHAR(3) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, '
            . 'PRIMARY KEY(id)) '
            . 'DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' '
        );
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('DROP TABLE order_items');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE transactions');
        $this->addSql('DROP TABLE users');
    }
}
