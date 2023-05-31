<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230505175119 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADB5D724CD');
        $this->addSql('DROP INDEX IDX_D34A04ADB5D724CD ON product');
        $this->addSql('ALTER TABLE product DROP created_at, DROP updated_at, DROP created_by, DROP updated_by, CHANGE nutrition_id product_nutrition_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD387306A6 FOREIGN KEY (product_nutrition_id) REFERENCES product_nutrition (id)');
        $this->addSql('CREATE INDEX IDX_D34A04AD387306A6 ON product (product_nutrition_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD387306A6');
        $this->addSql('DROP INDEX IDX_D34A04AD387306A6 ON product');
        $this->addSql('ALTER TABLE product ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, ADD created_by VARCHAR(255) DEFAULT NULL, ADD updated_by VARCHAR(255) DEFAULT NULL, CHANGE product_nutrition_id nutrition_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADB5D724CD FOREIGN KEY (nutrition_id) REFERENCES product_nutrition (id)');
        $this->addSql('CREATE INDEX IDX_D34A04ADB5D724CD ON product (nutrition_id)');
    }
}
