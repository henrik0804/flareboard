<?php

namespace App\Enums\Cloudflare;

enum SummaryStatus: string
{
    case None = 'none';
    case Minor = 'minor';
    case Major = 'major';
    case Critical = 'critical';

}
