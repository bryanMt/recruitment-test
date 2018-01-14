<?php


namespace AppBundle\Exception;


use Exception;

class InvalidDateException extends InputValidationException {

  public function __construct()
  {
    parent::__construct("date.invalid [Format: Y-m-d]");
  }

}