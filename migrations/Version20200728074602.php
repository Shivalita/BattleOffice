<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200728074602 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client ADD country_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('CREATE INDEX IDX_C7440455F92F3E70 ON client (country_id)');
        $this->addSql('ALTER TABLE `order` ADD product_id INT DEFAULT NULL, ADD client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993984584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F529939819EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_F52993984584665A ON `order` (product_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F529939819EB6921 ON `order` (client_id)');
        $this->addSql('ALTER TABLE shipping ADD country_id INT DEFAULT NULL, ADD client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE shipping ADD CONSTRAINT FK_2D1C1724F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE shipping ADD CONSTRAINT FK_2D1C172419EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_2D1C1724F92F3E70 ON shipping (country_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2D1C172419EB6921 ON shipping (client_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455F92F3E70');
        $this->addSql('DROP INDEX IDX_C7440455F92F3E70 ON client');
        $this->addSql('ALTER TABLE client DROP country_id');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993984584665A');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F529939819EB6921');
        $this->addSql('DROP INDEX IDX_F52993984584665A ON `order`');
        $this->addSql('DROP INDEX UNIQ_F529939819EB6921 ON `order`');
        $this->addSql('ALTER TABLE `order` DROP product_id, DROP client_id');
        $this->addSql('ALTER TABLE shipping DROP FOREIGN KEY FK_2D1C1724F92F3E70');
        $this->addSql('ALTER TABLE shipping DROP FOREIGN KEY FK_2D1C172419EB6921');
        $this->addSql('DROP INDEX IDX_2D1C1724F92F3E70 ON shipping');
        $this->addSql('DROP INDEX UNIQ_2D1C172419EB6921 ON shipping');
        $this->addSql('ALTER TABLE shipping DROP country_id, DROP client_id');
    }
}
