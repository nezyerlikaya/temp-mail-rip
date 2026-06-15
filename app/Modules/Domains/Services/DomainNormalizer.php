<?php

namespace App\Modules\Domains\Services;

use App\Modules\Domains\Exceptions\InvalidDomainException;

class DomainNormalizer
{
    public function normalize(string $input, bool $allowIdn = true): string
    {
        $candidate = trim($input);
        $candidate = rtrim($candidate, '.');

        if ($candidate === '') {
            throw InvalidDomainException::forInput('empty domain');
        }

        $this->rejectNonDomainInput($candidate);

        if (preg_match('/[^\x00-\x7F]/', $candidate) === 1) {
            if (! $allowIdn) {
                throw InvalidDomainException::forInput('IDN domains are disabled');
            }

            if (! function_exists('idn_to_ascii')) {
                throw InvalidDomainException::forInput('IDN normalization is not supported by this PHP runtime');
            }

            $ascii = idn_to_ascii($candidate, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);

            if ($ascii === false) {
                throw InvalidDomainException::forInput('IDN conversion failed');
            }

            $candidate = $ascii;
        }

        $canonical = strtolower($candidate);

        $this->validateCanonical($canonical);

        return $canonical;
    }

    public function displayDomain(string $canonical): string
    {
        return $this->normalize($canonical);
    }

    private function rejectNonDomainInput(string $candidate): void
    {
        if (str_contains($candidate, '://')) {
            throw InvalidDomainException::forInput('URLs are not accepted');
        }

        if (str_contains($candidate, '@')) {
            throw InvalidDomainException::forInput('email addresses are not accepted');
        }

        foreach (['/', '\\', '?', '#', ':'] as $forbidden) {
            if (str_contains($candidate, $forbidden)) {
                throw InvalidDomainException::forInput('paths, ports, queries, and fragments are not accepted');
            }
        }

        if (str_contains($candidate, '*')) {
            throw InvalidDomainException::forInput('wildcards are metadata only and are not canonical domains');
        }

        if (filter_var($candidate, FILTER_VALIDATE_IP) !== false) {
            throw InvalidDomainException::forInput('IP addresses are not accepted');
        }
    }

    private function validateCanonical(string $domain): void
    {
        if (strlen($domain) > 253) {
            throw InvalidDomainException::forInput('domain is too long');
        }

        if (! str_contains($domain, '.')) {
            throw InvalidDomainException::forInput('internal hostnames are not accepted');
        }

        $labels = explode('.', $domain);
        $tld = end($labels);

        if (in_array($tld, ['local', 'localhost', 'internal', 'lan', 'home', 'intranet'], true)) {
            throw InvalidDomainException::forInput('internal hostnames are not accepted');
        }

        if ($tld === false || strlen($tld) < 2 || preg_match('/^\d+$/', $tld) === 1) {
            throw InvalidDomainException::forInput('top-level label is invalid');
        }

        foreach ($labels as $label) {
            if ($label === '') {
                throw InvalidDomainException::forInput('empty labels are not accepted');
            }

            if (strlen($label) > 63) {
                throw InvalidDomainException::forInput('domain label is too long');
            }

            if (! preg_match('/^[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/', $label)) {
                throw InvalidDomainException::forInput('domain label contains invalid characters');
            }
        }
    }
}
