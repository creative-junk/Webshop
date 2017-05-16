<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170510100715 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE buyer_agent (id INT AUTO_INCREMENT NOT NULL, buyer_id INT NOT NULL, agent_id INT NOT NULL, list_owner_id INT NOT NULL, status VARCHAR(255) NOT NULL, date_since DATETIME DEFAULT NULL, updated_at DATETIME NOT NULL, INDEX IDX_D1D21B386C755722 (buyer_id), INDEX IDX_D1D21B383414710B (agent_id), INDEX IDX_D1D21B38515299 (list_owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE buyer_agent ADD CONSTRAINT FK_D1D21B386C755722 FOREIGN KEY (buyer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE buyer_agent ADD CONSTRAINT FK_D1D21B383414710B FOREIGN KEY (agent_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE buyer_agent ADD CONSTRAINT FK_D1D21B38515299 FOREIGN KEY (list_owner_id) REFERENCES user (id)');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE growers_list (id INT AUTO_INCREMENT NOT NULL, whose_list_id INT NOT NULL, grower_id INT NOT NULL, status VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_84FA36972CF96B9 (whose_list_id), INDEX IDX_84FA36975243E353 (grower_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE buyer_agent');
    }
}
