<?php

 namespace AppBundle\Controller;

 use AppBundle\Exception\InvalidDateException;
 use AppBundle\Service\StatisticsService;

 use AppBundle\Exception\InputValidationException;
 use AppBundle\Exception\InvalidCountryException;
 use AppBundle\Exception\InvalidEmailException;
 use DateTime;
 use Exception;
 use FOS\RestBundle\Request\ParamFetcher;
 use FOS\RestBundle\Controller\Annotations as Rest;
 use FOS\RestBundle\Controller\FOSRestController;
 use Symfony\Component\HttpFoundation\Response;
 use FOS\RestBundle\View\View;


 class StatisticsController extends FOSRestController {

   private $statisticsService;


   /**
    * StatisticsController constructor.
    * @param StatisticsService $statisticsService
    */
   public function __construct(StatisticsService $statisticsService){
     $this->statisticsService = $statisticsService;
   }

   /**
    *
    * @Rest\Get("/statistics")
    * @Rest\QueryParam(name="dateFrom", nullable = true, description="Date from", default = null)
    * @Rest\QueryParam(name="dateTo", nullable = true, description="Date to.", default = null)
    *
    * @param ParamFetcher $paramFetcher
    */
   public function retrieveTxsGroupedByDateAndCountry(ParamFetcher $paramFetcher){

     //get all params
     $params = $paramFetcher->all(true);

     try {

       $this->validateInputParams($params);

       $statistics = $this->statisticsService->getTxStatsGroupedByCountryAndDay($params['dateFrom'], $params['dateTo']);

       return View::create($statistics,Response::HTTP_OK);

     } catch (InputValidationException $e) {
       return View::create(['errors' => [$e->getMessage()]], Response::HTTP_BAD_REQUEST);
     } catch (Exception $e){
       return View::create(['errors' => ["unexpected.error"]], Response::HTTP_INTERNAL_SERVER_ERROR);
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
   private function validateInputParams(Array $params){

     foreach ($params as $paramName => $paramValue){
       if (!is_null($paramValue) && !$this->isValidDate($paramValue)){
         throw new InvalidDateException();
       }
     }

   }

   /**
    * @param $date
    * @param string $format
    * @return bool
    */
   private function isValidDate($date, $format = 'Y-m-d') : bool {
     $d = DateTime::createFromFormat($format, $date);
     return $d && $d->format($format) == $date;
   }




 }