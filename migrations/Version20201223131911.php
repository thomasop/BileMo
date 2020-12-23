<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201223131911 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE customer (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, first_name VARCHAR(100) NOT NULL, password VARCHAR(150) NOT NULL, mail VARCHAR(150) NOT NULL, roles JSON NOT NULL, UNIQUE INDEX UNIQ_81398E095126AC48 (mail), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, model VARCHAR(150) NOT NULL, description LONGTEXT NOT NULL, price INT NOT NULL, brand VARCHAR(150) NOT NULL, UNIQUE INDEX UNIQ_D34A04ADD79572D9 (model), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, customer INT NOT NULL, name VARCHAR(100) NOT NULL, first_name VARCHAR(100) NOT NULL, mail VARCHAR(150) NOT NULL, UNIQUE INDEX UNIQ_8D93D6495126AC48 (mail), INDEX IDX_8D93D64981398E09 (customer), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64981398E09 FOREIGN KEY (customer) REFERENCES customer (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64981398E09');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE user');
    }
}
