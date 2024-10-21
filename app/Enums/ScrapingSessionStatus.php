<?php

namespace App\Enums;

enum ScrapingSessionStatus: int
{
    case RUNNING = 0;
    case COMPLETED = 1;
    case FAILED = 13;
}
