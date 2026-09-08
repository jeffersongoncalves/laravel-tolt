<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Tolt\Exceptions\ToltException;
use JeffersonGoncalves\Tolt\Facades\Tolt;

it('lists partners with the configured program', function () {
    Http::fake([
        'api.tolt.com/v1/partners*' => Http::response(['success' => true, 'data' => []], 200),
    ]);

    expect(Tolt::listPartners(['limit' => 50]))->toBe(['success' => true, 'data' => []]);

    Http::assertSent(fn (Request $request) => str_contains($request->url(), 'program_id=prg_1')
        && str_contains($request->url(), 'limit=50'));
});

it('lets an explicit program override the configured one', function () {
    Http::fake([
        'api.tolt.com/v1/partners*' => Http::response([], 200),
    ]);

    Tolt::listPartners(['program_id' => 'prg_2']);

    Http::assertSent(fn (Request $request) => str_contains($request->url(), 'program_id=prg_2'));
});

it('authenticates with a bearer token', function () {
    Http::fake([
        'api.tolt.com/v1/partners*' => Http::response([], 200),
    ]);

    Tolt::listPartners();

    Http::assertSent(fn (Request $request) => $request->header('Authorization')[0] === 'Bearer fake-api-key');
});

it('gets a partner', function () {
    Http::fake([
        'api.tolt.com/v1/partners/part_1' => Http::response(['data' => ['id' => 'part_1']], 200),
    ]);

    expect(Tolt::getPartner('part_1'))->toBe(['data' => ['id' => 'part_1']]);
});

it('creates a partner', function () {
    Http::fake([
        'api.tolt.com/v1/partners' => Http::response(['data' => ['id' => 'part_2']], 200),
    ]);

    expect(Tolt::createPartner([
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'email' => 'jane@example.com',
    ]))->toBe(['data' => ['id' => 'part_2']]);

    Http::assertSent(fn (Request $request) => $request->method() === 'POST'
        && $request->data()['email'] === 'jane@example.com'
        && $request->data()['program_id'] === 'prg_1');
});

it('updates a partner', function () {
    Http::fake([
        'api.tolt.com/v1/partners/part_1' => Http::response(['data' => ['id' => 'part_1']], 200),
    ]);

    expect(Tolt::updatePartner('part_1', ['status' => 'suspended']))->toBe(['data' => ['id' => 'part_1']]);

    Http::assertSent(fn (Request $request) => $request->method() === 'PUT'
        && $request->data()['status'] === 'suspended');
});

it('deletes a partner', function () {
    Http::fake([
        'api.tolt.com/v1/partners/part_1' => Http::response(['success' => true], 200),
    ]);

    expect(Tolt::deletePartner('part_1'))->toBe(['success' => true]);

    Http::assertSent(fn (Request $request) => $request->method() === 'DELETE');
});

it('throws a ToltException on a failed response', function () {
    Http::fake([
        'api.tolt.com/v1/partners/part_1' => Http::response(['message' => 'Partner not found'], 404),
    ]);

    expect(fn () => Tolt::getPartner('part_1'))
        ->toThrow(ToltException::class, 'Partner not found');
});

it('exposes the http status code on the exception', function () {
    Http::fake([
        'api.tolt.com/v1/partners*' => Http::response(['message' => 'Too many requests. Please try again'], 429),
    ]);

    try {
        Tolt::listPartners();
    } catch (ToltException $e) {
        expect($e->statusCode)->toBe(429);
    }
});
