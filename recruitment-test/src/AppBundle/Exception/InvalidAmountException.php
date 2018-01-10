<?php


namespace AppBundle\Exception;


use Exception;

class InvalidAmountException extends InputValidationException {

  public function __construct()
  {
    parent::__construct("deposit_amount.invalid");
  }

}