<?php

namespace Trafiklab\Sl;

use DateTime;
use Exception;
use PHPUnit_Framework_TestCase;
use Trafiklab\Common\Model\Enum\TimeTableType;
use Trafiklab\Common\Model\Enum\TransportType;
use Trafiklab\Common\Model\Exceptions\InvalidKeyException;
use Trafiklab\Common\Model\Exceptions\InvalidRequestException;
use Trafiklab\Common\Model\Exceptions\InvalidStopLocationException;
use Trafiklab\Common\Model\Exceptions\KeyRequiredException;
use Trafiklab\Sl\Model\SlRoutePlanningRequest;
use Trafiklab\Sl\Model\SlStopLocationLookupRequest;
use Trafiklab\Sl\Model\SlTimeTableRequest;

class SlWrapperIntegrationTest extends PHPUnit_Framework_TestCase
{
    private $_TIMETABLES_API_KEY;
    private $_ROUTEPLANNING_API_KEY;
    private $_STOPLOCATIONLOOKUP_API_KEY;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $keys = $this->getTestKeys();
        $this->_TIMETABLES_API_KEY = $keys['SLREALTID4_API_KEY'];
        $this->_ROUTEPLANNING_API_KEY = $keys['SLPLANNER31_API_KEY'];
        $this->_STOPLOCATIONLOOKUP_API_KEY = $keys['SLPLATSUPPSLAG_API_KEY'];
    }

    /**
     * @throws Exception
     */
    public function testGetDepartures()
    {
        if (empty($this->_TIMETABLES_API_KEY)) {
            $this->markTestIncomplete();
        }

        $departuresRequest = new SlTimeTableRequest();
        $departuresRequest->setStopId("9001");

        $slWrapper = new SlWrapper();
        $slWrapper->setUserAgent("SDK Integration tests");
        $slWrapper->setTimeTablesApiKey($this->_TIMETABLES_API_KEY);
        $response = $slWrapper->getTimeTable($departuresRequest);

        self::assertEquals(TimeTableType::DEPARTURES, $response->getType());
        self::assertEquals("SL", $response->getTimetable()[0]->getOperator());


        $departuresRequest = new SlTimeTableRequest();
        $departuresRequest->setStopId("1001"); // Nybroplan boats
        $departuresRequest->setTimeTableType(TimeTableType::DEPARTURES);

        $slWrapper = new SlWrapper();
        $slWrapper->setUserAgent("SDK Integration tests");
        $slWrapper->setTimeTablesApiKey($this->_TIMETABLES_API_KEY);
        $response = $slWrapper->getTimeTable($departuresRequest);

        foreach ($response->getTimetable() as $timeTableEntry) {
            // Expect only boats
            self::assertEquals(TransportType::SHIP, $timeTableEntry->getTransportType());
        }
    }

    /**
     * @throws Exception
     */
    public function testGetDepartures_invalidStationId_shouldThrowException()
    {
        if (empty($this->_TIMETABLES_API_KEY)) {
            $this->markTestIncomplete();
        }

        $this->expectException(InvalidStopLocationException::class);

        $departuresRequest = new SlTimeTableRequest();
        $departuresRequest->setStopId("123.56");
        $departuresRequest->setTimeTableType(TimeTableType::DEPARTURES);

        $slWrapper = new SlWrapper();
        $slWrapper->setUserAgent("SDK Integration tests");
        $slWrapper->setTimeTablesApiKey($this->_TIMETABLES_API_KEY);
        $slWrapper->getTimeTable($departuresRequest);

    }


    /**
     * @throws Exception
     */
    public function testGetDepartures_invalidApiKey_shouldThrowException()
    {
        $this->expectException(InvalidKeyException::class);
        $departuresRequest = new SlTimeTableRequest();
        $departuresRequest->setStopId("1001");
        $departuresRequest->setTimeTableType(TimeTableType::DEPARTURES);

        $slWrapper = new SlWrapper();
        $slWrapper->setUserAgent("SDK Integration tests");
        $slWrapper->setTimeTablesApiKey("ABC123");
        $slWrapper->getTimeTable($departuresRequest);
    }

    /**
     * @throws Exception
     */
    public function testGetDepartures_missingApiKey_shouldThrowException()
    {
        $this->expectException(KeyRequiredException::class);

        $departuresRequest = new SlTimeTableRequest();
        $departuresRequest->setStopId("1001");
        $departuresRequest->setTimeTableType(TimeTableType::DEPARTURES);

        $slWrapper = new SlWrapper();
        $slWrapper->setUserAgent("SDK Integration tests");
        $slWrapper->setTimeTablesApiKey("");
        $slWrapper->getTimeTable($departuresRequest);
    }

    /**
     * @throws Exception
     */
    public function testGetRoutePlanning_validParameters_shouldReturnResponse()
    {
        if (empty($this->_ROUTEPLANNING_API_KEY)) {
            $this->markTestIncomplete();
        }

        $queryTime = new DateTime();
        $queryTime->setTime(18, 0);

        $routePlanningRequest = new SlRoutePlanningRequest();
        $routePlanningRequest->setOriginStopId("9192");
        $routePlanningRequest->setDestinationStopId("1002");
        $routePlanningRequest->setDateTime($queryTime);

        $slWrapper = new SlWrapper();
        $slWrapper->setUserAgent("SDK Integration tests");
        $slWrapper->setRoutePlanningApiKey($this->_ROUTEPLANNING_API_KEY);
        $response = $slWrapper->getRoutePlanning($routePlanningRequest);

        self::assertTrue(count($response->getTrips()) > 0);
        $firstTripLegs = $response->getTrips()[0]->getLegs();
        self::assertEquals("9192", $firstTripLegs[0]->getOrigin()->getStopId());
        self::assertEquals("1002", end($firstTripLegs)->getDestination()->getStopId());
    }

    /**
     * @throws Exception
     */
    public function testGetRoutePlanning_WithVia_shouldReturnResponse()
    {
        if (empty($this->_ROUTEPLANNING_API_KEY)) {
            $this->markTestIncomplete();
        }

        $queryTime = new DateTime();
        $queryTime->setTime(18, 0);

        $routePlanningRequest = new SlRoutePlanningRequest();
        $routePlanningRequest->setOriginStopId("1002");
        $routePlanningRequest->setDestinationStopId("9192");
        $routePlanningRequest->setViaStopId("9180");
        $routePlanningRequest->setDateTime($queryTime);

        $slWrapper = new SlWrapper();
        $slWrapper->setUserAgent("SDK Integration tests");
        $slWrapper->setRoutePlanningApiKey($this->_ROUTEPLANNING_API_KEY);
        $response = $slWrapper->getRoutePlanning($routePlanningRequest);


        self::assertTrue(count($response->getTrips()) > 0);
        $firstTripLegs = $response->getTrips()[0]->getLegs();
        self::assertEquals("1002", $firstTripLegs[0]->getOrigin()->getStopId());
        self::assertEquals("9192", end($firstTripLegs)->getDestination()->getStopId());

        $foundViaStation = false;
        foreach ($response->getTrips()[0]->getLegs() as $leg) {
            if ($leg->getDestination()->getStopId() == "9180") {
                $foundViaStation = true;
            }
        }
        self::assertTrue($foundViaStation, "Failed to find via station in trip");
    }

    /**
     * @throws Exception
     */
    public function testGetRoutePlanning_invalidStationId_shouldThrowException()
    {
        if (empty($this->_ROUTEPLANNING_API_KEY)) {
            $this->markTestIncomplete();
        }

        $this->expectException(InvalidRequestException::class);
        $routePlanningRequest = new SlRoutePlanningRequest();
        $routePlanningRequest->setOriginStopId("1001");
        $routePlanningRequest->setDestinationStopId("0");

        $slWrapper = new SlWrapper();
        $slWrapper->setUserAgent("SDK Integration tests");
        $slWrapper->setRoutePlanningApiKey($this->_ROUTEPLANNING_API_KEY);
        $slWrapper->getRoutePlanning($routePlanningRequest);

        $this->expectException(InvalidStoplocationException::class);
        $routePlanningRequest = new SlRoutePlanningRequest();
        $routePlanningRequest->setOriginStopId("1001");
        $routePlanningRequest->setDestinationStopId("45.45");
        $slWrapper->getRoutePlanning($routePlanningRequest);
    }

    /**
     * @throws Exception
     */
    public function testGetRoutePlanning_invalidApiKey_shouldThrowException()
    {
        $this->expectException(InvalidKeyException::class);

        $routePlanningRequest = new SlRoutePlanningRequest();
        $routePlanningRequest->setOriginStopId("1001");
        $routePlanningRequest->setDestinationStopId("2002");

        $slWrapper = new SlWrapper();
        $slWrapper->setUserAgent("SDK Integration tests");
        $slWrapper->setRoutePlanningApiKey("ABC123");
        $slWrapper->getRoutePlanning($routePlanningRequest);
    }

    /**
     * @throws Exception
     */
    public function testGetRoutePlanning_missingApiKey_shouldThrowException()
    {
        $this->expectException(KeyRequiredException::class);

        $queryTime = new DateTime();
        $queryTime->setTime(18, 0);

        $routePlanningRequest = new SlRoutePlanningRequest();
        $routePlanningRequest->setOriginStopId("1001");
        $routePlanningRequest->setDestinationStopId("2002");

        $slWrapper = new SlWrapper();
        $slWrapper->setUserAgent("SDK Integration tests");
        $slWrapper->setRoutePlanningApiKey("");
        $slWrapper->getRoutePlanning($routePlanningRequest);
    }

    /**
     * @throws Exception
     */
    public function testGetRoutePlanning_invalidDate_shouldThrowException()
    {
        if (empty($this->_ROUTEPLANNING_API_KEY)) {
            $this->markTestIncomplete();
        }

        $this->expectException(InvalidRequestException::class);

        $queryTime = new DateTime();
        $queryTime->setDate(2100, 1, 1);

        $routePlanningRequest = new SlRoutePlanningRequest();
        $routePlanningRequest->setOriginStopId("1001");
        $routePlanningRequest->setDestinationStopId("2002");
        $routePlanningRequest->setDateTime($queryTime);

        $slWrapper = new SlWrapper();
        $slWrapper->setUserAgent("SDK Integration tests");
        $slWrapper->setRoutePlanningApiKey($this->_ROUTEPLANNING_API_KEY);
        $slWrapper->getRoutePlanning($routePlanningRequest);
    }


    public function testGetStopLocation_searchForCity_shouldReturnLargestStopsFirst()
    {
        if (empty($this->_STOPLOCATIONLOOKUP_API_KEY)) {
            $this->markTestIncomplete();
        }
        $slWrapper = new SlWrapper();
        $slWrapper->setUserAgent("SDK Integration tests");
        $slWrapper->setStopLocationLookupApiKey($this->_STOPLOCATIONLOOKUP_API_KEY);

        $stopLocationLookupRequest = new SlStopLocationLookupRequest();
        $stopLocationLookupRequest->setSearchQuery("Stockholm");
        $response = $slWrapper->lookupStopLocation($stopLocationLookupRequest);
        /**
         *  ...
         * {
         * "Name": "Stockholm City (Stockholm)",
         * "SiteId": "1080",
         * "Type": "Station",
         * "X": "18059293",
         * "Y": "59331008"
         * },
         * {
         * "Name": "Stockholms central (Stockholm)",
         * "SiteId": "9000",
         * "Type": "Station",
         * "X": "18057657",
         * "Y": "59331134"
         * },
         * {
         * "Name": "Stockholm Odenplan (Stockholm)",
         * "SiteId": "1079",
         * "Type": "Station",
         * "X": "18045683",
         * "Y": "59343116"
         * },
         * {
         * "Name": "Stockholms östra (Stockholm)",
         * "SiteId": "9600",
         * "Type": "Station",
         * "X": "18071707",
         * "Y": "59345543"
         * },
         *  ...
         */
        self::assertEquals("1080", $response->getFoundStopLocations()[0]->getId());
        self::assertEquals("9000", $response->getFoundStopLocations()[1]->getId());

        $stopLocationLookupRequest = new SlStopLocationLookupRequest();
        $stopLocationLookupRequest->setSearchQuery("Sollentuna");
        $response = $slWrapper->lookupStopLocation($stopLocationLookupRequest);
        /**
         *  ...
         * {
         * "Name": "Sollentuna (Sollentuna)",
         * "SiteId": "9506",
         * "Type": "Station",
         * "X": "17948186",
         * "Y": "59429592"
         * },
         * {
         * "Name": "Sollentuna centrum (Sollentuna)",
         * "SiteId": "9506",
         * "Type": "Station",
         * "X": "17948186",
         * "Y": "59429592"
         * },
         * {
         * "Name": "Sollentuna station (Sollentuna)",
         * "SiteId": "9506",
         * "Type": "Station",
         * "X": "17948186",
         * "Y": "59429592"
         * },
         * {
         * "Name": "Sollentunavallen (Sollentuna)",
         * "SiteId": "5530",
         * "Type": "Station",
         * "X": "17950146",
         * "Y": "59435750"
         * },
         *  ...
         */
        self::assertEquals("9506", $response->getFoundStopLocations()[0]->getId());
        self::assertEquals("Sollentuna (Sollentuna)", $response->getFoundStopLocations()[0]->getName());

    }

    /**
     * Read test keys from a .testkeys file.
     *
     * @return array
     */
    private function getTestKeys(): array
    {

        try {
            $testKeys = [];
            $testKeysFile = file_get_contents(".testkeys");

            foreach (explode(PHP_EOL, $testKeysFile) as $line) {
                if (empty($line) || strpos($line, '#') === 0 || strpos($line, '=') === false) {
                    continue;
                }

                $keyvalue = explode('=', $line);
                $testKeys[$keyvalue[0]] = $keyvalue[1];
            }

            return $testKeys;
        } catch (Exception $exception) {
            return [];
        }
    }
}
