<?php

namespace App\Modules\Translation\Services;

class TranslationValueProvider
{
    /**
     * @return array<string, array<string, string>>
     */
    public function values(): array
    {
        return [
            'en' => [
                'system.app.name' => 'Temp Mail',
                'system.missing_key' => 'Missing translation: :key',
                'navigation.home' => 'Home',
                'auth.login' => 'Log in',
                'auth.logout' => 'Log out',
                'mailboxes.create.success' => 'Mailbox :address was created.',
            ],
            'tr' => [
                'system.app.name' => 'Temp Mail',
                'system.missing_key' => 'Eksik çeviri: :key',
                'navigation.home' => 'Ana sayfa',
                'auth.login' => 'Giriş yap',
                'auth.logout' => 'Çıkış yap',
                'mailboxes.create.success' => ':address posta kutusu oluşturuldu.',
            ],
        ];
    }
}
