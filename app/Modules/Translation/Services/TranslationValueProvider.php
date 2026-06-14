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
                'admin.navigation.dashboard' => 'Dashboard',
                'admin.dashboard.title' => 'Admin dashboard',
                'admin.dashboard.foundation_status' => 'Foundation shell only. Authentication and authorization arrive in later steps.',
                'admin.shell.skip_to_content' => 'Skip to content',
                'admin.shell.primary_navigation' => 'Admin navigation',
                'admin.shell.temporary_access_notice' => 'Temporary development shell. Production access is blocked until authentication and authorization are implemented.',
                'admin.empty.title' => 'No admin content yet',
                'admin.empty.description' => 'This shell is ready for future authorized modules.',
                'auth.login' => 'Log in',
                'auth.logout' => 'Log out',
                'mailboxes.create.success' => 'Mailbox :address was created.',
            ],
            'tr' => [
                'system.app.name' => 'Temp Mail',
                'system.missing_key' => 'Eksik çeviri: :key',
                'navigation.home' => 'Ana sayfa',
                'admin.navigation.dashboard' => 'Pano',
                'admin.dashboard.title' => 'Admin panosu',
                'admin.dashboard.foundation_status' => 'Yalnızca temel kabuk. Kimlik doğrulama ve yetkilendirme sonraki adımlarda gelecek.',
                'admin.shell.skip_to_content' => 'İçeriğe geç',
                'admin.shell.primary_navigation' => 'Admin navigasyonu',
                'admin.shell.temporary_access_notice' => 'Geçici geliştirme kabuğu. Kimlik doğrulama ve yetkilendirme uygulanana kadar production erişimi kapalıdır.',
                'admin.empty.title' => 'Henüz admin içeriği yok',
                'admin.empty.description' => 'Bu kabuk gelecekteki yetkili modüller için hazır.',
                'auth.login' => 'Giriş yap',
                'auth.logout' => 'Çıkış yap',
                'mailboxes.create.success' => ':address posta kutusu oluşturuldu.',
            ],
        ];
    }
}
