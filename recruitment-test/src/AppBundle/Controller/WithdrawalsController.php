<?php

 namespace AppBundle\Controller;

use AppBundle\Exception\InsufficientBalanceException;
use AppBundle\Repository\WithdrawalRepository;
use FOS\RestBundle\Request\ParamFetcher;
use AppBundle\Exception\InputValidationException;
use AppBundle\Repository\CustomerRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;


/**
 * Class WithdrawalsController
 * @package AppBundle\Controller
 */
class WithdrawalsController extends TransactionsController
{

  /**
   * Provides a layer of abstraction on top of DBAL
   *
   * @var CustomerRepository
   */
  private $withdrawalRepository;


  /**
   * WithdrawalsController constructor.
   *
   * @param CustomerRepository $customerRepository
   */
  public function __construct(CustomerRepository $customerRepository,
                              WithdrawalRepository $withdrawalRepository)
  {
    parent::__construct($customerRepository);
    $this->withdrawalRepository = $withdrawalRepository;
  }

  /**
   *
   * @Rest\Post("/withdrawal/{customer_id}")
   * @Rest\RequestParam(name="amount", requirements="^[-+]?\d+(\.\d+)?", description="Withdrawal amount.")
   * @Rest\RequestParam(name="currency", requirements="[a-zA-Z]{3}", description="Currency.")
   *
   * @param ParamFetcher $paramFetcher
   */
  public function withdraw(ParamFetcher $paramFetcher, $customer_id){

    //get all params
    $params = $paramFetcher->all(true);

    try {

      $this->validateInputParams($params);

      $customer = $this->customerRepository->retrieveCustomerById($customer_id);

      $deposit = $this->withdrawalRepository->withdrawal($customer, floatval($params['amount']));

      return View::create($deposit, Response::HTTP_OK);

    } catch (InsufficientBalanceException $e){
      return View::create(['errors' => [$e->getMessage()]], Response::HTTP_FORBIDDEN);
    } catch (InputValidationException $e) {
      return View::create(['errors' => [$e->getMessage()]], Response::HTTP_BAD_REQUEST);
    } catch (Exception $e){
      return View::create(['errors' => ["unexpected.error"]], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

  }


}
