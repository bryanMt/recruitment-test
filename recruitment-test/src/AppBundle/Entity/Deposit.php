<?php

 namespace AppBundle\Entity;


 class Deposit {

   private $id;

   private $customer_id;

   private $real_deposit_amount;

   private $bonus_deposit_amount;

   /**
    * Deposit constructor.
    *
    * @param Customer $customer
    * @param float $depositAmount
    */
   public function __construct(Customer $customer, float $depositAmount){
     $this->customer_id = $customer->getId();
     $this->real_deposit_amount = $depositAmount;
     $this->bonus_deposit_amount = 0;
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
     return $this->customer_id;
   }

   /**
    * @param int $customer_id
    */
   public function setCustomerId(int $customer_id)
   {
     $this->customer_id = $customer_id;
   }

   /**
    * @return float
    */
   public function getRealDepositAmount() : float
   {
     return $this->real_deposit_amount;
   }

   /**
    * @param float $real_deposit_amount
    */
   public function setRealDepositAmount(float $real_deposit_amount)
   {
     $this->real_deposit_amount = $real_deposit_amount;
   }

   /**
    * @return float
    */
   public function getBonusDepositAmount() : float
   {
     return $this->bonus_deposit_amount;
   }

   /**
    * @param float $bonus_deposit_amount
    */
   public function setBonusDepositAmount(float $bonus_deposit_amount)
   {
     $this->bonus_deposit_amount = $bonus_deposit_amount;
   }

 }