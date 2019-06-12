<?php

namespace Trafiklab\Sl\Model;

use DateTime;
use PHPUnit_Framework_TestCase;
use Trafiklab\Common\Model\Enum\RoutePlanningSearchType;

class SlRoutePlanningRequestTest extends PHPUnit_Framework_TestCase
{
    function testGettersSetters_createNewRequest_shouldReturnCorrectDefaultsOrSetValues()
    {
        $request = new SlRoutePlanningRequest();
        $request->setOriginStopId("123");
        $request->setDestinationStopId("456");

        self::assertEquals("123", $request->getOriginStopId());
        self::assertEquals("456", $request->getDestinationStopId());

        // Test defaults
        self::assertEquals("sv", $request->getLanguage());
        self::assertEquals(null, $request->getViaStopId());
        self::assertEquals(RoutePlanningSearchType::DEPART_AT_SPECIFIED_TIME, $request->getRoutePlanningSearchType());

        $request->setLanguage("en");
        $request->setViaStopId("1234");
        $request->setRoutePlanningSearchType(RoutePlanningSearchType::ARRIVE_AT_SPECIFIED_TIME);

        self::assertEquals("1234", $request->getViaStopId());
        self::assertEquals("en", $request->getLanguage());
        self::assertEquals(RoutePlanningSearchType::ARRIVE_AT_SPECIFIED_TIME, $request->getRoutePlanningSearchType());

        $now = new DateTime();
        self::assertEquals($now->getTimestamp(), $request->getDateTime()->getTimestamp());

        $now->setTime(01,23,45);
        $request->setDateTime($now);
        self::assertEquals($now->getTimestamp(), $request->getDateTime()->getTimestamp());

    }
}