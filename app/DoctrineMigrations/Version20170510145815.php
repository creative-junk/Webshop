<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170510145815 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE buyer_grower (id INT AUTO_INCREMENT NOT NULL, buyer_id INT NOT NULL, grower_id INT NOT NULL, list_owner_id INT NOT NULL, status VARCHAR(255) NOT NULL, date_since DATETIME DEFAULT NULL, updated_at DATETIME NOT NULL, INDEX IDX_1510357C6C755722 (buyer_id), INDEX IDX_1510357C5243E353 (grower_id), INDEX IDX_1510357C515299 (list_owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE grower_agent (id INT AUTO_INCREMENT NOT NULL, agent_id INT NOT NULL, grower_id INT NOT NULL, list_owner_id INT NOT NULL, status VARCHAR(255) NOT NULL, date_since DATETIME DEFAULT NULL, updated_at DATETIME NOT NULL, INDEX IDX_CBF2E7443414710B (agent_id), INDEX IDX_CBF2E7445243E353 (grower_id), INDEX IDX_CBF2E744515299 (list_owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE grower_breeder (id INT AUTO_INCREMENT NOT NULL, grower_id INT NOT NULL, breeder_id INT NOT NULL, list_owner_id INT NOT NULL, status VARCHAR(255) NOT NULL, date_since DATETIME DEFAULT NULL, updated_at DATETIME NOT NULL, INDEX IDX_E4F706E15243E353 (grower_id), INDEX IDX_E4F706E133C95BB1 (breeder_id), INDEX IDX_E4F706E1515299 (list_owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE buyer_grower ADD CONSTRAINT FK_1510357C6C755722 FOREIGN KEY (buyer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE buyer_grower ADD CONSTRAINT FK_1510357C5243E353 FOREIGN KEY (grower_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE buyer_grower ADD CONSTRAINT FK_1510357C515299 FOREIGN KEY (list_owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE grower_agent ADD CONSTRAINT FK_CBF2E7443414710B FOREIGN KEY (agent_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE grower_agent ADD CONSTRAINT FK_CBF2E7445243E353 FOREIGN KEY (grower_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE grower_agent ADD CONSTRAINT FK_CBF2E744515299 FOREIGN KEY (list_owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE grower_breeder ADD CONSTRAINT FK_E4F706E15243E353 FOREIGN KEY (grower_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE grower_breeder ADD CONSTRAINT FK_E4F706E133C95BB1 FOREIGN KEY (breeder_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE grower_breeder ADD CONSTRAINT FK_E4F706E1515299 FOREIGN KEY (list_owner_id) REFERENCES user (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE buyer_grower');
        $this->addSql('DROP TABLE grower_agent');
        $this->addSql('DROP TABLE grower_breeder');
    }
}
