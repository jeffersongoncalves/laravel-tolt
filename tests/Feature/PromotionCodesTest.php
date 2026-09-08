<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Tolt\Facades\Tolt;

it('lists promotion codes', function () {
    Http::fake([
        'api.tolt.com/v1/promotion-codes*' => Http::response(['success' => true, 'data' => []], 200),
    ]);

    expect(Tolt::listPromotionCodes(['search' => 'SUMMER']))->toBe(['success' => true, 'data' => []]);

    Http::assertSent(fn (Request $request) => str_contains($request->url(), 'search=SUMMER')
        && str_contains($request->url(), 'program_id=prg_1'));
});

it('gets a promotion code', function () {
    Http::fake([
        'api.tolt.com/v1/promotion-codes/prc_1' => Http::response(['data' => ['id' => 'prc_1']], 200),
    ]);

    expect(Tolt::getPromotionCode('prc_1'))->toBe(['data' => ['id' => 'prc_1']]);
});

it('creates a promotion code', function () {
    Http::fake([
        'api.tolt.com/v1/promotion-codes' => Http::response(['data' => ['id' => 'prc_2']], 200),
    ]);

    expect(Tolt::createPromotionCode([
        'code' => 'SUMMER2026',
        'type' => 'percentage',
        'value' => 20,
        'partner_id' => 'part_1',
    ]))->toBe(['data' => ['id' => 'prc_2']]);

    Http::assertSent(fn (Request $request) => $request->method() === 'POST'
        && $request->data()['code'] === 'SUMMER2026');
});

it('updates a promotion code', function () {
    Http::fake([
        'api.tolt.com/v1/promotion-codes/prc_1' => Http::response(['data' => ['id' => 'prc_1']], 200),
    ]);

    expect(Tolt::updatePromotionCode('prc_1', ['type' => 'fixed', 'value' => 2500]))
        ->toBe(['data' => ['id' => 'prc_1']]);

    Http::assertSent(fn (Request $request) => $request->method() === 'PUT'
        && $request->data()['value'] === 2500);
});

it('deletes a promotion code', function () {
    Http::fake([
        'api.tolt.com/v1/promotion-codes/prc_1' => Http::response(['success' => true], 200),
    ]);

    expect(Tolt::deletePromotionCode('prc_1'))->toBe(['success' => true]);

    Http::assertSent(fn (Request $request) => $request->method() === 'DELETE');
});
