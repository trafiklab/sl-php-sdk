<?php


namespace Trafiklab\Sl\Model;


use DateTime;
use Trafiklab\Common\Model\Contract\Stop;

class SlStop implements Stop
{
    private $_stopId;
    private $_stopName;
    private $_departureTime;
    private $_arrivalTime;
    private $_latitude;
    private $_longitude;
    private $_platform;

    public function __construct(array $json)
    {
        $this->parseApiResponse($json);
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
     * @return DateTime|null The arrival time at this stop. Null if there is no data about the arrival time at this
     *                       stop location.
     */
    public function getScheduledArrivalTime(): ?DateTime
    {
        return $this->_arrivalTime;
    }

    /**
     * The latitude component of this stoplocation's coordinates.
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->_latitude;
    }

    /**
     * The longitude component of this stoplocation's coordinates.
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->_longitude;
    }

    /**
     * The platform at which the vehicle will stop.
     * @return null|string The platform at which the vehicle will stop. Null if no platform information is known.
     */
    public function getPlatform(): ?string
    {
        return $this->_platform;
    }

    private function parseApiResponse(array $json)
    {
        $this->_stopId = $json['extId'];
        $this->_stopName = $json['name'];

        $this->_latitude = $json['lat'];
        $this->_longitude = $json['lon'];

        if (key_exists('depDate', $json)) {
            $this->_departureTime =
                DateTime::createFromFormat("Y-m-d H:i:s",
                    $json['depDate'] . ' ' . $json['depTime']);
        }
        if (key_exists('arrDate', $json)) {
            $this->_arrivalTime =
                DateTime::createFromFormat("Y-m-d H:i:s",
                    $json['arrDate'] . ' ' . $json['arrTime']);
        }
        if (key_exists('track', $json)) {
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