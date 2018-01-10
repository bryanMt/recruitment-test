<?php


namespace AppBundle\Exception;


use Exception;

class InvalidCountryException extends InputValidationException {

  public function __construct()
  {
    parent::__construct("country.invalid");
  }

}