<?php


namespace Trafiklab\Sl\Internal;

use Exception;
use Trafiklab\Common\Internal\CurlWebClient;
use Trafiklab\Common\Internal\WebClient;
use Trafiklab\Common\Model\Contract\RoutePlanningRequest;
use Trafiklab\Common\Model\Contract\TimeTableResponse;
use Trafiklab\Common\Model\Contract\WebResponse;
use Trafiklab\Common\Model\Enum\RoutePlanningSearchType;
use Trafiklab\Common\Model\Enum\TimeTableType;
use Trafiklab\Common\Model\Exceptions\InvalidKeyException;
use Trafiklab\Common\Model\Exceptions\InvalidRequestException;
use Trafiklab\Common\Model\Exceptions\InvalidStoplocationException;
use Trafiklab\Common\Model\Exceptions\KeyRequiredException;
use Trafiklab\Common\Model\Exceptions\QuotaExceededException;
use Trafiklab\Common\Model\Exceptions\RequestTimedOutException;
use Trafiklab\Common\Model\Exceptions\ServiceUnavailableException;
use Trafiklab\Sl\Model\SlRoutePlanningResponse;
use Trafiklab\Sl\Model\SlTimeTableRequest;
use Trafiklab\Sl\Model\SlTimeTableResponse;

/**
 * @internal Builds requests and gets data.
 * @package  Trafiklab\Sl\Internal
 */
class SlClient
{

    public const DEPARTURES_ENDPOINT = "http://api.sl.se/api2/realtimedeparturesV4.json";
    public const TRIPS_ENDPOINT = "https://api.sl.se/api2/TravelplannerV3_1/trip.json";
    public const SDK_USER_AGENT = "Trafiklab/Sl-php-sdk";
    private $applicationUserAgent = "Unknown";
    /**
     * @var WebClient
     */
    private $_webClient;

    public function __construct(WebClient $webClient = null)
    {
        $this->_webClient = $webClient;
        if ($webClient == null) {
            $this->_webClient = new CurlWebClient($this->getUserAgent());
        }
    }


    /**
     * @param string             $key
     * @param SlTimeTableRequest $request
     *
     * @return TimeTableResponse
     * @throws InvalidKeyException
     * @throws InvalidRequestException
     * @throws InvalidStoplocationException
     * @throws KeyRequiredException
     * @throws QuotaExceededException
     * @throws ServiceUnavailableException
     * @throws RequestTimedOutException
     * @throws Exception
     */
    public function getTimeTable(string $key, SlTimeTableRequest $request): TimeTableResponse
    {

        $endpoint = self::DEPARTURES_ENDPOINT;
        if ($request->getTimeTableType() == TimeTableType::ARRIVALS) {
            throw new Exception("This API does cannot provide arrivals information", 400);
        }

        $parameters = [
            "key" => $key,
            "SiteId" => $request->getStopId(),
            "passlist" => "0",
        ];

        if ($request->getVehicleFilter() > 0) {
            $parameters['products'] = $request->getVehicleFilter();
        }

        $response = $this->_webClient->makeRequest($endpoint, $parameters);
        $json = json_decode($response->getResponseBody(), true);

        $this->validateSlResponse($response, $json, "SL departures");
        return new SlTimeTableResponse($response, $json);
    }

    /**
     * @param string $applicationUserAgent
     */
    public function setApplicationUserAgent(string $applicationUserAgent): void
    {
        $this->applicationUserAgent = $applicationUserAgent;
    }

    /**
     * @param                        $key
     * @param RoutePlanningRequest   $request
     *
     * @return SlRoutePlanningResponse
     * @throws InvalidKeyException
     * @throws InvalidRequestException
     * @throws InvalidStoplocationException
     * @throws KeyRequiredException
     * @throws QuotaExceededException
     * @throws RequestTimedOutException
     * @throws ServiceUnavailableException
     */
    public function getRoutePlanning($key, RoutePlanningRequest $request): SlRoutePlanningResponse
    {
        $searchForArrival = "0";
        if ($request->getRoutePlanningSearchType() == RoutePlanningSearchType::ARRIVE_AT_SPECIFIED_TIME) {
            $searchForArrival = "1";
        }

        $parameters = [
            "key" => $key,
            "originExtId" => $request->getOriginStopId(),
            "destExtId" => $request->getDestinationStopId(),
            "date" => $request->getDateTime()->format("Y-m-d"),
            "time" => $request->getDateTime()->format("H:i"),
            "lang" => $request->getLang(),
            "searchForArrival" => $searchForArrival,
            "passlist" => "1",
        ];


        if ($request->getVehicleFilter() > 0) {
            $parameters['products'] = $request->getVehicleFilter();
        }

        if ($request->getViaStopId() != null) {
            $parameters['viaId'] = $request->getViaStopId();
        }

        $response = $this->_webClient->makeRequest(self::TRIPS_ENDPOINT, $parameters);
        $json = json_decode($response->getResponseBody(), true);

        $this->validateSlResponse($response, $json, "SL reseplanerare");
        return new SlRoutePlanningResponse($response, $json);
    }


    private function getUserAgent()
    {
        return $this->applicationUserAgent . " VIA " . self::SDK_USER_AGENT;
    }

    /**
     * @param WebResponse $response
     * @param array       $json
     * @param string      $api
     *
     * @throws InvalidKeyException
     * @throws InvalidRequestException
     * @throws InvalidStoplocationException
     * @throws KeyRequiredException
     * @throws QuotaExceededException
     * @throws ServiceUnavailableException
     */
    private function validateSlResponse(WebResponse $response, array $json, string $api)
    {
        if (key_exists("StatusCode", $json) && $json['StatusCode'] != 0) {
            switch ($json['StatusCode']) {
                case '1001':
                    throw new KeyRequiredException();
                    break;
                case '1002':
                    throw new InvalidKeyException($response->getRequestParameter('key'));
                    break;
                case '1003':
                    throw new InvalidRequestException("Invalid API",
                        $response->getRequestParameters());
                    break;
                case '1004':
                    throw new ServiceUnavailableException($response->getUrl(),
                        "The service is currently unavailable for request with a priority over 2.");
                case '1005':
                    throw new InvalidKeyException($response->getRequestParameter('key'));
                    break;
                case '1006':
                    throw new QuotaExceededException($api,
                        $response->getRequestParameter('key'), "Requests per minute exceeded");
                case '1007':
                    throw new QuotaExceededException($api,
                        $response->getRequestParameter('key'), "Requests per month exceeded");
                    break;
                case '5321':
                case '5322':
                case '5323':
                    throw new InvalidRequestException("One or more parameters are invalid",
                        $response->getRequestParameters());
                    break;
                case '4001':
                    throw new InvalidStoplocationException($response->getRequestParameters());
                    break;
                default:
                    throw new InvalidRequestException($json['Message'], $response->getRequestParameters());
                    break;
            }
        }
    }
}