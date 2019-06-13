<?php
/**
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at https://mozilla.org/MPL/2.0/.
 */

namespace Trafiklab\Sl\Model;


use Trafiklab\Common\Model\Contract\StopLocationLookupResponse;
use Trafiklab\Common\Model\Contract\WebResponse;

class SlStopLocationLookupResponse implements StopLocationLookupResponse
{
    private $_foundStopLocations;
    private $_originalResponse;

    /**
     * SlStopLocationLookupResponse constructor.
     *
     * @param WebResponse $response
     * @param mixed       $json
     */
    public function __construct($response, $json)
    {
        $this->_originalResponse = $response;
        $this->parseApiResponse($json);
    }

    /**
     * Get the original response from the API.
     *
     * @return WebResponse
     */
    public function getOriginalApiResponse(): WebResponse
    {
        return $this->_originalResponse;
    }

    /**
     * An array containing the stop areas which were found.
     *
     * @return SlStopLocationLookupEntry[]
     */
    public function getFoundStopLocations(): array
    {
        return $this->_foundStopLocations;
    }

    /**
     * @param array $json The API output to parse.
     *
     */
    private function parseApiResponse(array $json): void
    {
        $this->_foundStopLocations = [];
        foreach ($json['ResponseData'] as $stopLocation) {
            $this->_foundStopLocations[] = new SlStopLocationLookupEntry($stopLocation);
        }
    }

}