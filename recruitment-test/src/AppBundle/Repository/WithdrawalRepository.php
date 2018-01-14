<?php

  namespace AppBundle\Repository;

  use AppBundle\Entity\Customer;
  use AppBundle\Entity\Withdrawal;
  use AppBundle\Exception\InsufficientBalanceException;
  use AppBundle\Exception\InsufficientBalaneException;
  use Doctrine\DBAL\Connection;

  
  class WithdrawalRepository {

    private $connection;
    private $customerRepository;


    /**
     * DepositRepository constructor.
     * @param Connection $dbalConnection
     */
    public function __construct(Connection $dbalConnection,
                                CustomerRepository $customerRepository)  {
      $this->connection = $dbalConnection;
      $this->connection->setTransactionIsolation(Connection::TRANSACTION_SERIALIZABLE);
      $this->customerRepository = $customerRepository;
    }


    /**
     * Adds a withdrawal record in the customer_withdrawals table with the given
     * wtidhrawalAmount
     *
     * @param Withdrawal $withdrawal
     * @return Withdrawal
     */
    private function addWithdrawalRecord(Withdrawal $withdrawal) : Withdrawal {
      $sql = "INSERT INTO CUSTOMER_WITHDRAWALS (customer_id, withdrawal_amount)
                VALUES (:customer_id, :withdrawal_amount );";
      $stmt = $this->connection->prepare($sql);
      $stmt->bindValue("customer_id", $withdrawal->getCustomerId());
      $stmt->bindValue("withdrawal_amount", $withdrawal->getWithdrawalAmount());
      $stmt->execute();

      $withdrawal->setId($this->connection->lastInsertId());
      return $withdrawal;

    }

    /**
     * Performs a withdrawal for given Customer
     *
     * @param Customer $customer
     *
     * @param float $withdrawalAmount
     * @return Withdrawal
     * @throws \Exception
     */
    public function withdrawal(Customer $customer, float $withdrawalAmount) : Withdrawal{

      try {

        //tx demarcation
        $this->connection->beginTransaction();

        if ($withdrawalAmount > $this->customerRepository->getRealBalance($customer)){
          throw new InsufficientBalanceException();
        }

        $withdrawal = new Withdrawal($customer, $withdrawalAmount);

        $withdrawal = $this->addWithdrawalRecord($withdrawal);

        $this->customerRepository->withdraw($withdrawal);
        $this->connection->commit();

        return $withdrawal;

      } catch (\Exception $e) {
        $this->connection->rollBack();
        throw $e;
      }

    }


  }