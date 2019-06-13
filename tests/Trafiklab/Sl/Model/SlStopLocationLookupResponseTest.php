<?php
/**
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at https://mozilla.org/MPL/2.0/.
 */

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