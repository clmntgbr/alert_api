<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220903124904 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product ADD image_ingredients_name VARCHAR(255) DEFAULT NULL, ADD image_ingredients_original_name VARCHAR(255) DEFAULT NULL, ADD image_ingredients_mime_type VARCHAR(255) DEFAULT NULL, ADD image_ingredients_size INT DEFAULT NULL, ADD image_ingredients_dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', ADD image_nutrition_name VARCHAR(255) DEFAULT NULL, ADD image_nutrition_original_name VARCHAR(255) DEFAULT NULL, ADD image_nutrition_mime_type VARCHAR(255) DEFAULT NULL, ADD image_nutrition_size INT DEFAULT NULL, ADD image_nutrition_dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP image_ingredients_name, DROP image_ingredients_original_name, DROP image_ingredients_mime_type, DROP image_ingredients_size, DROP image_ingredients_dimensions, DROP image_nutrition_name, DROP image_nutrition_original_name, DROP image_nutrition_mime_type, DROP image_nutrition_size, DROP image_nutrition_dimensions');
    }
}
