<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230602170525 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notification_item (notification_id INT NOT NULL, item_id INT NOT NULL, INDEX IDX_A7276E24EF1A9D84 (notification_id), INDEX IDX_A7276E24126F525E (item_id), PRIMARY KEY(notification_id, item_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notification_item ADD CONSTRAINT FK_A7276E24EF1A9D84 FOREIGN KEY (notification_id) REFERENCES notification (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification_item ADD CONSTRAINT FK_A7276E24126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification_item DROP FOREIGN KEY FK_A7276E24EF1A9D84');
        $this->addSql('ALTER TABLE notification_item DROP FOREIGN KEY FK_A7276E24126F525E');
        $this->addSql('DROP TABLE notification_item');
    }
}
