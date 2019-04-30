<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Trafiklab\Sl\Model;

use DateTime;
use PHPUnit_Framework_TestCase;

class LegTest extends PHPUnit_Framework_TestCase
{
    function testConstructor_validDepartureBoardJson_shouldReturnCorrectObjectRepresentation()
    {
        $jsonArray = json_decode(file_get_contents("./tests/Resources/Sl/validRoutePlanningLeg.json"), true);
        $leg = new SlLeg($jsonArray);

        self::assertEquals("Sergels torg", $leg->getOrigin()->getStopName());
        self::assertEquals(400111505, $leg->getOrigin()->getStopId());
        self::assertEquals(new DateTime("2019-05-03 00:21:00"), $leg->getOrigin()->getScheduledDepartureTime());
        self::assertNull($leg->getOrigin()->getScheduledArrivalTime());
        self::assertEquals("Södra station (på Swedenborgsgatan)", $leg->getDestination()->getStopName());
        self::assertEquals(0, count($leg->getNotes()));
        self::assertEquals("buss 57", $leg->getVehicle()->getName());
        self::assertEquals(8, count($leg->getIntermediaryStops()));
    }

}