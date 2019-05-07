<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Trafiklab\Sl\Model;

use DateTime;
use PHPUnit_Framework_TestCase;

class SlStopTest extends PHPUnit_Framework_TestCase
{
    function testConstructor_validDepartureBoardJson_shouldReturnCorrectObjectRepresentation()
    {
        $jsonArray = json_decode(file_get_contents("./tests/Resources/Sl/validRoutePlanningDepartureStop.json"), true);
        $stop = new SlStop($jsonArray);

        self::assertEquals("Sergels torg", $stop->getStopName());
        self::assertEquals(1000, $stop->getStopId());
        self::assertEquals(new DateTime("2019-05-03 00:21:00"), $stop->getScheduledDepartureTime());
        self::assertEquals(null, $stop->getScheduledArrivalTime());
        self::assertEquals(new DateTime("2019-05-03 00:21:00"), $stop->getEstimatedDepartureTime());
        self::assertEquals(null, $stop->getEstimatedArrivalTime());
        self::assertEquals(null, $stop->getPlatform());
        self::assertEquals(59.333929, $stop->getLatitude());
        self::assertEquals(18.064623, $stop->getLongitude());
    }

}