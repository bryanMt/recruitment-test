<?php

 namespace AppBundle\Service;

 use AppBundle\Entity\Customer;
 use AppBundle\Entity\Deposit;
 use AppBundle\Repository\DepositRepository;


 class BonusService{

   private $depositsRepository;

   /**
    * BonusService constructor.
    * @param DepositRepository $depositRepository
    */
   public function __construct(DepositRepository $depositRepository){
     $this->depositsRepository = $depositRepository;
   }

   /**
    * Returns true if this customer should get a bonus on next deposit
    *
    * @param Customer $customer
    * @return bool
    */
   public function shouldAddBonus(Customer $customer) : bool {
     $totalNumberDeposits = $this->depositsRepository->getTotalNumberDeposits($customer);
     return ((($totalNumberDeposits + 1 ) % 3 == 0) && $totalNumberDeposits !== 0);
   }

   /**
    * Calculates the bonus amount for the given customer + deposit
    *
    * @param Customer $customer
    * @param Deposit $deposit
    * @return float
    */
   public function calculateBonusAmount(Customer $customer, Deposit $deposit) : float {
     return ( $customer->getBonus() / 100 ) * $deposit->getRealDepositAmount();
   }


 }