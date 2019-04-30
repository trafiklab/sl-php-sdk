<?php

namespace Trafiklab\Sl\Model;

use PHPUnit_Framework_TestCase;
use Trafiklab\Common\Model\Enum\TransportType;

class SlTimeTableEntryTest extends PHPUnit_Framework_TestCase
{
    function testConstructor_validDepartureBoardEntryJson_shouldReturnCorrectObjectRepresentation()
    {
        $validDepartures = json_decode(
            file_get_contents("./tests/Resources/Sl/validDeparturesReplyEntry.json"), true);
        $entry = new SlTimeTableEntry($validDepartures);

        self::assertEquals("Mörby centrum", $entry->getDirection());
        self::assertEquals("tunnelbanans röda linje", $entry->getLineName());
        self::assertEquals("17:29", $entry->getDisplayTime());
        self::assertEquals("14", $entry->getLineNumber());
        self::assertEquals("28958", $entry->getTripNumber());
        self::assertEquals(TransportType::METRO, $entry->getTransportType());
    }


}
