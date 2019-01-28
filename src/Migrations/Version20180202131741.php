<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180202131741 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE categorie ADD tree_root INT DEFAULT NULL, ADD parent_id INT DEFAULT NULL, ADD lft INT NOT NULL, ADD lvl INT NOT NULL, ADD rgt INT NOT NULL');
        $this->addSql('ALTER TABLE categorie ADD CONSTRAINT FK_497DD634A977936C FOREIGN KEY (tree_root) REFERENCES categorie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE categorie ADD CONSTRAINT FK_497DD634727ACA70 FOREIGN KEY (parent_id) REFERENCES categorie (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_497DD634A977936C ON categorie (tree_root)');
        $this->addSql('CREATE INDEX IDX_497DD634727ACA70 ON categorie (parent_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE categorie DROP FOREIGN KEY FK_497DD634A977936C');
        $this->addSql('ALTER TABLE categorie DROP FOREIGN KEY FK_497DD634727ACA70');
        $this->addSql('DROP INDEX IDX_497DD634A977936C ON categorie');
        $this->addSql('DROP INDEX IDX_497DD634727ACA70 ON categorie');
        $this->addSql('ALTER TABLE categorie DROP tree_root, DROP parent_id, DROP lft, DROP lvl, DROP rgt');
    }
}
