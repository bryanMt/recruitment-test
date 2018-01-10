<?php


namespace AppBundle\Exception;


use Exception;

class InsufficientBalanceException extends InputValidationException {

  public function __construct()
  {
    parent::__construct("balance.insufficient");
  }

}