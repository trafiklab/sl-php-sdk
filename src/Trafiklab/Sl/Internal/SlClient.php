<?php


namespace Trafiklab\Sl\Internal;

use Trafiklab\Common\Internal\CurlWebClient;
use Trafiklab\Common\Internal\WebClient;
use Trafiklab\Common\Model\Contract\TimeTableResponse;
use Trafiklab\Common\Model\Enum\TimeTableType;
use Trafiklab\Sl\Model\SlRoutePlanningRequest;
use Trafiklab\Sl\Model\SlRoutePlanningResponse;
use Trafiklab\Sl\Model\SlTimeTableRequest;
use Trafiklab\Sl\Model\SlTimeTableResponse;

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
     * @throws \Exception
     */
    public function getTimeTable(string $key, SlTimeTableRequest $request): TimeTableResponse
    {

        $endpoint = self::DEPARTURES_ENDPOINT;
        if ($request->getTimeTableType() == TimeTableType::ARRIVALS) {
            throw new \Exception("This API does cannot provide arrivals information", 400);
        }

        $parameters = [
            "key" => $key,
            "SiteId" => $request->getStopId(),
            "passlist" => "0",
        ];

        if ($request->getVehicleFilter() > 0) {
            $parameters['products'] = $request->getVehicleFilter();
        }

        if ($request->getOperatorFilter() != null) {
            $parameters['operators'] = join(',', $request->getOperatorFilter());
        }

        $response = $this->_webClient->makeRequest($endpoint, $parameters);
        $json = json_decode($response->getBody(), true);
        return new SlTimeTableResponse($json);
    }

    /**
     * @param string $applicationUserAgent
     */
    public function setApplicationUserAgent(string $applicationUserAgent): void
    {
        $this->applicationUserAgent = $applicationUserAgent;
    }

    /**
     * @param                      $key
     * @param SlRoutePlanningRequest $request
     *
     * @return SlRoutePlanningResponse
     * @throws \Exception
     */
    public function getRoutePlanning($key, SlRoutePlanningRequest $request): SlRoutePlanningResponse
    {
        $parameters = [
            "key" => $key,
            "originId" => $request->getOriginId(),
            "destId" => $request->getDestinationId(),
            "date" => $request->getDateTime()->format("Y-m-d"),
            "time" => $request->getDateTime()->format("H:i"),
            "lang" => $request->getLang(),
            "passlist" => "1",
        ];


        if ($request->getVehicleFilter() > 0) {
            $parameters['products'] = $request->getVehicleFilter();
        }

        if ($request->getOperatorFilter() != null) {
            $parameters['operators'] = join(',', $request->getOperatorFilter());
        }

        if ($request->getViaId() != null) {
            $parameters['viaId'] =  $request->getViaId();
        }

        $response = $this->_webClient->makeRequest(self::TRIPS_ENDPOINT, $parameters);
        $json = json_decode($response->getBody(), true);
        return new SlRoutePlanningResponse($json);
    }


    private function getUserAgent()
    {
        return $this->applicationUserAgent . " VIA " . self::SDK_USER_AGENT;
    }
}