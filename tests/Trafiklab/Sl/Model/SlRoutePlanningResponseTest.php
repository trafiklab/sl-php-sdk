<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Trafiklab\Sl\Model;

use PHPUnit_Framework_TestCase;
use Trafiklab\Common\Model\Contract\WebResponse;

class SlRoutePlanningResponseTest extends PHPUnit_Framework_TestCase
{
    function testConstructor_validDepartureBoardJson_shouldReturnCorrectObjectRepresentation()
    {
        $jsonArray = json_decode(file_get_contents("./tests/Resources/Sl/validRoutePlanningReply.json"), true);
        $webResponse = self::createMock(WebResponse::class);
        $response = new SlRoutePlanningResponse($webResponse, $jsonArray);

        self::assertEquals($webResponse, $response->getOriginalApiResponse());
        self::assertEquals(6, count($response->getTrips()));
    }

}