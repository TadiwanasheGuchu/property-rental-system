<?php

namespace App\Enums;

enum PropertyType: string
{
    case ApartmentComplex = 'apartment_complex';
    case House            = 'house';
    case Townhouse        = 'townhouse';
    case Commercial       = 'commercial';
    case Office           = 'office';
    case Retail           = 'retail';
    case MixedUse         = 'mixed_use';
}
