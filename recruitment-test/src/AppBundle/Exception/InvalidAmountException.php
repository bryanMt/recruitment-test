<?php


namespace AppBundle\Exception;


use Exception;

class InvalidDepositAmountException extends InputValidationException {

  public function __construct()
  {
    parent::__construct("deposit_amount.invalid");
  }

}