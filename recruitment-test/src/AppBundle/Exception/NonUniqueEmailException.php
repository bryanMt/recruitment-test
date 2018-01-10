<?php


namespace AppBundle\Exception;


use Exception;

class NonUniqueEmailException extends InputValidationException {

  public function __construct()
  {
    parent::__construct("email.not_unique");
  }

}