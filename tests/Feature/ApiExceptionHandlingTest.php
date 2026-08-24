<?php

namespace Tests\Feature;

use App\Domain\Product\ProductNotFoundException;
use App\Services\Product\GetProductService;
use App\Services\Product\ListProductsService;
use Mockery\MockInterface;
use RuntimeException;
use Tests\TestCase;

class ApiExceptionHandlingTest extends TestCase
{
    public function test_unhandled_exception_returns_json_500_without_internals_when_debug_is_off(): void
    {
        config(['app.debug' => false]);

        $this->mock(ListProductsService::class, function (MockInterface $mock) {
            $mock->shouldReceive('handle')
                ->once()
                ->andThrow(new RuntimeException('database unavailable'));
        });

        $response = $this->getJson('/api/products');

        $response->assertStatus(500)
            ->assertJsonPath('error', 'internal_server_error')
            ->assertJsonPath('message', __('messages.errors.internal_server'))
            ->assertJsonMissingPath('debug')
            ->assertHeader('X-Request-Id');

        $this->assertNotEmpty($response->json('request_id'));
        $this->assertSame(
            $response->headers->get('X-Request-Id'),
            $response->json('request_id'),
        );
    }

    public function test_unhandled_exception_includes_debug_details_when_debug_is_on(): void
    {
        config(['app.debug' => true]);

        $this->mock(ListProductsService::class, function (MockInterface $mock) {
            $mock->shouldReceive('handle')
                ->once()
                ->andThrow(new RuntimeException('database unavailable'));
        });

        $this->getJson('/api/products')
            ->assertStatus(500)
            ->assertJsonPath('error', 'internal_server_error')
            ->assertJsonPath('debug.exception', RuntimeException::class)
            ->assertJsonPath('debug.message', 'database unavailable');
    }

    public function test_domain_exception_is_not_converted_into_a_500(): void
    {
        $this->mock(GetProductService::class, function (MockInterface $mock) {
            $mock->shouldReceive('handle')
                ->once()
                ->andThrow(new ProductNotFoundException(99));
        });

        $this->getJson('/api/products/99')
            ->assertStatus(404)
            ->assertJsonPath('message', __('messages.errors.product_not_found', ['id' => 99]))
            ->assertJsonMissingPath('error');
    }

    public function test_incoming_request_id_is_propagated_on_error_responses(): void
    {
        config(['app.debug' => false]);

        $this->mock(ListProductsService::class, function (MockInterface $mock) {
            $mock->shouldReceive('handle')
                ->once()
                ->andThrow(new RuntimeException('boom'));
        });

        $this->getJson('/api/products', ['X-Request-Id' => 'req-observability-1'])
            ->assertStatus(500)
            ->assertHeader('X-Request-Id', 'req-observability-1')
            ->assertJsonPath('request_id', 'req-observability-1');
    }

    public function test_error_messages_are_translated_when_accept_language_is_spanish(): void
    {
        config(['app.debug' => false]);

        $this->mock(ListProductsService::class, function (MockInterface $mock) {
            $mock->shouldReceive('handle')
                ->once()
                ->andThrow(new RuntimeException('boom'));
        });

        $this->withHeaders(['Accept-Language' => 'es'])
            ->getJson('/api/products')
            ->assertStatus(500)
            ->assertHeader('Content-Language', 'es')
            ->assertJsonPath('message', 'Error interno del servidor.');
    }
}
