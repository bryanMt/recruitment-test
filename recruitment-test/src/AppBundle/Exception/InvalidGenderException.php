<?php


namespace AppBundle\Exception;


class InvalidGenderException extends InputValidationException {

  public function __construct()
  {
    parent::__construct("gender.invalid");
  }

}