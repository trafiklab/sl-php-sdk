<?php

namespace Sl;

use InvalidArgumentException;
use Trafiklab\Common\Model\Contract\PublicTransportApiWrapper;
use Trafiklab\Common\Model\Contract\RoutePlanningRequest;
use Trafiklab\Common\Model\Contract\RoutePlanningResponse;
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
use Trafiklab\Sl\Model\SlTimeTableRequest;

class SlWrapper implements PublicTransportApiWrapper
{
    private $_key_reseplanerare;
    private $_key_stolptidstabeller;
    private $_slClient;

    public function __construct()
    {
        $this->_slClient = new SlClient();
    }


    public function setRoutePlanningApiKey(string $key): void
    {
        $this->_key_reseplanerare = $key;
    }

    public function setTimeTablesApiKey(string $key): void
    {
        $this->_key_stolptidstabeller = $key;
    }


    public function setUserAgent(string $userAgent): void
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
            throw new InvalidArgumentException("ResRobot requires a SlTimeTableRequest object");
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
            throw new InvalidArgumentException("ResRobot requires a ResRobotRoutePlanningRequest object");
        }
        return $this->_slClient->getRoutePlanning($this->_key_reseplanerare, $request);
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
}