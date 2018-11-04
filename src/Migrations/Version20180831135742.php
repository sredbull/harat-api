<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180831135742 extends AbstractMigration
{

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE `recruitment` (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, form JSON NOT NULL COMMENT \'(DC2Type:json_array)\', INDEX IDX_C7238C6EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `recruitment` ADD CONSTRAINT FK_C7238C6EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE `recruitment`');
    }

}
