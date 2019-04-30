<?php


namespace Trafiklab\Sl\Model;


use Trafiklab\Common\Model\Contract\RoutePlanningResponse;

class SlRoutePlanningResponse implements RoutePlanningResponse
{
    private $_trips;

    /**
     *
     * @param array $json
     *
     * @throws \Exception
     */
    public function __construct(array $json)
    {
        $this->parseApiResponse($json);
    }

    /**
     * @return SlTrip[]
     */
    public function getTrips(): array
    {
        return $this->_trips;
    }

    /**
     * @param array $json The API output to parse.
     *
     * @throws \Exception
     */
    private function parseApiResponse(array $json): void
    {
        if (key_exists('errorCode', $json)) {
            throw new \Exception('ResRobot threw an error: ' . $json['errorText'], 500);
        }

        foreach ($json['Trip'] as $key => $entry) {
            $this->_trips[] = new SlTrip($entry);
        }
    }

}