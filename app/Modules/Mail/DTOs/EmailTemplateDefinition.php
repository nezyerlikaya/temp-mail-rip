<?php

namespace App\Modules\Mail\DTOs;

use App\Modules\Mail\Enums\EmailTemplateFormat;
use App\Modules\Mail\Enums\EmailTemplateStatus;
use InvalidArgumentException;

readonly class EmailTemplateDefinition
{
    /**
     * @param  list<string>  $placeholders
     */
    public function __construct(
        public string $key,
        public string $purpose,
        public string $locale,
        public int $version,
        public EmailTemplateStatus $status,
        public string $subject,
        public string $body,
        public array $placeholders = [],
        public EmailTemplateFormat $format = EmailTemplateFormat::Text,
        public string $channel = 'email',
    ) {
        if (! preg_match('/^[a-z][a-z0-9_]*(?:\.[a-z][a-z0-9_]*)*$/', $this->key)) {
            throw new InvalidArgumentException('Email template keys must use lowercase dot notation or snake case.');
        }

        if ($this->channel !== 'email') {
            throw new InvalidArgumentException('STEP010 email templates only support the email channel.');
        }

        if ($this->version < 1) {
            throw new InvalidArgumentException('Email template versions must start at 1.');
        }

        foreach ($this->placeholders as $placeholder) {
            if (! preg_match('/^[a-z][a-z0-9_]*$/', $placeholder)) {
                throw new InvalidArgumentException('Email template placeholders must use lowercase snake case.');
            }
        }

        $declared = array_flip($this->placeholders);

        foreach ($this->tokens($this->subject."\n".$this->body) as $token) {
            if (! isset($declared[$token])) {
                throw new InvalidArgumentException("Email template token [{$token}] is not declared in the placeholder contract.");
            }
        }

        if (preg_match('/[\r\n]/', $this->subject) === 1 || mb_strlen($this->subject) > 180) {
            throw new InvalidArgumentException('Email template subjects must be bounded and must not contain CRLF.');
        }

        if ($this->body === '' || mb_strlen($this->body) > 200000) {
            throw new InvalidArgumentException('Email template bodies must be non-empty and bounded.');
        }
    }

    /**
     * @return list<string>
     */
    private function tokens(string $content): array
    {
        preg_match_all('/\{\{\s*([a-zA-Z][a-zA-Z0-9_]*)\s*\}\}/', $content, $matches);

        return array_values(array_unique(array_map(
            fn (string $token): string => strtolower($token),
            $matches[1] ?? [],
        )));
    }
}
