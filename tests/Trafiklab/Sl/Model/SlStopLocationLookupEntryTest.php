<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Trafiklab\Sl\Model;

use PHPUnit_Framework_TestCase;
use Trafiklab\Common\Model\Enum\TransportType;

class SlStopLocationLookupEntryTest extends PHPUnit_Framework_TestCase
{
    function testConstructor_validDepartureBoardJson_shouldReturnCorrectObjectRepresentation()
    {
        $jsonArray = json_decode(file_get_contents("./tests/Resources/Sl/validStopLocationLookupEntry.json"), true);
        $entry = new SlStopLocationLookupEntry($jsonArray);

        self::assertEquals("9001", $entry->getId());
        self::assertEquals("T-Centralen (Stockholm)", $entry->getName());
        self::assertEquals(18.060434, $entry->getLongitude());
        self::assertEquals(59.331376, $entry->getLatitude());
        self::assertEquals(0, $entry->getWeight());
        self::assertEquals(true, $entry->isStopLocationForTransportType(TransportType::TRAIN));
    }

}