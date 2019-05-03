<?php

namespace Sl;

use Exception;
use Trafiklab\Common\Model\Contract\RoutePlanningResponse;
use Trafiklab\Common\Model\Contract\TimeTableResponse;
use Trafiklab\Common\Model\Exceptions\KeyRequiredException;
use Trafiklab\Sl\Internal\SlClient;
use Trafiklab\Sl\Model\SlRoutePlanningRequest;
use Trafiklab\Sl\Model\SlTimeTableRequest;

class SlWrapper
{

    private static $_instance;

    private $_key_reseplanerare;
    private $_key_stolptidstabeller;
    private $_slClient;

    private function __construct($_slClient = null)
    {
        // Private constructor for Singleton pattern
        $this->_slClient = $_slClient;
        if ($this->_slClient == null) {
            $this->_slClient = new SlClient();
        }
    }

    public static function getInstance(): slWrapper
    {
        if (self::$_instance == null) {
            self::$_instance = new slWrapper();
        }
        return self::$_instance;
    }

    public function registerRoutePlanningApiKey(string $key): void
    {
        $this->_key_reseplanerare = $key;
    }

    public function registerTimeTablesApiKey(string $key): void
    {
        $this->_key_stolptidstabeller = $key;
    }


    public function registerUserAgent(string $userAgent): void
    {
        $this->_slClient->setApplicationUserAgent($userAgent);
    }

    /**
     * @param SlTimeTableRequest $request
     *
     * @return TimeTableResponse
     * @throws Exception
     */
    public function getTimeTable(SlTimeTableRequest $request): TimeTableResponse
    {
        $this->requireValidTimeTablesKey();
        return $this->_slClient->getTimeTable($this->_key_stolptidstabeller, $request);
    }

    /**
     * @param SlRoutePlanningRequest $request
     *
     * @return RoutePlanningResponse
     * @throws Exception
     */
    public function getRoutePlanning(SlRoutePlanningRequest $request): RoutePlanningResponse
    {
        $this->requireValidRouteplannerKey();
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