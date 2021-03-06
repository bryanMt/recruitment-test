<?php

namespace AppBundle\Controller;

use AppBundle\Repository\DepositRepository;
use Exception;
use FOS\RestBundle\Request\ParamFetcher;
use AppBundle\Exception\InputValidationException;
use AppBundle\Repository\CustomerRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;


class DepositsController extends TransactionsController
{

  /**
   * Provides a layer of abstraction on top of DBAL
   *
   * @var CustomerRepository
   */
  private $depositRepository;


  /**
   * DepositsController constructor.
   *
   * @param CustomerRepository $customerRepository
   */
  public function __construct(CustomerRepository $customerRepository,
                              DepositRepository $depositRepository)
  {
    parent::__construct($customerRepository);
    $this->depositRepository = $depositRepository;
  }

  /**
   *
   * @Rest\Post("/deposit/{customer_id}")
   * @Rest\RequestParam(name="amount", requirements={"rule" = "^[-+]?\d+(\.\d+)?", "error_message" = "Invalid deposit amount" }, strict=true, description="Deposit amount")
   * @Rest\RequestParam(name="currency", requirements={"rule" = "[a-zA-Z]{3}", "error_message" = "Invalid currency" }, strict=true, description="Currency")
   *
   * @param ParamFetcher $paramFetcher
   */
  public function deposit(ParamFetcher $paramFetcher, $customer_id){

    try {

      //get all params
      $params = $paramFetcher->all(true);

      $this->validateInputParams($params);

      $customer = $this->customerRepository->retrieveCustomerById($customer_id);

      $deposit = $this->depositRepository->deposit($customer, floatval($params['amount']));

      return View::create($deposit,Response::HTTP_OK);

    } catch (\RuntimeException $e){
      return View::create(['errors' =>  [$e->getMessage()]], Response::HTTP_BAD_REQUEST);
    } catch (InputValidationException $e) {
      return View::create(['errors' => [$e->getMessage()]], Response::HTTP_BAD_REQUEST);
    } catch (Exception $e){
      return View::create(['errors' => ["unexpected.error"]], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

  }



}
