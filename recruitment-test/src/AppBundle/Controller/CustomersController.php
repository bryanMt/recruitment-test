<?php

namespace AppBundle\Controller;

use Alcohol\ISO3166\ISO3166;
use AppBundle\Entity\Customer;
use AppBundle\Exception\InputValidationException;
use AppBundle\Exception\InvalidCountryException;
use AppBundle\Exception\InvalidEmailException;
use AppBundle\Exception\NonUniqueEmailException;
use AppBundle\Repository\CustomerRepository;
use DomainException;
use Exception;
use FOS\RestBundle\Request\ParamFetcher;
use OutOfBoundsException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;

class CustomersController extends FOSRestController
{


  /**
   * Country validator 3rd party library
   *
   * @var ISO3166
   */
  private $countryValidator;

  /**
   * Provides a 'layer of abstraction' for DBAL
   *
   * @var
   */
  private $customerRepository;

  /**
   * CustomersController constructor.
   *
   * @param ISO3166 $countryValidator
   * @param CustomerRepository $customerRepository
   */
  public function __construct(ISO3166 $countryValidator,
                              CustomerRepository $customerRepository)
  {

    $this->countryValidator = $countryValidator;
    $this->customerRepository = $customerRepository;

  }

  //***************************************
  //*******   ENDPOINTS  ******************
  //***************************************

  /**
   *
   * @Rest\Post("/customer")
   * @Rest\RequestParam(name="gender", requirements="(male|female)", description="Gender.")
   * @Rest\RequestParam(name="firstname", requirements="[a-z]+", description="Firstname.")
   * @Rest\RequestParam(name="lastname", requirements="[a-z]+", description="Lastname.")
   * @Rest\RequestParam(name="country", requirements="[a-zA-Z]{2}", description="country.")
   * @Rest\RequestParam(name="email", description="email.")
   *
   * @param ParamFetcher $paramFetcher
   */
  public function addNewCustomer(ParamFetcher $paramFetcher){

    //get all params
    $params = $paramFetcher->all(true);

    try {

      $this->validateInputParams($params);

      $customer = Customer::from_array($params);
      $customer = $this->customerRepository->addNewCustomer($customer);

      return View::create($customer,Response::HTTP_CREATED);

    } catch (InputValidationException $e) {
      return View::create(['errors' => [$e->getMessage()]], Response::HTTP_BAD_REQUEST);
    } catch (Exception $e){
      return View::create(['errors' => ["unexpected.error"]], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  /**
   *
   * @Rest\Put("/customer/{customer_id}")
   * @Rest\RequestParam(name="gender", requirements="(male|female)", nullable=true, description="Gender.")
   * @Rest\RequestParam(name="firstname", requirements="[a-z]+", nullable=true, description="Firstname.")
   * @Rest\RequestParam(name="lastname", requirements="[a-z]+", nullable=true, description="Lastname.")
   * @Rest\RequestParam(name="country", requirements="[a-zA-Z]{2}", nullable=true, description="country.")
   * @Rest\RequestParam(name="email", description="email.", nullable=true)
   *
   * @param ParamFetcher $paramFetcher
   */
  public function editCustomer(ParamFetcher $paramFetcher, $customer_id){

    //get all params
    $params = $paramFetcher->all(true);

    try {

      $params = $this->cleanUpdateParams($params);

      $this->validateInputParams($params);

      if (!empty($params)){

        $customer = $this->customerRepository->retrieveCustomerById($customer_id);
        $updatedCustomer = $customer->updateFromArray($params);
        $this->customerRepository->updateCustomer($updatedCustomer);

      }

      return View::create($updatedCustomer,Response::HTTP_OK);

    } catch (InputValidationException $e) {
      return View::create(['errors' => [$e->getMessage()]], Response::HTTP_BAD_REQUEST);
    } catch (Exception $e){
      return View::create(['errors' => ["unexpected.error"]], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

  }

  //**********************************************
  //*******   PRIVATE FUNCTIONS ******************
  //**********************************************

  /**
   *
   * Returns true if a valid ISO 2 country code
   *
   * @return bool
   */
  private function isValidIso2CountryCode(string $countryCode){
    try {
      $this->countryValidator->getByAlpha2($countryCode);
      return true;
    } catch (DomainException $e) {
      return false;
    } catch (OutOfBoundsException $e) {
      return false;
    }
  }


  /**
   * Validates input parameters
   *
   * @param array $parameters
   *
   * @throws InvalidCountryException
   * @throws InvalidEmailException
   */
  private function validateInputParams(Array $parameters){

    if (isset($parameters['country']) && !$this->isValidIso2CountryCode($parameters['country'])){
      throw new InvalidCountryException();
    }

    if (isset($parameters['email']) && !filter_var($parameters['email'], FILTER_VALIDATE_EMAIL)) {
      throw new InvalidEmailException();
    }

    if (isset($parameters['email']) && $this->customerRepository->emailExists($parameters['email'])){
      throw new NonUniqueEmailException();
    }

  }

  /**
   * @param array $parameters
   * @return array
   */
  private function cleanUpdateParams(Array $parameters){
    foreach ($parameters as $paramName => $paramValue){
      if (is_null($parameters[$paramName])){
        unset($parameters[$paramName]);
      }
    }
    return $parameters;
  }

}