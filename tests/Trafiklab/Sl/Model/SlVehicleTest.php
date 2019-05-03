<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Trafiklab\Sl\Model;

use PHPUnit_Framework_TestCase;
use Trafiklab\Common\Model\Enum\TransportType;

class SlVehicleTest extends PHPUnit_Framework_TestCase
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
    }
}