<?php
/**
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at https://mozilla.org/MPL/2.0/.
 */

namespace Trafiklab\Sl\Model;

use Trafiklab\Common\Model\Contract\RoutePlanningLeg;
use Trafiklab\Common\Model\Contract\Stop;
use Trafiklab\Common\Model\Contract\StopWithRealtime;
use Trafiklab\Common\Model\Contract\Vehicle;
use Trafiklab\Common\Model\Contract\VehicleStop;
use Trafiklab\Common\Model\Contract\VehicleStopWithRealtime;

/**
 * A leg is one part of a journey, made with a single vehicle or on foot. A journey can consist of one or more legs. In
 * the case of multiple legs, a transfer is required between two legs.
 *
 * @package Trafiklab\Sl\Model
 */
class SlLeg implements RoutePlanningLeg
{

    private $_destination;
    private $_direction;
    private $_intermediaryStops;
    private $_notes;
    private $_origin;
    private $_type;
    private $_vehicle;

    /**
     * SlLeg constructor.
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
     * The origin of this leg.
     *
     * @return VehicleStopWithRealtime The stoplocation at which this leg starts.
     */
    public function getDeparture(): VehicleStop
    {
        return $this->_origin;
    }

    /**
     * The destination of this leg.
     *
     * @return VehicleStopWithRealtime The stoplocation at which this leg ends.
     */
    public function getArrival(): VehicleStop
    {
        return $this->_destination;
    }

    /**
     * Get the duration of this leg in seconds.
     *
     * @return int
     */
    public function getDuration(): int
    {
        if ($this->getArrival()->getScheduledArrivalTime() == null
            || $this->getDeparture()->getScheduledDepartureTime() == null) {
            return 0;
        }
        return $this->getArrival()->getScheduledArrivalTime()->getTimestamp() -
            $this->getDeparture()->getScheduledDepartureTime()->getTimestamp();
    }

    /**
     * Remarks about this leg, for example describing facilities on board of a train, or possible disturbances on the
     * route.
     *
     * @return string[]
     */
    public function getNotes(): array
    {
        return $this->_notes;
    }

    /**
     * The vehicle which is used to travel from the origin to the destination of this leg, if any. Can be null in case
     * of a walk between two stop locations.
     *
     * @return Vehicle|null The vehicle used on this leg, or null in case of a walking transfer.
     */
    public function getVehicle(): ?Vehicle
    {
        return $this->_vehicle;
    }

    /**
     * Intermediary stops made by the vehicle on this leg.
     *
     * @return SlVehicleStop[] Stops between the origin and destination, excluding the origin and destination.
     */
    public function getIntermediaryStops(): array
    {
        return $this->_intermediaryStops;
    }

    /**
     * The direction of the vehicle on this leg. Can be null in case
     * of a walk between two stop locations.
     *
     * @return string|null The direction of the vehicle used on this leg, or null in case of a walking transfer.
     */
    public function getDirection(): ?string
    {
        return $this->_direction;
    }

    /**
     * JNY: journey, WALK: walking.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->_type;
    }

    private function parseApiResponse(array $json)
    {
        $this->_notes = [];
        if (key_exists('Notes', $json)) {
            foreach ($json['Notes']['Note'] as $note) {
                $this->_notes[] = $note['value'];
            }
        }

        $this->_type = $json['type'];

        if ($this->_type == "JNY") {
            $this->_vehicle = new SlVehicle($json['Product']);

            $originTrack = key_exists('track', $json['Origin']) ? $json['Origin']['track'] : null;
            $this->_origin = new SlVehicleStop($json['Stops']['Stop'][0], $originTrack);
            $destinationTrack = key_exists('track', $json['Destination']) ? $json['Destination']['track'] : null;
            $this->_destination = new SlVehicleStop(end($json['Stops']['Stop']), $destinationTrack);
            $this->_intermediaryStops = [];
            foreach ($json['Stops']['Stop'] as $stop) {
                $this->_intermediaryStops[] = new SlVehicleStop($stop);
            }

            // The stops array also includes the departure and arrival. Pop them of and use them instead of the
            // origin and destination data delivered by the API. This approach uses only one data type instead
            // of 2, making it easier to handle for end users.
            array_shift($this->_intermediaryStops);
            array_pop($this->_intermediaryStops);

            $this->_direction = $json['direction'];
        } else {
            // Should Dist (distance) and Duration (time) be parsed in case of a walk?
            $this->_origin = new SlVehicleStop($json['Origin']);
            $this->_destination = new SlVehicleStop($json['Destination']);
        }

    }
}