<?php


namespace AppBundle\Entity;

use AppBundle\Enum\Gender;
use AppBundle\Exception\InvalidGenderException;


class Customer {

  private $id;

  private $gender;

  private $firstName;

  private $lastName;

  private $country;

  private $email;

  private $bonus;


  private function __construct(){
    $this->bonus = mt_rand(5,20);
  }

  /**
   * @return int
   */
  public function getId() : int {
    return $this->id;
  }

  /**
   * @param int $id
   */
  public function setId(int $id){
    $this->id = $id;
  }

  /**
   * @return string
   */
  public function getGender() : string{
    return $this->gender;
  }

  /**
   * @param string $gender
   *
   * @throws InvalidGenderException
   */
  public function setGender(string $gender){
    if (!in_array($gender, [Gender::MALE, Gender::FEMALE])){
      throw new InvalidGenderException();
    }

    $this->gender = $gender;
  }

  /**
   * @return string
   */
  public function getFirstname() : string {
    return $this->firstName;
  }

  /**
   * @param string $firstname
   */
  public function setFirstname(string $firstname){
    $this->firstName = $firstname;
  }

  /**
   * @return string
   */
  public function getLastname() : string {
    return $this->lastName;
  }

  /**
   * @param string $lastname
   */
  public function setLastname(string $lastname){
    $this->lastName = $lastname;
  }

  /**
   * @return string
   */
  public function getCountry() : string {
    return $this->country;
  }


  /**
   * @param string $country
   */
  public function setCountry(string $country){
    $this->country = $country;
  }

  /**
   * @param string $email
   */
  public function setEmail(string $email){
    $this->email = $email;
  }

  /**
   * @return string
   */
  public function getEmail() : string {
    return $this->email;
  }

  /**
   * @return float
   */
  public function getBonus() : float {
    return $this->bonus;
  }

  /**
   * @param float $bonus
   */
  private function setBonus(float $bonus) {
    $this->bonus = $bonus;
  }


  /**
   * Creates a Customer instance from the given array of input values
   *
   * @param array $params
   * @return Customer
   */
  public static function from_array(Array $params){

    $customerInstance = new Customer();

    $customerInstance->setGender($params['gender']);
    $customerInstance->setFirstname($params['firstname']);
    $customerInstance->setLastname($params['lastname']);
    $customerInstance->setCountry($params['country']);
    $customerInstance->setEmail($params['email']);

    if (isset($params['bonus'])){
      $customerInstance->setBonus($params['bonus']);
    }

    if (isset($params['id'])){
      $customerInstance->setId($params['id']);
    }

    return $customerInstance;
  }

  /**
   * Updates the instances using the updated values provided in $params array
   *
   * @param array $params
   */
  public function updateFromArray(Array $params){
    foreach ($params as $paramName => $newValue){
      $functionName = ucwords("set$paramName");
      $this->$functionName($newValue);
    }

    return $this;
  }


}