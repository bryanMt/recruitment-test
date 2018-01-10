<?php


namespace AppBundle\Exception;


use Exception;

class UnsupportedCurrencyException extends InputValidationException {

  public function __construct()
  {
    parent::__construct("currency.unsupported");
  }

}