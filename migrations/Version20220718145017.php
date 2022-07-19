<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220718145017 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE room_equipment_room (room_equipment_id INT NOT NULL, room_id INT NOT NULL, INDEX IDX_11B7760E70DF16D (room_equipment_id), INDEX IDX_11B776054177093 (room_id), PRIMARY KEY(room_equipment_id, room_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE room_equipment_room ADD CONSTRAINT FK_11B7760E70DF16D FOREIGN KEY (room_equipment_id) REFERENCES room_equipment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE room_equipment_room ADD CONSTRAINT FK_11B776054177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE room_equipment_room');
    }
}
