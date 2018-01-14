<?php
/**
 * Created by PhpStorm.
 * User: bryanborg
 * Date: 08/01/2018
 * Time: 20:42
 */

namespace AppBundle\Controller;

use AppBundle\Exception\InvalidAmountException;
use AppBundle\Exception\InvalidDepositAmountException;
use AppBundle\Exception\UnsupportedCurrencyException;
use AppBundle\Repository\CustomerRepository;
use FOS\RestBundle\Controller\FOSRestController;

/**
 *
 * Class TransactionsController abstracts the common functionality between
 * the depositsController And the withdrawalsController
 *
 * @package AppBundle\Controller
 */
class TransactionsController extends FOSRestController
{

  protected $customerRepository;

  /**
   * TransactionsController constructor.
   * @param CustomerRepository $customerRepository
   */
  protected function __construct(CustomerRepository $customerRepository){
    $this->customerRepository = $customerRepository;
  }

  /**
   *
   * Validates Input parameters:
   *
   *  - check that currency is supported
   *  - confirm we have a valid deposit amount
   *
   * @param array $params
   * @throws InvalidDepositAmountException
   * @throws UnsupportedCurrencyException
   */
  protected function validateInputParams(Array $params){

    if (strtoupper($params['currency']) !== "EUR"){
      throw new UnsupportedCurrencyException();
    }

    if  (!is_numeric($params["amount"]) || floatval($params['amount']) < 0){
      throw new InvalidAmountException();
    }

  }

}