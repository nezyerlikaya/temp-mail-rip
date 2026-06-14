<?php

namespace App\Modules\SystemHealth\Services;

use App\Modules\SystemHealth\DTOs\HealthSummary;

class DegradedStateResolver
{
    /**
     * @return list<string>
     */
    public function reasons(HealthSummary $summary): array
    {
        $reasons = [];

        foreach ($summary->results as $result) {
            if ($result->productionBlocking()) {
                $reasons[] = $result->key;
            }
        }

        return array_values(array_unique($reasons));
    }

    public function degraded(HealthSummary $summary): bool
    {
        return $this->reasons($summary) !== [];
    }
}
