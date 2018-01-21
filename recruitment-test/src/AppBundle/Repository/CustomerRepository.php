<?php

  namespace AppBundle\Repository;

  use AppBundle\Entity\Customer;
  use AppBundle\Entity\Deposit;
  use AppBundle\Entity\Withdrawal;
  use AppBundle\Exception\InputValidationException;
  use Doctrine\DBAL\Connection;
  use Doctrine\DBAL\Driver\Statement;
  use Exception;


  class CustomerRepository  {

    /**
     *
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $dbalConnection)  {
      $this->connection = $dbalConnection;
    }


    /**
     * Initializes the customer balance record
     *
     * @param string $customerId
     */
    private function initializeCustomerBalance(Customer $customer){
      $sql = "INSERT INTO CUSTOMER_BALANCE (customer_id) VALUES (:customer_id)";
      $stmt = $this->connection->prepare($sql);
      $stmt->bindValue("customer_id", $customer->getId());
      $stmt->execute();
    }

    /**
     * @param Customer $customer
     * @return Customer
     */
    public function addNewCustomer(Customer $customer){

      $sql = "INSERT INTO CUSTOMER (gender, firstname, lastname,  country, email, bonus ) 
              VALUES (:gender, :firstname, :lastname, :country, :email, :bonus); ";
      $stmt = $this->connection->prepare($sql);
      $stmt = $this->bindCustomerParamsToStmt($stmt, $customer);
      $stmt->execute();

      $customer->setId($this->connection->lastInsertId());

      $this->initializeCustomerBalance($customer);

      return $customer;

    }

    /**
     * Checks if given email exists
     *
     * @param string $email
     * @return bool
     */
    public function emailExists(string $email) : bool {
      $sql = "SELECT * FROM CUSTOMER WHERE email = :email";
      $stmt = $this->connection->prepare($sql);
      $stmt->bindValue("email", $email);
      $stmt->execute();

      $customers = $stmt->fetchAll();

      return (count($customers) >= 1);
    }

    /**
     *
     * Retrieves the Customer instance with the given id
     *
     * @param int $customerId
     *
     * @return Customer
     * @throws InputValidationException
     */
    public function retrieveCustomerById(int $customerId){

      if (is_null($customerId)){
        throw new InputValidationException("customer_id.invalid");
      }

      $sql = "SELECT * FROM CUSTOMER WHERE id = :customer_id";
      $stmt = $this->connection->prepare($sql);
      $stmt->bindValue("customer_id", $customerId);
      $stmt->execute();
      $record = $stmt->fetch();

      if (!$record){
        throw new InputValidationException("customer_id.invalid");
      }

      return Customer::from_array($record);
    }

    /**
     *
     * Binds the Customer parameters to the PDO Statement instance
     *
     * @param Statement $statement
     * @param Customer $customer
     *
     * @return Statement
     */
    private function bindCustomerParamsToStmt(Statement $statement, Customer $customer){
      $statement->bindValue("gender", $customer->getGender());
      $statement->bindValue("firstname", $customer->getFirstname());
      $statement->bindValue("lastname", $customer->getLastname());
      $statement->bindValue("country", $customer->getCountry());
      $statement->bindValue("email", $customer->getEmail());
      $statement->bindValue("bonus", $customer->getBonus());

      return $statement;
    }


    /**
     * Updates the given Customer instance
     *
     * @param Customer $customer
     */
    public function updateCustomer(Customer $customer){

      $sql = "UPDATE CUSTOMER SET gender = :gender, firstname = :firstname, lastname= :lastname, country= :country, email= :email, bonus=:bonus WHERE id= :customerId";
      $stmt = $this->connection->prepare($sql);
      $stmt = $this->bindCustomerParamsToStmt($stmt, $customer);

      $stmt->bindValue("customerId", $customer->getId());
      $stmt->execute();

    }

    /**
     * Performs a deposit into the customers' account
     *
     * @param Deposit $deposit
     * @return bool
     */
    public function deposit(Deposit $deposit) : bool {

      $sql = "SELECT * FROM customer_balance WHERE customer_id = :customer_id FOR UPDATE;";
      $stmt = $this->connection->prepare($sql);
      $stmt->bindValue("customer_id", $deposit->getCustomerId());
      $stmt->execute();
      $stmt->fetchAll();


      $sql = "UPDATE customer_balance SET real_balance = real_balance + "  . round($deposit->getRealDepositAmount(),2) .
          ", bonus_balance = bonus_balance + " . round($deposit->getBonusDepositAmount(),2) .
          " WHERE customer_id = " . $deposit->getCustomerId() . ";";
      $this->connection->exec($sql);

      return true;

    }

    /**
     * @param Withdrawal $withdrawal
     * @return bool
     */
    public function withdraw(Withdrawal $withdrawal): bool {

        $sql = "UPDATE CUSTOMER_BALANCE 
                SET real_balance = real_balance - :withdrawal_amount
                WHERE customer_id = :customer_id;";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue("withdrawal_amount", $withdrawal->getWithdrawalAmount());
        $stmt->bindValue("customer_id", $withdrawal->getCustomerId());
        return $stmt->execute();

    }

    /**
     * Returns the real balance for given customer
     *
     * @param Customer $customer
     * @return float
     */
    public function getRealBalance(Customer $customer) : float {

      $sql = "SELECT real_balance FROM CUSTOMER_BALANCE WHERE customer_id = :customer_id FOR UPDATE ";
      $stmt = $this->connection->prepare($sql);
      $stmt->bindValue("customer_id", $customer->getId());
      $stmt->execute();
      $record = $stmt->fetch();

      return floatval($record['real_balance']);

    }

    public function incrementNumDeposits(Customer $customer) : bool {
      $sql = "UPDATE customer SET num_deposits = num_deposits + 1 WHERE id = :customer_id";
      $stmt = $this->connection->prepare($sql);
      $stmt->bindValue("customer_id", $customer->getId());
      return $stmt->execute();



    }
  }