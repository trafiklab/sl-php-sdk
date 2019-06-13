<?php
/**
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at https://mozilla.org/MPL/2.0/.
 */

namespace Trafiklab\Sl\Model;


use DateTime;
use Trafiklab\Common\Model\Contract\VehicleStopWithRealtime;

class SlVehicleStop implements VehicleStopWithRealtime
{
    private $_stopId;
    private $_stopName;
    private $_departureTime;
    private $_arrivalTime;
    private $_latitude;
    private $_longitude;
    private $_platform;
    private $_realtimeArrivalTime;
    private $_realtimeDepartureTime;

    /**
     * SlVehicleStop constructor.
     *
     * @param array       $json
     * @param null|string $platform
     *
     * @internal
     */
    public function __construct(array $json, ?string $platform = null)
    {
        $this->parseApiResponse($json, $platform);
    }

    /**
     * The Rikshållplats-ID for this stoplocation.
     *
     * @return string
     */
    public function getStopId(): string
    {
        return $this->_stopId;
    }

    /**
     * The name of this stoplocation.
     *
     * @return string The name of this stoplocation.
     */
    public function getStopName(): string
    {
        return $this->_stopName;
    }

    /**
     * @return DateTime|null   The departure time at this stop. Null if there is no data about the departure time at
     *                         this stop location.
     */
    public function getScheduledDepartureTime(): ?DateTime
    {
        return $this->_departureTime;
    }

    /**
     * The arrival time at this stop.
     *
     * @return DateTime|null The arrival time at this stop. Null if there is no data about the arrival time at this
     *                       stop location.
     */
    public function getScheduledArrivalTime(): ?DateTime
    {
        return $this->_arrivalTime;
    }

    /**
     * The latitude component of this stoplocation's coordinates.
     *
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->_latitude;
    }

    /**
     * The longitude component of this stoplocation's coordinates.
     *
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->_longitude;
    }

    /**
     * The platform at which the vehicle will stop.
     *
     * @return null|string The platform at which the vehicle will stop. Null if no platform information is known.
     */
    public function getPlatform(): ?string
    {
        return $this->_platform;
    }

    /**
     * @return DateTime|null   The estimated (real-time) departure time at this stop. Null if there is no data about
     *                         the departure time at this stop area.
     */
    public function getEstimatedDepartureTime(): ?DateTime
    {
        return $this->_realtimeDepartureTime != null ? $this->_realtimeDepartureTime : $this->_departureTime;
    }

    /**
     * The arrival time at this stop.
     *
     * @return DateTime|null The estimated (real-time) arrival time at this stop. Null if there is no data about the
     *                       arrival time at this stop area.
     */
    public function getEstimatedArrivalTime(): ?DateTime
    {
        return $this->_realtimeArrivalTime != null ? $this->_realtimeArrivalTime : $this->_arrivalTime;
    }

    /**
     * Whether or not this vehicle's stop is cancelled.
     *
     * @return bool
     */
    public function isCancelled(): bool
    {
        // TODO: Correctly parse and implement isCancelled() method.
        return false;
    }

    /**
     * @param array       $json     An array representing the API answer.
     * @param null|string $platform The platform this stop will take place at, in case this information is provided
     *                              outside the JSON API response
     */
    private function parseApiResponse(array $json, ?string $platform)
    {
        // Remove leading 30010
        $this->_stopId = substr($json['mainMastExtId'], 5);
        $this->_stopName = $json['name'];

        $this->_latitude = $json['lat'];
        $this->_longitude = $json['lon'];

        if (key_exists('depDate', $json)) {
            $this->_departureTime =
                DateTime::createFromFormat("Y-m-d H:i:s",
                    $json['depDate'] . ' ' . $json['depTime']);
        }

        if (key_exists('rtDepDate', $json)) {
            $this->_realtimeDepartureTime =
                DateTime::createFromFormat("Y-m-d H:i:s",
                    $json['rtDepDate'] . ' ' . $json['rtDepDate']);
        }

        if (key_exists('arrDate', $json)) {
            $this->_arrivalTime =
                DateTime::createFromFormat("Y-m-d H:i:s",
                    $json['arrDate'] . ' ' . $json['arrTime']);
        }

        if (key_exists('rtArrDate', $json)) {
            $this->_realtimeArrivalTime =
                DateTime::createFromFormat("Y-m-d H:i:s",
                    $json['rtArrDate'] . ' ' . $json['rtArrDate']);
        }


        if ($platform !== null) {
            $this->_platform = $platform;
        } else if (key_exists('track', $json)) {
            $this->_platform = $json['track'];
        }

        if ($this->_departureTime == null && $this->_arrivalTime == null && key_exists('date', $json)) {
            // This is a backup solution designed to handle origin/destination of legs in case of a walking link.
            $this->_departureTime =
                DateTime::createFromFormat("Y-m-d H:i:s",
                    $json['date'] . ' ' . $json['time']);
            $this->_arrivalTime = $this->_departureTime;
        }

    }
}