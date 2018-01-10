<?php


namespace AppBundle\Exception;


use Exception;

class InputValidationException extends Exception {

  public function __construct($message)
  {
    parent::__construct($message, 400, null);
  }

}