<?php
/**
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at https://mozilla.org/MPL/2.0/.
 */

namespace Trafiklab\Sl\Model;


use Trafiklab\Common\Model\Contract\StopLocationLookupEntry;

class SlStopLocationLookupEntry implements StopLocationLookupEntry
{
    private $_longitude;
    private $_latitude;
    private $_name;
    private $_id;


    /**
     * SlStopLocationLookupEntry constructor.
     *
     * @param array $json
     *
     * @internal
     */
    public function __construct(array $json)
    {
        $this->parseApiResponse($json);
    }

    /**
     * Get the id of this stop area.
     *
     * @return string The id of this stop area.
     */
    public function getId(): string
    {
        return $this->_id;
    }

    /**
     * Get the name of this stop area.
     *
     * @return string The name of this stop area.
     */
    public function getName(): string
    {
        return $this->_name;
    }

    /**
     * The longitude of this stop area.
     *
     * @return float The longitude of this stop area.
     */
    public function getLongitude(): float
    {
        return $this->_longitude;
    }

    /**
     * The latitude of this stop area.
     *
     * @return float The latitude of this stop area.
     */
    public function getLatitude(): float
    {
        return $this->_latitude;
    }

    /**
     * The sorting weight for this station. This can be determined by the number of vehicles stopping there, the
     * number of passengers, ...
     *
     * @return int The sorting weight for this station.
     */
    public function getWeight(): int
    {
        return 0;
    }

    /**
     * Check if a certain mode of transport stops at this stop location.
     *
     * @param string $transportType The type of transport, one of the constants in TransportType
     *
     * @return bool Whether or not the specified type of traffic can stop in this point. In case an API doesn't provide
     *              this information, it will always return true.
     *
     * @see TransportType
     */
    public function isStopLocationForTransportType(string $transportType): bool
    {
        return true;
    }

    private function parseApiResponse(array $json)
    {
        $this->_id = $json['SiteId'];
        $this->_name = $json['Name'];
        $this->_latitude = $json['Y'] / 1000000;
        $this->_longitude = $json['X'] / 1000000;

    }
}