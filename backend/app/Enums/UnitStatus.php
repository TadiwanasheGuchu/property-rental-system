<?php

namespace App\Enums;

enum UnitStatus: string
{
    case Available   = 'available';
    case Reserved    = 'reserved';
    case Maintenance = 'maintenance';
    case Unavailable = 'unavailable';
}
