<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\HttpCache\HttpCache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheKernel extends HttpCache
{
    protected function getOptions(): array
    {
        return [
            'default_ttl' => 3600,
            'trace_level' => 'full'
        ];
    }
}