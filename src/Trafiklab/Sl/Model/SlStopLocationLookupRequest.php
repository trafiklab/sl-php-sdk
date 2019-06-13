<?php
/**
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at https://mozilla.org/MPL/2.0/.
 */

namespace Trafiklab\Sl\Model;

use Trafiklab\Common\Model\Contract\StopLocationLookupRequest;

class SlStopLocationLookupRequest implements StopLocationLookupRequest
{
    private $_language = "sv";
    private $_maxNumberOfResults = 10;
    private $_searchQuery;

    /**
     * Get the station name to search after.
     *
     * @return string The station name to search after.
     */
    function getSearchQuery(): string
    {
        return $this->_searchQuery;
    }

    /**
     * Set the station name to search after. The maximum length might be limited based on the implementation.
     * If the input is longer than 20 characters, only the first 20 characters will be used.
     *
     * @param string $searchQuery (A part of) the station name to search after.
     */
    function setSearchQuery(string $searchQuery): void
    {
        $this->_searchQuery = $searchQuery;

        // Enforce the maximum query length
        if (strlen($this->_searchQuery) > 20) {
            $this->_searchQuery = substr($this->_searchQuery, 0, 20);
        }
    }

    /**
     * Get the language which is used in the response.
     *
     * @return string The language which is used in the response.
     */
    function getLanguage(): string
    {
        return $this->_language;
    }

    /**
     * Set the language which is used in the response.
     *
     * @param string $language The language which is used in the response.
     */
    function setLanguage(string $language): void
    {
        $this->_language = $language;
    }

    /**
     * Get the maximum number of results. The response might contain fewer results, but never more.
     *
     * @return int The maximum number of results.
     */
    function getMaxNumberOfResults(): int
    {
        return $this->_maxNumberOfResults;
    }

    /**
     * Set the maximum number of results. The response might contain fewer results, but never more.
     *
     * @param int $maximumNumberOfResults The maximum number of results.
     */
    function setMaxNumberOfResults(int $maximumNumberOfResults): void
    {
        $this->_maxNumberOfResults = $maximumNumberOfResults;
    }
}