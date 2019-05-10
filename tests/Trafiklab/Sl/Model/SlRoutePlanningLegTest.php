<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Trafiklab\Sl\Model;

use DateTime;
use PHPUnit_Framework_TestCase;

class SlRoutePlanningLegTest extends PHPUnit_Framework_TestCase
{
    function testConstructor_validDepartureBoardJson_shouldReturnCorrectObjectRepresentation()
    {
        $jsonArray = json_decode(file_get_contents("./tests/Resources/Sl/validRoutePlanningLeg.json"), true);
        $leg = new SlLeg($jsonArray);

        self::assertEquals("Sergels torg", $leg->getOrigin()->getStopName());
        self::assertEquals(1000, $leg->getOrigin()->getStopId());
        self::assertEquals("O", $leg->getOrigin()->getPlatform());
        self::assertEquals(null, $leg->getDestination()->getPlatform());
        self::assertEquals(new DateTime("2019-05-03 00:21:00"), $leg->getOrigin()->getScheduledDepartureTime());
        self::assertNull($leg->getOrigin()->getScheduledArrivalTime());
        self::assertEquals("Södra station (på Swedenborgsgatan)", $leg->getDestination()->getStopName());
        self::assertEquals(0, count($leg->getNotes()));
        self::assertEquals("buss 57", $leg->getVehicle()->getName());
        self::assertEquals("Sofia", $leg->getDirection());
        self::assertEquals(8, count($leg->getIntermediaryStops()));
    }

}