<?php

namespace Trafiklab\Sl;

use InvalidArgumentException;
use Trafiklab\Common\Model\Contract\PublicTransportApiWrapper;
use Trafiklab\Common\Model\Contract\RoutePlanningRequest;
use Trafiklab\Common\Model\Contract\RoutePlanningResponse;
use Trafiklab\Common\Model\Contract\StopLocationLookupRequest;
use Trafiklab\Common\Model\Contract\StopLocationLookupResponse;
use Trafiklab\Common\Model\Contract\TimeTableRequest;
use Trafiklab\Common\Model\Contract\TimeTableResponse;
use Trafiklab\Common\Model\Exceptions\InvalidKeyException;
use Trafiklab\Common\Model\Exceptions\InvalidRequestException;
use Trafiklab\Common\Model\Exceptions\InvalidStoplocationException;
use Trafiklab\Common\Model\Exceptions\KeyRequiredException;
use Trafiklab\Common\Model\Exceptions\QuotaExceededException;
use Trafiklab\Common\Model\Exceptions\RequestTimedOutException;
use Trafiklab\Common\Model\Exceptions\ServiceUnavailableException;
use Trafiklab\Sl\Internal\SlClient;
use Trafiklab\Sl\Model\SlRoutePlanningRequest;
use Trafiklab\Sl\Model\SlStopLocationLookupRequest;
use Trafiklab\Sl\Model\SlTimeTableRequest;

class SlWrapper implements PublicTransportApiWrapper
{
    private $_key_reseplanerare;
    private $_key_stolptidstabeller;
    private $_key_platsuppslag;
    private $_slClient;

    public function __construct()
    {
        $this->_slClient = new SlClient();
    }


    /**
     * Set the API key used for finding routes from A to B.
     * For SL, this a key to SL Reseplanerare 3.1.
     *
     * @param string $key The API key to use.
     */
    public function setRoutePlanningApiKey(?string $key): void
    {
        $this->_key_reseplanerare = $key;
    }

    /**
     * Set the API key used for getting departures and arrivals boards.
     * For SL, this a key to SL Realtid 4.
     *
     * @param string $key The API key to use.
     */
    public function setTimeTablesApiKey(?string $key): void
    {
        $this->_key_stolptidstabeller = $key;
    }


    /**
     * Set the API key used for finding stop locations.
     * For SL, this a key to SL Platsuppslag.
     *
     * @param string $key The API key to use.
     */
    public function setStopLocationLookupApiKey(?string $key): void
    {
        $this->_key_platsuppslag = $key;
    }

    public function setUserAgent(?string $userAgent): void
    {
        $this->_slClient->setApplicationUserAgent($userAgent);
    }

    /**
     * @param TimeTableRequest $request
     *
     * @return TimeTableResponse
     * @throws KeyRequiredException
     * @throws InvalidKeyException
     * @throws InvalidRequestException
     * @throws InvalidStoplocationException
     * @throws QuotaExceededException
     * @throws RequestTimedOutException
     * @throws ServiceUnavailableException
     */
    public function getTimeTable(TimeTableRequest $request): TimeTableResponse
    {
        $this->requireValidTimeTablesKey();

        if (!$request instanceof SlTimeTableRequest) {
            throw new InvalidArgumentException("SL requires an SlTimeTableRequest object");
        }

        return $this->_slClient->getTimeTable($this->_key_stolptidstabeller, $request);
    }

    /**
     * @param RoutePlanningRequest $request
     *
     * @return RoutePlanningResponse
     * @throws InvalidKeyException
     * @throws InvalidRequestException
     * @throws InvalidStoplocationException
     * @throws KeyRequiredException
     * @throws QuotaExceededException
     * @throws RequestTimedOutException
     * @throws ServiceUnavailableException
     */
    public function getRoutePlanning(RoutePlanningRequest $request): RoutePlanningResponse
    {
        $this->requireValidRouteplannerKey();

        if (!$request instanceof SlRoutePlanningRequest) {
            throw new InvalidArgumentException("SL requires an SlRoutePlanningRequest object");
        }
        return $this->_slClient->getRoutePlanning($this->_key_reseplanerare, $request);
    }


    /**
     * Find a stoplocation based on (a part of) its name.
     *
     * @param SlStopLocationLookupRequest $request The request object containing the query parameters.
     *
     * @return StopLocationLookupResponse The response from the API.
     *
     * @return RoutePlanningResponse
     * @throws InvalidKeyException
     * @throws InvalidRequestException
     * @throws InvalidStoplocationException
     * @throws KeyRequiredException
     * @throws QuotaExceededException
     * @throws RequestTimedOutException
     * @throws ServiceUnavailableException
     */
    public function lookupStopLocation(StopLocationLookupRequest $request): StopLocationLookupResponse
    {
        $this->requireValidLookupStopLocationKey();

        if (!$request instanceof SlStopLocationLookupRequest) {
            throw new InvalidArgumentException("SL requires an SlStopLocationLookupRequest object");
        }

        return $this->_slClient->lookupStopLocation($this->_key_platsuppslag, $request);
    }

    public function createTimeTableRequestObject(): TimeTableRequest
    {
        return new SlTimeTableRequest();
    }

    public function createRoutePlanningRequestObject(): RoutePlanningRequest
    {
        return new SlRoutePlanningRequest();
    }

    public function createStopLocationLookupRequestObject(): StopLocationLookupRequest
    {
        return new SlStopLocationLookupRequest();
    }

    /**
     * @throws KeyRequiredException
     */
    private function requireValidTimeTablesKey()
    {
        if ($this->_key_stolptidstabeller == null || empty($this->_key_stolptidstabeller)) {
            throw new KeyRequiredException(
                "No Timetables API key configured. Obtain a free key at https://www.trafiklab.se/api");
        }
    }

    /**
     * @throws KeyRequiredException
     */
    private function requireValidRouteplannerKey()
    {
        if ($this->_key_reseplanerare == null || empty($this->_key_reseplanerare)) {
            throw new KeyRequiredException(
                "No Routeplanner API key configured. Obtain a free key at https://www.trafiklab.se/api");
        }
    }

    /**
     * @throws KeyRequiredException
     */
    private function requireValidLookupStopLocationKey()
    {
        if ($this->_key_platsuppslag == null || empty($this->_key_platsuppslag)) {
            throw new KeyRequiredException(
                "No StopLocationLookup API key configured. Obtain a free key at https://www.trafiklab.se/api");
        }
    }
}