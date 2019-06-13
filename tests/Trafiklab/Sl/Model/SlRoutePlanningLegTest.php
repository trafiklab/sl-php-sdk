<?php
/**
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at https://mozilla.org/MPL/2.0/.
 */

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

        self::assertEquals("Sergels torg", $leg->getDeparture()->getStopName());
        self::assertEquals(1000, $leg->getDeparture()->getStopId());
        self::assertEquals(780, $leg->getDuration());
        self::assertEquals("O", $leg->getDeparture()->getPlatform());
        self::assertEquals(null, $leg->getArrival()->getPlatform());
        self::assertEquals(new DateTime("2019-05-03 00:21:00"), $leg->getDeparture()->getScheduledDepartureTime());
        self::assertNull($leg->getDeparture()->getScheduledArrivalTime());
        self::assertEquals("Södra station (på Swedenborgsgatan)", $leg->getArrival()->getStopName());
        self::assertEquals(0, count($leg->getNotes()));
        self::assertEquals("buss 57", $leg->getVehicle()->getName());
        self::assertEquals("Sofia", $leg->getDirection());
        self::assertEquals(8, count($leg->getIntermediaryStops()));
    }

}