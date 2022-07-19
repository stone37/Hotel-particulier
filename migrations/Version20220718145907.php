<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220718145907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE room_supplement (room_id INT NOT NULL, supplement_id INT NOT NULL, INDEX IDX_35A803D554177093 (room_id), INDEX IDX_35A803D57793FA21 (supplement_id), PRIMARY KEY(room_id, supplement_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE room_supplement ADD CONSTRAINT FK_35A803D554177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE room_supplement ADD CONSTRAINT FK_35A803D57793FA21 FOREIGN KEY (supplement_id) REFERENCES supplement (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE supplement_room');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE supplement_room (supplement_id INT NOT NULL, room_id INT NOT NULL, INDEX IDX_CA053E7F54177093 (room_id), INDEX IDX_CA053E7F7793FA21 (supplement_id), PRIMARY KEY(supplement_id, room_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE supplement_room ADD CONSTRAINT FK_CA053E7F54177093 FOREIGN KEY (room_id) REFERENCES room (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE supplement_room ADD CONSTRAINT FK_CA053E7F7793FA21 FOREIGN KEY (supplement_id) REFERENCES supplement (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('DROP TABLE room_supplement');
    }
}
