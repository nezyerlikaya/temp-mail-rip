<?php

namespace App\Modules\Domains\Services;

use App\Modules\Domains\Exceptions\InvalidDomainException;

class DomainNotesPolicy
{
    public function validate(?string $notes): ?string
    {
        if ($notes === null) {
            return null;
        }

        $notes = trim($notes);

        if ($notes === '') {
            return null;
        }

        if (mb_strlen($notes) > 2000) {
            throw InvalidDomainException::forInput('domain notes are too long');
        }

        $forbidden = [
            '/password\s*[:=]/i',
            '/token\s*[:=]/i',
            '/api[_-]?key\s*[:=]/i',
            '/secret\s*[:=]/i',
            '/authorization\s*[:=]/i',
            '/registrar\s+login/i',
        ];

        foreach ($forbidden as $pattern) {
            if (preg_match($pattern, $notes) === 1) {
                throw InvalidDomainException::forInput('domain notes must not contain credentials or provider secrets');
            }
        }

        return $notes;
    }
}
