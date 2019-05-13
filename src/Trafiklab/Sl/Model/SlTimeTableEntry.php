<?php

namespace Trafiklab\Sl\Model;

use DateTime;
use Trafiklab\Common\Model\Contract\TimeTableEntryWithRealTime;
use Trafiklab\Common\Model\Enum\TimeTableType;
use Trafiklab\Common\Model\Enum\TransportType;


/**
 * An entry in a timetable, describing a single departure or arrival of a vehicle at a stoplocation.
 *
 * @package Trafiklab\Sl\Model
 */
class SlTimeTableEntry implements TimeTableEntryWithRealTime
{
    private $_stopId;
    private $_stopName;
    private $_lineName;
    private $_direction;
    private $_lineNumber;
    private $_scheduledStopTime;
    private $_timeTableType = TimeTableType::DEPARTURES;
    private $_operator;
    private $_estimatedStopTime;
    private $_displayTime;
    private $_tripNumber;
    private $_transportType;
    private $_isCancelled;

    /**
     * SlTimeTableEntry constructor.
     *
     * @param array $json
     * @internal
     */
    public function __construct(array $json)
    {
        $this->parseApiResponse($json);
    }

    /**
     * The operator of the vehicle.
     *
     * @return string
     */
    public function getOperator(): string
    {
        return $this->_operator;
    }

    /**
     * The Rikshållplats-ID for the stop location.
     *
     * @return string
     */
    public function getStopId(): string
    {
        return $this->_stopId;
    }

    /**
     * The name of the stop at which the vehicle stops.
     *
     * @return string
     */
    public function getStopName(): string
    {
        return $this->_stopName;
    }

    /**
     * The time at which the vehicle stops at the stop location, including possible delays.
     *
     * @return DateTime
     */
    public function getScheduledStopTime(): DateTime
    {
        return $this->_scheduledStopTime;
    }

    /**
     * The type of timetable in which this entry resides, either arrivals or departures.
     *
     * @return int
     */
    public function getTimeTableType(): int
    {
        return $this->_timeTableType;
    }

    /**
     * The direction of the vehicle stopping at this time at this stop location. In case of a vehicle departing, this
     * is the destination of the vehicle. In case of a vehicle arriving, this is the origin of the vehicle.
     *
     * @return string
     */
    public function getDirection(): string
    {
        return $this->_direction;
    }

    /**
     * The name of the line on which the vehicle is driving.
     *
     * @return string
     */
    public function getLineName(): string
    {
        return $this->_lineName;
    }

    /**
     * The number of the line on which the vehicle is driving.
     *
     * @return int
     */
    public function getLineNumber(): string
    {
        return $this->_lineNumber;
    }

    /**
     * @return string
     */
    public function getTripNumber(): string
    {
        return $this->_tripNumber;
    }

    /**
     * @return string
     */
    public function getDisplayTime(): string
    {
        return $this->_displayTime;
    }

    /**
     * @return DateTime
     */
    public function getEstimatedStopTime(): DateTime
    {
        return $this->_estimatedStopTime;
    }

    /**
     * The type of transport.
     *
     * @return string TransportType indicating the type of transport.
     */
    public function getTransportType(): string
    {
        return $this->_transportType;
    }

    /**
     * Whether or not this vehicle's trip is cancelled.
     *
     * @return bool
     */
    public function isCancelled(): bool
    {
        return $this->_isCancelled;
    }

    private function parseApiResponse(array $json): void
    {
        $this->_stopId = $json['StopAreaNumber'];
        $this->_stopName = $json['StopAreaName'];
        $this->_lineName = $json['GroupOfLine'];
        if ($this->_lineName == null) {
            // default
            $this->_lineName = $json['LineNumber'] . ' ' . $json['Destination'];
        }
        $this->_lineNumber = $json['LineNumber'];

        $this->_scheduledStopTime = DateTime::createFromFormat("Y-m-d\TH:i:s", $json['TimeTabledDateTime']);
        $this->_estimatedStopTime = DateTime::createFromFormat("Y-m-d\TH:i:s", $json['ExpectedDateTime']);
        $this->_displayTime = $json['DisplayTime'];
        $this->_tripNumber = $json['JourneyNumber'];
        $this->_operator = "SL";
        $this->_direction = $json['Destination'];

        switch ($json['TransportMode']) {
            case "TRAIN":
                $this->_transportType = TransportType::TRAIN;
                break;
            case "TRAM":
                $this->_transportType = TransportType::TRAM;
                break;
            case "BUS":
                $this->_transportType = TransportType::BUS;
                break;
            case "METRO":
                $this->_transportType = TransportType::METRO;
                break;
            case "SHIP":
                $this->_transportType = TransportType::SHIP;
                break;
        }

        $this->_isCancelled = false;
        if (key_exists('Deviations', $json) && !empty($json['Deviations'])) {
            foreach ($json['Deviations'] as $deviation) {
                if ($deviation['Consequence'] == "CANCELLED") {
                    $this->_isCancelled = true;
                }
            }
        }
    }
}
