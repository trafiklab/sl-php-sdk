<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Trafiklab\Sl\Model;

use PHPUnit_Framework_TestCase;
use Trafiklab\Common\Model\Enum\TimeTableType;

class SlTimeTableResponseTest extends PHPUnit_Framework_TestCase
{
    function testConstructor_validDepartureBoardJson_shouldReturnCorrectObjectRepresentation()
    {
        $validDepartures = json_decode(file_get_contents("./tests/Resources/Sl/validDeparturesReply.json"), true);
        $departureBoard = new SlTimeTableResponse($validDepartures);

        self::assertNotNull($departureBoard->getTimetable());
        self::assertEquals(TimeTableType::DEPARTURES, $departureBoard->getType());

        self::assertEquals(96, count($departureBoard->getTimetable()));
    }

    function testConstructor_validArrivalBoardJson_shouldReturnCorrectObjectRepresentation()
    {
        $validDepartures = json_decode(
            file_get_contents("./tests/Resources/Sl/validDeparturesReplyWithBoats.json"), true);
        $departureBoard = new SlTimeTableResponse($validDepartures);

        self::assertNotNull($departureBoard->getTimetable());
        self::assertEquals(TimeTableType::DEPARTURES, $departureBoard->getType());

        self::assertEquals(1, count($departureBoard->getTimetable()));
    }
}
