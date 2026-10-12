<?php

namespace App\Enums;

enum UnitType: string
{
    case Apartment = 'apartment';
    case Room      = 'room';
    case House     = 'house';
    case Shop      = 'shop';
    case Office    = 'office';
    case Studio    = 'studio';
    case Other     = 'other';
}
