<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220911165416 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product_status (id INT AUTO_INCREMENT NOT NULL, reference VARCHAR(50) NOT NULL, label VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_status_history (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, product_status_id INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_7C0379E24584665A (product_id), INDEX IDX_7C0379E2557B630 (product_status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product_status_history ADD CONSTRAINT FK_7C0379E24584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_status_history ADD CONSTRAINT FK_7C0379E2557B630 FOREIGN KEY (product_status_id) REFERENCES product_status (id)');
        $this->addSql('ALTER TABLE product ADD product_status_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD557B630 FOREIGN KEY (product_status_id) REFERENCES product_status (id)');
        $this->addSql('CREATE INDEX IDX_D34A04AD557B630 ON product (product_status_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD557B630');
        $this->addSql('ALTER TABLE product_status_history DROP FOREIGN KEY FK_7C0379E24584665A');
        $this->addSql('ALTER TABLE product_status_history DROP FOREIGN KEY FK_7C0379E2557B630');
        $this->addSql('DROP TABLE product_status');
        $this->addSql('DROP TABLE product_status_history');
        $this->addSql('DROP INDEX IDX_D34A04AD557B630 ON product');
        $this->addSql('ALTER TABLE product DROP product_status_id');
    }
}
