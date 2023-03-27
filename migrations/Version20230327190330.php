<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230327190330 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE item (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, store_id INT DEFAULT NULL, expiration_date DATE DEFAULT NULL, is_liked TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_1F1B251E4584665A (product_id), INDEX IDX_1F1B251EB092A811 (store_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, nutrition_id INT DEFAULT NULL, ean VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, brand VARCHAR(255) DEFAULT NULL, manufacturing_place VARCHAR(255) DEFAULT NULL, link VARCHAR(255) DEFAULT NULL, origin VARCHAR(255) DEFAULT NULL, categories VARCHAR(255) DEFAULT NULL, statuses LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', status VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, image_original_name VARCHAR(255) DEFAULT NULL, image_mime_type VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, image_dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', image_ingredients_name VARCHAR(255) DEFAULT NULL, image_ingredients_original_name VARCHAR(255) DEFAULT NULL, image_ingredients_mime_type VARCHAR(255) DEFAULT NULL, image_ingredients_size INT DEFAULT NULL, image_ingredients_dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', image_nutrition_name VARCHAR(255) DEFAULT NULL, image_nutrition_original_name VARCHAR(255) DEFAULT NULL, image_nutrition_mime_type VARCHAR(255) DEFAULT NULL, image_nutrition_size INT DEFAULT NULL, image_nutrition_dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', UNIQUE INDEX UNIQ_D34A04AD67B1C660 (ean), INDEX IDX_D34A04ADB5D724CD (nutrition_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_nutrition (id INT AUTO_INCREMENT NOT NULL, ecoscore_grade VARCHAR(255) DEFAULT NULL, ecoscore_score VARCHAR(255) DEFAULT NULL, ingredients_text LONGTEXT DEFAULT NULL, nutriscore_grade VARCHAR(255) DEFAULT NULL, nutriscore_score VARCHAR(255) DEFAULT NULL, quantity VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE store (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, max_item_per_store INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, INDEX IDX_FF575877A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251EB092A811 FOREIGN KEY (store_id) REFERENCES store (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADB5D724CD FOREIGN KEY (nutrition_id) REFERENCES product_nutrition (id)');
        $this->addSql('ALTER TABLE store ADD CONSTRAINT FK_FF575877A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E4584665A');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251EB092A811');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADB5D724CD');
        $this->addSql('ALTER TABLE store DROP FOREIGN KEY FK_FF575877A76ED395');
        $this->addSql('DROP TABLE item');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_nutrition');
        $this->addSql('DROP TABLE store');
    }
}
