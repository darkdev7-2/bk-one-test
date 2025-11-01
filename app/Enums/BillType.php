<?php

namespace App\Enums;

enum BillType: string
{
    case Airtime = 'airtime';
    case Electricity = 'electricity';
    case Internet = 'internet';
    case DataBundle = 'data-bundle';
    case Cables = 'cables';
    case Toll = 'toll';
}
