<?php


namespace AppBundle\Exception;


use Exception;

class InvalidEmailException extends InputValidationException {

  public function __construct()
  {
    parent::__construct("email.invalid");
  }

}