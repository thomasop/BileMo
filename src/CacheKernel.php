<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\HttpCache\HttpCache;

class CacheKernel extends HttpCache
{
    protected function getOptions(): array
    {
        return [
            'default_ttl' => 3600,
            'trace_level' => 'full',
        ];
    }
}
