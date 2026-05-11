<?php

declare(strict_types=1);

namespace App\Entity;

enum SurfaceType: string
{
    case Admin = 'admin';
    case Site  = 'site';
    case Api   = 'api';
}
