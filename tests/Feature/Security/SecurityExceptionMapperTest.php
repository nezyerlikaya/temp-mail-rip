<?php

namespace Tests\Feature\Security;

use Illuminate\Support\Facades\Route;
use RuntimeException;
use Tests\TestCase;

class SecurityExceptionMapperTest extends TestCase
{
    public function test_production_exception_response_does_not_leak_internal_details(): void
    {
        config(['app.debug' => false]);

        Route::get('/__security-exception-test', function (): never {
            throw new RuntimeException('database password=secret failed at '.base_path('vendor/package/file.php'));
        });

        $response = $this->get('/__security-exception-test');

        $response->assertStatus(500);
        $response->assertSeeText('Server Error');
        $response->assertDontSee('secret');
        $response->assertDontSee(base_path());
        $response->assertDontSee('database password');
    }
}
