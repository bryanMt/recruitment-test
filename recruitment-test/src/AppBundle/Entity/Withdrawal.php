<?php

 namespace AppBundle\Entity;

 class Withdrawal {

    private $id;

    private $customerId;

   /**
    * Deposit constructor.
    *
    * @param Customer $customer
    * @param float $depositAmount
    */
   public function __construct(Customer $customer, float $withdrawalAmount){
     $this->withdrawalAmount = $withdrawalAmount;
     $this->customerId = $customer->getId();
   }


   /**
    * @return int
    */
   public function getId() : int
   {
     return $this->id;
   }

   /**
    * @param int $id
    */
   public function setId(int $id)
   {
     $this->id = $id;
   }

   /**
    * @return int
    */
   public function getCustomerId() : int
   {
     return $this->customerId;
   }

   /**
    * @param int $customerId
    */
   public function setCustomerId(int $customerId)
   {
     $this->customerId = $customerId;
   }

   /**
    * @return float
    */
   public function getWithdrawalAmount() : float
   {
     return $this->withdrawalAmount;
   }

   /**
    * @param float $withdrawalAmount
    */
   public function setWithdrawalAmount(float $withdrawalAmount)
   {
     $this->withdrawalAmount = $withdrawalAmount;
   }

    private $withdrawalAmount;


 }