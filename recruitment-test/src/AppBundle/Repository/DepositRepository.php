<?php

  namespace AppBundle\Repository;


  use AppBundle\Entity\Customer;
  use AppBundle\Entity\Deposit;
  use AppBundle\Service\BonusService;
  use Doctrine\DBAL\Connection;
  use AppBundle\Client;

  /**
   * Class DepositRepository
   * @package AppBundle\Repository
   */
  class DepositRepository {

    private $connection;
    private $customerRepository;
    private $bonusService;

    /**
     * DepositRepository constructor.
     * @param Connection $dbalConnection
     */
    public function __construct(Connection $dbalConnection,
                                CustomerRepository $customerRepository)  {
      $this->connection = $dbalConnection;
      $this->customerRepository = $customerRepository;
      $this->bonusService = new BonusService($this);

    }


    /**
     *
     * Returns total number of deposits for given Customer
     *
     * @param Customer $customer
     * @return int
     */
    public function getTotalNumberDeposits(Customer $customer) : int {

        $sql = "SELECT COUNT(*) as tot_deposits FROM CUSTOMER_DEPOSITS WHERE customer_id = :customer_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("customer_id", $customer->getId());
        $stmt->execute();
        $record = $stmt->fetch();

        return intval($record['tot_deposits']);

    }


    /**
     * Adds a deposit record in the customer_deposits table with the given
     * depositAmount and bonusAmount
     *
     * @param string $customerId
     * @param string $depositAmount
     * @param string $bonusAmount
     */
    private function addDepositRecord(Deposit $deposit) : Deposit {
      $sql = "INSERT INTO CUSTOMER_DEPOSITS (customer_id, real_deposit_amount, bonus_deposit_amount)
                VALUES (:customer_id, :real_deposit_amount, :bonus_deposit_amount);";
      $stmt = $this->connection->prepare($sql);
      $stmt->bindValue("customer_id", $deposit->getCustomerId());
      $stmt->bindValue("real_deposit_amount", $deposit->getRealDepositAmount());
      $stmt->bindValue("bonus_deposit_amount", $deposit->getBonusDepositAmount());
      $stmt->execute();

      $deposit->setId($this->connection->lastInsertId());
      return $deposit;

    }

    /**
     * @param Customer $customer
     * @param float $depositAmount
     */
    public function deposit(Customer $customer, float $depositAmount){

        $deposit = new Deposit($customer, $depositAmount);

        if ($this->bonusService->shouldAddBonus($customer)){
          $bonusAmount = $this->bonusService->calculateBonusAmount($customer, $deposit);
          $deposit->setBonusDepositAmount($bonusAmount);
        }

        //tx demarcation
        $this->connection->beginTransaction();

        try {

          $deposit = $this->addDepositRecord($deposit);
          $this->customerRepository->deposit($deposit);
          $this->connection->commit();

          return $deposit;

        } catch (\Exception $e) {
          $this->connection->rollBack();
          throw $e;
        }

    }


  }