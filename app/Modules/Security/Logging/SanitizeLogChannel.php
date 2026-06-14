<?php

namespace App\Modules\Security\Logging;

use Monolog\Logger;

class SanitizeLogChannel
{
    public function __invoke(Logger $logger): void
    {
        $logger->pushProcessor(app(SanitizedLogProcessor::class));
    }
}
