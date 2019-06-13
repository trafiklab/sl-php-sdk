<?php
/**
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at https://mozilla.org/MPL/2.0/.
 */

/** @noinspection PhpUnhandledExceptionInspection */

namespace Trafiklab\Sl\Model;

use PHPUnit_Framework_TestCase;
use Trafiklab\Common\Model\Contract\WebResponse;
use Trafiklab\Common\Model\Enum\TimeTableType;

class SlTimeTableResponseTest extends PHPUnit_Framework_TestCase
{
    function testConstructor_validDepartureBoardJson_shouldReturnCorrectObjectRepresentation()
    {
        $validDepartures = json_decode(file_get_contents("./tests/Resources/Sl/validDeparturesReply.json"), true);
        $dummyResponse = $this->createMock(WebResponse::class);
        $departureBoard = new SlTimeTableResponse($dummyResponse, $validDepartures);

        self::assertNotNull($departureBoard->getTimetable());
        self::assertEquals(TimeTableType::DEPARTURES, $departureBoard->getType());

        self::assertEquals(96, count($departureBoard->getTimetable()));
        self::assertEquals($dummyResponse, $departureBoard->getOriginalApiResponse());
    }

    function testConstructor_validArrivalBoardJson_shouldReturnCorrectObjectRepresentation()
    {
        $validDepartures = json_decode(
            file_get_contents("./tests/Resources/Sl/validDeparturesReplyWithBoats.json"), true);
        $dummyResponse = $this->createMock(WebResponse::class);
        $departureBoard = new SlTimeTableResponse($dummyResponse, $validDepartures);

        self::assertNotNull($departureBoard->getTimetable());
        self::assertEquals(TimeTableType::DEPARTURES, $departureBoard->getType());

        self::assertEquals(1, count($departureBoard->getTimetable()));
    }
}
