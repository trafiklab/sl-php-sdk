<?php


namespace Trafiklab\Sl\Model;

use Trafiklab\Common\Model\Contract\TimeTableEntryWithRealTime;
use Trafiklab\Common\Model\Contract\TimeTableResponseWithRealTime;
use Trafiklab\Common\Model\Enum\TimeTableType;

class SlTimeTableResponse implements TimeTableResponseWithRealTime
{

    private $_timetable = [];

    /**
     * Create a SlTimeTableResponse from Sls JSON response.
     *
     * @param array $json The API output to parse.
     *
     * @throws \Exception
     */
    public function __construct(array $json)
    {
        $this->parseApiResponse($json);
    }

    /**
     * @return TimeTableEntryWithRealTime[] The requested timetable as an array of timetable entries.
     */
    public function getTimetable(): array
    {
        return $this->_timetable;
    }

    /**
     * @return int The type of the stops in this timetable.
     */
    public function getType(): int
    {
        // SL only supports departures
        return TimeTableType::DEPARTURES;
    }

    /**
     * @param array $json The API output to parse.
     *
     * @throws \Exception
     */
    private function parseApiResponse(array $json): void
    {
        if ($json['StatusCode'] > 0) {
            throw new \Exception('SL Departures threw an error: ' . $json['Message'], 500);
        }

        foreach ($json['ResponseData']['Metros'] as $key => $entry) {
            $this->_timetable[] = new SlTimeTableEntry($entry);
        }

        foreach ($json['ResponseData']['Buses'] as $key => $entry) {
            $this->_timetable[] = new SlTimeTableEntry($entry);
        }

        foreach ($json['ResponseData']['Trains'] as $key => $entry) {
            $this->_timetable[] = new SlTimeTableEntry($entry);
        }

        foreach ($json['ResponseData']['Trams'] as $key => $entry) {
            $this->_timetable[] = new SlTimeTableEntry($entry);
        }

        foreach ($json['ResponseData']['Ships'] as $key => $entry) {
            $this->_timetable[] = new SlTimeTableEntry($entry);
        }
    }


}