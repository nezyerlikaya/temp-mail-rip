<?php

namespace App\Modules\Security\Logging;

use App\Modules\Security\Services\PathAnonymizer;
use App\Modules\Security\Services\SafeDiagnosticsFormatter;
use App\Modules\Security\Services\SecretMasker;
use Monolog\LogRecord;

class SanitizedLogProcessor
{
    public function __construct(
        private readonly SecretMasker $secretMasker,
        private readonly PathAnonymizer $pathAnonymizer,
        private readonly SafeDiagnosticsFormatter $diagnosticsFormatter,
    ) {}

    public function __invoke(LogRecord $record): LogRecord
    {
        return $record->with(
            message: $this->pathAnonymizer->anonymizeString($this->secretMasker->maskText($record->message)),
            context: $this->diagnosticsFormatter->format($record->context),
            extra: $this->diagnosticsFormatter->format($record->extra),
        );
    }
}
