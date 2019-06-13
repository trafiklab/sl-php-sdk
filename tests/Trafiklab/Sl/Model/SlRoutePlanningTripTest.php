<?php
/**
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at https://mozilla.org/MPL/2.0/.
 */

/** @noinspection PhpUnhandledExceptionInspection */

namespace Trafiklab\Sl\Model;

use PHPUnit_Framework_TestCase;

class SlRoutePlanningTripTest extends PHPUnit_Framework_TestCase
{
    function testConstructor_validDepartureBoardJson_shouldReturnCorrectObjectRepresentation()
    {
        $jsonArray = json_decode(file_get_contents("./tests/Resources/Sl/validRoutePlanningTrip.json"), true);
        $trip = new SlTrip($jsonArray);
        self::assertNotNull($trip->getLegs());
        self::assertEquals(8, count($trip->getLegs()));
        self::assertEquals(23580, $trip->getDuration());
        self::assertEquals(1000, $trip->getDeparture()->getStopId());
        self::assertEquals(5000, $trip->getArrival()->getStopId());
    }

}