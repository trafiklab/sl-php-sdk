<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Trafiklab\Sl\Model;

use DateTime;
use PHPUnit_Framework_TestCase;

class TripTest extends PHPUnit_Framework_TestCase
{
    function testConstructor_validDepartureBoardJson_shouldReturnCorrectObjectRepresentation()
    {
        $jsonArray = json_decode(file_get_contents("./tests/Resources/Sl/validRoutePlanningTrip.json"), true);
        $trip = new SlTrip($jsonArray);
        self::assertNotNull($trip->getLegs());
        self::assertEquals(8, count($trip->getLegs()));
    }

}