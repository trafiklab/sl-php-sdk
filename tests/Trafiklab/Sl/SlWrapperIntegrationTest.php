<?php

namespace Trafiklab\Sl;

use Exception;
use PHPUnit_Framework_TestCase;
use Sl\SlWrapper;
use Trafiklab\Common\Model\Enum\TimeTableType;
use Trafiklab\Common\Model\Enum\TransportType;
use Trafiklab\Sl\Model\SlTimeTableRequest;
use Trafiklab\Sl\Model\Enum\SlTransportType;

class SlWrapperIntegrationTest extends PHPUnit_Framework_TestCase
{
    private $_TIMETABLES_API_KEY = "";

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $keys = $this->getTestKeys();
        $this->_TIMETABLES_API_KEY = $keys['SLREALTID4_API_KEY'];
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
        $departuresRequest->setStopId("1000");

        $slWrapper = SlWrapper::getInstance();
        $slWrapper->registerUserAgent("SDK Integration tests");
        $slWrapper->registerTimeTablesApiKey($this->_TIMETABLES_API_KEY);
        $response = $slWrapper->getTimeTable($departuresRequest);

        self::assertEquals(TimeTableType::DEPARTURES, $response->getType());
        self::assertEquals("SL", $response->getTimetable()[0]->getOperator());


        $departuresRequest = new SlTimeTableRequest();
        $departuresRequest->setStopId("1001"); // Nybroplan boats
        $departuresRequest->setTimeTableType(TimeTableType::DEPARTURES);

        $slWrapper = SlWrapper::getInstance();
        $slWrapper->registerUserAgent("SDK Integration tests");
        $slWrapper->registerTimeTablesApiKey($this->_TIMETABLES_API_KEY);
        $response = $slWrapper->getTimeTable($departuresRequest);

        foreach ($response->getTimetable() as $timeTableEntry) {
            // Expect only boats
            self::assertEquals(TransportType::SHIP, $timeTableEntry->getTransportType());
        }

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
