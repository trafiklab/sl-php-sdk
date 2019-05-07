<?php


namespace Trafiklab\Sl\Model;

use Exception;
use Trafiklab\Common\Internal\WebResponseImpl;
use Trafiklab\Common\Model\Contract\TimeTableEntryWithRealTime;
use Trafiklab\Common\Model\Contract\TimeTableResponseWithRealTime;
use Trafiklab\Common\Model\Contract\WebResponse;
use Trafiklab\Common\Model\Enum\TimeTableType;

class SlTimeTableResponse implements TimeTableResponseWithRealTime
{

    private $_timetable = [];
    private $_oringinalResponse;

    /**
     * Create a SlTimeTableResponse from Sls JSON response.
     *
     * @param WebResponse $webResponse The WebResponse created by the request.
     * @param array       $json        The API output to parse.
     *
     * @internal
     * @throws Exception
     */
    public function __construct(WebResponse $webResponse, array $json)
    {
        $this->_oringinalResponse = $webResponse;
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
     * Get the original response from the API.
     *
     * @return WebResponseImpl
     */
    public function getOriginalApiResponse(): WebResponse
    {
        return $this->_oringinalResponse;
    }

    /**
     * @param array $json The API output to parse.
     *
     * @throws Exception
     */
    private function parseApiResponse(array $json): void
    {
        if ($json['StatusCode'] > 0) {
            throw new Exception('SL Departures threw an error: ' . $json['Message'], 500);
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