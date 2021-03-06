<?php
/**
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at https://mozilla.org/MPL/2.0/.
 */

/** @noinspection PhpUnhandledExceptionInspection */

namespace Trafiklab\Sl\Model;

use PHPUnit_Framework_TestCase;
use Trafiklab\Common\Model\Enum\TransportType;

class SlRoutePlanningVehicleTest extends PHPUnit_Framework_TestCase
{
    function testConstructor_validDepartureBoardJson_shouldReturnCorrectObjectRepresentation()
    {
        $jsonArray = json_decode(file_get_contents("./tests/Resources/Sl/validVehicle.json"), true);
        $vehicle = new SlVehicle($jsonArray);

        self::assertEquals("buss 57", $vehicle->getName());
        self::assertEquals(TransportType::BUS, $vehicle->getType());
        self::assertEquals("36535", $vehicle->getNumber());
        self::assertEquals("SL", $vehicle->getOperatorName());
        self::assertEquals(275, $vehicle->getOperatorCode());
        self::assertEquals("https://sl.se", $vehicle->getOperatorUrl());
        self::assertEquals("57", $vehicle->getLineNumber());
    }
}