<?php
/**
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at https://mozilla.org/MPL/2.0/.
 */

namespace Trafiklab\Sl\Model\Enum;


abstract class SlTransportType
{
    public const TRAIN_LOCAL = 1;
    public const SUBWAY = 2;
    public const TRAM_LIGHT_RAIL = 4;
    public const BUS_LOCAL = 8;
    public const FERRIES_BOATS = 64;
    public const LOCAL_TRAFFIC = 128;
}
