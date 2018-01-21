<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180121221519 extends AbstractMigration
{
    public function up(Schema $schema)
    {
      //create CUSTOMER table
      $this->addSql(' ALTER TABLE customer ADD COLUMN num_deposits INT DEFAULT 0;') ;


    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
