<?php


namespace Trafiklab\Sl\Model;


use Exception;
use Trafiklab\Common\Internal\WebResponseImpl;
use Trafiklab\Common\Model\Contract\RoutePlanningResponse;
use Trafiklab\Common\Model\Contract\WebResponse;

class SlRoutePlanningResponse implements RoutePlanningResponse
{
    private $_trips;

    /**
     * Build a RoutePlanningResponse from an API response.
     *
     * @param WebResponse $webResponse The WebResponse created by the request.
     * @param array       $json        The API output to parse.
     *
     * @internal
     * @throws Exception
     */
    public function __construct(WebResponse $webResponse, array $json)
    {
        $this->_oringinalResponse = $webResponse;
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
     * Get the original response from the API.
     *
     * @return WebResponseImpl
     */
    public function getOriginalApiResponse(): WebResponse
    {
        return $this->_oringinalResponse;
    }

    /**
     * @param array $json The API output to parse.
     *
     * @throws Exception
     */
    private function parseApiResponse(array $json): void
    {
        if (key_exists('errorCode', $json)) {
            throw new Exception('ResRobot threw an error: ' . $json['errorText'], 500);
        }

        foreach ($json['Trip'] as $key => $entry) {
            $this->_trips[] = new SlTrip($entry);
        }
    }
}