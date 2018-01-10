<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180107130210 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
      //drop CUSTOMER table
      $this->addSql('DROP TABLE IF EXISTS customer;');

      //create CUSTOMER table
      $this->addSql('  CREATE TABLE customer (
	                      id MEDIUMINT NOT NULL AUTO_INCREMENT,
                        gender ENUM(\'male\', \'female\') NOT NULL,
                        firstname VARCHAR(50) NOT NULL,
                        lastname VARCHAR(50) NOT NULL,
                        country VARCHAR(2) NOT NULL,
                        email VARCHAR(60) NOT NULL UNIQUE,
                        bonus DOUBLE NOT NULL,
                        PRIMARY KEY (id)
                   ) 
      ') ;

      //drop CUSTOMER_BALANCE table
      $this->addSql('DROP TABLE IF EXISTS customer_balance');

      //create CUSTOMER_BALANCE table
      $this->addSql('CREATE TABLE customer_balance (
                           customer_id MEDIUMINT NOT NULL ,
                           real_balance double not null default 0.0,
                           bonus_balance double not null default 0.0,
                           updated_at datetime not null default CURRENT_TIMESTAMP,
                           PRIMARY KEY (customer_id),
                           FOREIGN KEY (customer_id)
                                REFERENCES customer(id)
                                ON DELETE CASCADE
                         ) 
              ');

      //drop CUSTOMER_DEPOSITS table
      $this->addSql('DROP TABLE IF EXISTS customer_deposits');

      //create CUSTOMER_DEPOSITS table
      $this->addSql('CREATE TABLE customer_deposits (
                       id int NOT NULL AUTO_INCREMENT,
                       customer_id MEDIUMINT NOT NULL ,
                       real_deposit_amount double not null default 0.0,
                       bonus_deposit_amount double not null default 0.0,
                       deposit_date datetime not null default CURRENT_TIMESTAMP,
                       PRIMARY KEY (id),
                       INDEX customer_id (customer_id),
                       FOREIGN KEY (customer_id)
                            REFERENCES customer(id)
                            ON DELETE CASCADE
                     )');

      //drop CUSTOMER_WITHDRAWALS table
      $this->addSql('DROP TABLE IF EXISTS customer_withdrawals');

      //create CUSTOMER_WITHDRAWALS TABLE
      $this->addSql('CREATE TABLE customer_withdrawals (
                       id int NOT NULL AUTO_INCREMENT,
                       customer_id MEDIUMINT NOT NULL ,
                       withdrawal_amount double not null default 0.0,
                       withdrawal_date datetime not null default CURRENT_TIMESTAMP,
                       PRIMARY KEY (id),
                       INDEX customer_id (customer_id),
                       FOREIGN KEY (customer_id)
                            REFERENCES customer(id)
                            ON DELETE CASCADE
                     );');


    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
