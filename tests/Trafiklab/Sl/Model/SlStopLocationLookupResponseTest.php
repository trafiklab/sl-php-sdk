<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Trafiklab\Sl\Model;

use PHPUnit_Framework_TestCase;
use Trafiklab\Common\Model\Contract\WebResponse;

class SlStopLocationLookupResponseTest extends PHPUnit_Framework_TestCase
{
    function testConstructor_validDepartureBoardJson_shouldReturnCorrectObjectRepresentation()
    {
        $jsonArray = json_decode(file_get_contents("./tests/Resources/Sl/validStopLocationLookupReply.json"), true);
        $webResponse = self::createMock(WebResponse::class);
        $response = new SlStopLocationLookupResponse($webResponse, $jsonArray);

        self::assertEquals($webResponse, $response->getOriginalApiResponse());
        self::assertEquals(10, count($response->getFoundStopLocations()));
        self::assertEquals("9001", $response->getFoundStopLocations()[0]->getId());
    }

}