<?php
/**
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at https://mozilla.org/MPL/2.0/.
 */

namespace Trafiklab\Sl\Model;

use Trafiklab\Common\Internal\WebResponseImpl;
use Trafiklab\Common\Model\Contract\TimeTableEntry;
use Trafiklab\Common\Model\Contract\TimeTableEntryWithRealTime;
use Trafiklab\Common\Model\Contract\TimeTableResponse;
use Trafiklab\Common\Model\Contract\TimeTableResponseWithRealTime;
use Trafiklab\Common\Model\Contract\WebResponse;
use Trafiklab\Common\Model\Enum\TimeTableType;

class SlTimeTableResponse implements TimeTableResponse
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
     */
    public function __construct(WebResponse $webResponse, array $json)
    {
        $this->_oringinalResponse = $webResponse;
        $this->parseApiResponse($json);
    }

    /**
     * @return TimeTableEntry[] The requested timetable as an array of timetable entries.
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
     */
    private function parseApiResponse(array $json): void
    {
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