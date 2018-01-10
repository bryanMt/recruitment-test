<?php

  namespace AppBundle\Service;

  use Doctrine\DBAL\Connection;

  class StatisticsService {

    private $connection;

    /**
     * StatisticsService constructor.
     * @param Connection $dbalConnection
     */
    public function __construct(Connection $dbalConnection){
      $this->connection = $dbalConnection;
    }

    /**
     * @param string $dateFrom
     * @param string $dateto
     * @return string
     */
    private function getQuery(){
      return "
          
            SELECT tx_date as txDate,country,  SUM(distinct_users) as unique_customers, SUM(num_deposits) as number_of_deposits, SUM(num_withdrawals) as number_of_withdrawals, SUM(sum_deposits) as total_amount_deposits, SUM(sum_withdrawals) as total_amount_withdrawals
            FROM (
  
              SELECT COUNT(distinct customer_id) AS distinct_users, 
                     COUNT(*) AS num_deposits, 
                     0 AS num_withdrawals, 
                     SUM(real_deposit_amount) AS sum_deposits, 
                     0 AS sum_withdrawals, 
                     \"deposit\" AS label, 
                     date(deposit_date) AS tx_date, 
                     customer.country AS country
               FROM customer_deposits 
               INNER JOIN customer ON customer_deposits.customer_id = customer.id
               WHERE date(deposit_date) BETWEEN :date_from AND :date_to
               GROUP BY tx_date, country
               
                UNION
   
                SELECT COUNT(distinct customer_id) AS distinct_users, 
                       0 AS num_deposits, 
                       COUNT(*) AS num_withdrawals,  
                       0 AS sum_deposits, 
                       SUM(withdrawal_amount) AS sum_withdrawals, 
                       \"withdrawal\" AS label, 
                       date(withdrawal_date) AS tx_date, 
                       customer.country AS country
                FROM customer_withdrawals 
                INNER JOIN customer ON customer_withdrawals.customer_id = customer.id
                WHERE date(withdrawal_date) BETWEEN :date_from AND :date_to
                GROUP BY tx_date, country
   
                ORDER BY tx_date ASC
   
              ) AS data
              GROUP BY tx_date, country;
   
  
      ";
    }

    /**
     * @param string $dateFrom
     * @param string $dateTo
     * @return Array
     */
    public function getTxStatsGroupedByCountryAndDay(string $dateFrom = null, string $dateTo = null): Array{

      if (is_null($dateFrom) || is_null($dateTo)){
        $dateFrom = date('Y-m-d', strtotime('-7 days'));
        $dateTo = date('Y-m-d');
      }

      $sql = $this->getQuery($dateFrom, $dateTo);
      $stmt = $this->connection->prepare($sql);
      $stmt->bindValue("date_from", $dateFrom);
      $stmt->bindValue("date_to", $dateTo);
      $stmt->execute();

      $records = $stmt->fetchAll();

      return $records;
    }

  }

