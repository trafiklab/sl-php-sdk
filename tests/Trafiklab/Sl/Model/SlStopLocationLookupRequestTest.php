<?php

namespace Trafiklab\Sl\Model;

use PHPUnit_Framework_TestCase;

class SlStopLocationLookupRequestTest extends PHPUnit_Framework_TestCase
{
    function testGettersSetters_createNewRequest_shouldReturnCorrectDefaultsOrSetValues()
    {
        $request = new SlStopLocationLookupRequest();
        $request->setSearchQuery("Stockholm");

        // Test defaults
        self::assertEquals("sv", $request->getLanguage());
        self::assertEquals(10, $request->getMaxNumberOfResults());

        $request->setLanguage("en");
        $request->setMaxNumberOfResults(20);

        self::assertEquals("Stockholm", $request->getSearchQuery());
        self::assertEquals("en", $request->getLanguage());
        self::assertEquals(20, $request->getMaxNumberOfResults());

        $request->setSearchQuery("aReallyReallyReallyLongSearchQuery");
        self::assertEquals("aReallyReallyReallyL", $request->getSearchQuery());

    }
}