<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250908180009 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_word (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, word_id INT NOT NULL, status VARCHAR(16) NOT NULL, ef DOUBLE PRECISION NOT NULL, `interval` INT NOT NULL, reps INT NOT NULL, due_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', last_result SMALLINT DEFAULT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B97039D8A76ED395 (user_id), INDEX IDX_B97039D8E357438D (word_id), UNIQUE INDEX UNIQ_B97039D8A76ED395E357438D (user_id, word_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_word ADD CONSTRAINT FK_B97039D8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_word ADD CONSTRAINT FK_B97039D8E357438D FOREIGN KEY (word_id) REFERENCES word (id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649A76ED395');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649E357438D');
        $this->addSql('DROP INDEX UNIQ_8D93D649A76ED395E357438D ON user');
        $this->addSql('DROP INDEX IDX_8D93D649A76ED395 ON user');
        $this->addSql('DROP INDEX IDX_8D93D649E357438D ON user');
        $this->addSql('ALTER TABLE user ADD email VARCHAR(180) NOT NULL, ADD roles JSON NOT NULL, ADD password VARCHAR(255) NOT NULL, DROP user_id, DROP word_id, DROP status, DROP ef, DROP `interval`, DROP reps, DROP due_at, DROP last_result, DROP updated_at');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_word DROP FOREIGN KEY FK_B97039D8A76ED395');
        $this->addSql('ALTER TABLE user_word DROP FOREIGN KEY FK_B97039D8E357438D');
        $this->addSql('DROP TABLE user_word');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('ALTER TABLE user ADD user_id INT NOT NULL, ADD word_id INT NOT NULL, ADD status VARCHAR(16) NOT NULL, ADD ef DOUBLE PRECISION NOT NULL, ADD `interval` INT NOT NULL, ADD reps INT NOT NULL, ADD due_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD last_result SMALLINT DEFAULT NULL, ADD updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP email, DROP roles, DROP password');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649E357438D FOREIGN KEY (word_id) REFERENCES word (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649A76ED395E357438D ON user (user_id, word_id)');
        $this->addSql('CREATE INDEX IDX_8D93D649A76ED395 ON user (user_id)');
        $this->addSql('CREATE INDEX IDX_8D93D649E357438D ON user (word_id)');
    }
}
