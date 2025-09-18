<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250912110251 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_word (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, word_id INT NOT NULL, status VARCHAR(255) NOT NULL, ef DOUBLE PRECISION NOT NULL, `interval` INT NOT NULL, reps INT NOT NULL, due_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', last_result SMALLINT DEFAULT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B97039D8A76ED395 (user_id), INDEX IDX_B97039D8E357438D (word_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_word ADD CONSTRAINT FK_B97039D8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_word ADD CONSTRAINT FK_B97039D8E357438D FOREIGN KEY (word_id) REFERENCES word (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_word DROP FOREIGN KEY FK_B97039D8A76ED395');
        $this->addSql('ALTER TABLE user_word DROP FOREIGN KEY FK_B97039D8E357438D');
        $this->addSql('DROP TABLE user_word');
    }
}
