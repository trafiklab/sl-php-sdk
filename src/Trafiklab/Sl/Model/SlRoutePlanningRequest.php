<?php


namespace Trafiklab\Sl\Model;


use Trafiklab\Common\Model\Contract\RoutePlanningRequest;
use Trafiklab\Common\Model\Enum\RoutePlanningSearchType;

class SlRoutePlanningRequest extends SlBaseRequest implements RoutePlanningRequest
{

    private $_originId;
    private $_destinationId;
    private $_viaId;
    private $_lang = "sv";
    private $_routePlanningSearchType = RoutePlanningSearchType::DEPART_AT_SPECIFIED_TIME;

    /**
     * @return string
     */
    public function getOriginStopId(): string
    {
        return $this->_originId;
    }

    /**
     * @param string $originId
     */
    public function setOriginStopId(string $originId): void
    {
        $this->_originId = $originId;
    }

    /**
     * @return string
     */
    public function getDestinationStopId(): string
    {
        return $this->_destinationId;
    }

    /**
     * @param string $destinationId
     */
    public function setDestinationStopId(string $destinationId): void
    {
        $this->_destinationId = $destinationId;
    }

    /**
     * @return string
     */
    public function getViaStopId(): ?string
    {
        return $this->_viaId;
    }

    /**
     * @param string $viaId
     */
    public function setViaStopId(?string $viaId): void
    {
        $this->_viaId = $viaId;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->_lang;
    }

    /**
     * @param string $lang
     */
    public function setLanguage(string $lang): void
    {
        $this->_lang = $lang;
    }


    /**
     * Get the type of time definition in this query. Either Arriving at a certain time, or departing at a certain time.
     *
     * @return int One of the constants defined in RoutePlanningSearchType
     *
     * @see RoutePlanningSearchType
     */
    public function getRoutePlanningSearchType(): int
    {
        return $this->_routePlanningSearchType;
    }

    /**
     * Set the type of time definition in this query. Either Arriving at a certain time, or departing at a certain time.
     *
     * @param int $routePlanningSearchType One of the constants defined in RoutePlanningSearchType
     *
     * @see RoutePlanningSearchType
     */
    public function setRoutePlanningSearchType(int $routePlanningSearchType): void
    {
        $this->_routePlanningSearchType = $routePlanningSearchType;
    }
}