<?php

namespace App\Modules\Uploads\Services;

use App\Modules\Uploads\Exceptions\UploadValidationException;

class FilenameNormalizer
{
    /**
     * @var list<string>
     */
    private array $dangerousExtensions = ['php', 'phtml', 'phar', 'js', 'html', 'htm', 'svg', 'exe', 'sh', 'bat', 'cmd', 'ps1'];

    public function sanitizeOriginal(string $filename): string
    {
        if (str_contains($filename, "\0") || preg_match('/[\x00-\x1F\x7F]/', $filename)) {
            throw UploadValidationException::forReason('Filename contains unsafe control characters.');
        }

        if (str_contains($filename, '/') || str_contains($filename, '\\') || str_contains($filename, '..')) {
            throw UploadValidationException::forReason('Filename contains unsafe path segments.');
        }

        $filename = trim(preg_replace('/[^A-Za-z0-9._ -]+/', '-', $filename) ?? 'file');
        $filename = preg_replace('/\s+/', ' ', $filename) ?? $filename;
        $filename = trim($filename, '. ');

        if ($filename === '') {
            $filename = 'file';
        }

        return substr($filename, 0, 120);
    }

    public function extension(string $filename): string
    {
        $sanitized = $this->sanitizeOriginal($filename);
        $segments = array_values(array_filter(explode('.', strtolower($sanitized)), fn (string $segment): bool => $segment !== ''));

        if (count($segments) < 2) {
            throw UploadValidationException::forReason('Uploaded file extension is missing.');
        }

        foreach (array_slice($segments, 0, -1) as $segment) {
            if (in_array($segment, $this->dangerousExtensions, true)) {
                throw UploadValidationException::forReason('Dangerous double extension is not allowed.');
            }
        }

        $extension = end($segments);

        if (in_array($extension, $this->dangerousExtensions, true)) {
            throw UploadValidationException::forReason('Dangerous file extension is not allowed.');
        }

        return $extension;
    }

    public function generatedName(string $extension): string
    {
        return bin2hex(random_bytes(20)).'.'.$extension;
    }
}
