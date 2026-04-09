<?php

namespace App\Enums;

enum ProductTypeEnum: string
{
    case VEG = 'veg';
    case NON_VEG = 'non_veg';
    case EGG = 'egg';
    case BEVERAGE = 'beverage';
}

