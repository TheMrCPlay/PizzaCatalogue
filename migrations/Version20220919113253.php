<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220919113253 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ingridient (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pizza (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, price VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pizzas_ingridients (pizza_id INT NOT NULL, ingridient_id INT NOT NULL, INDEX IDX_49E257E6D41D1D42 (pizza_id), INDEX IDX_49E257E6750B1398 (ingridient_id), PRIMARY KEY(pizza_id, ingridient_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pizzas_ingridients ADD CONSTRAINT FK_49E257E6D41D1D42 FOREIGN KEY (pizza_id) REFERENCES pizza (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pizzas_ingridients ADD CONSTRAINT FK_49E257E6750B1398 FOREIGN KEY (ingridient_id) REFERENCES ingridient (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pizzas_ingridients DROP FOREIGN KEY FK_49E257E6D41D1D42');
        $this->addSql('ALTER TABLE pizzas_ingridients DROP FOREIGN KEY FK_49E257E6750B1398');
        $this->addSql('DROP TABLE ingridient');
        $this->addSql('DROP TABLE pizza');
        $this->addSql('DROP TABLE pizzas_ingridients');
    }
}
