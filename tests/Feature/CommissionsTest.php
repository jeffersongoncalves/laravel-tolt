<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Tolt\Facades\Tolt;

it('lists commissions', function () {
    Http::fake([
        'api.tolt.com/v1/commissions*' => Http::response(['success' => true, 'data' => []], 200),
    ]);

    expect(Tolt::listCommissions(['partner_id' => 'part_1']))->toBe(['success' => true, 'data' => []]);

    Http::assertSent(fn (Request $request) => str_contains($request->url(), 'partner_id=part_1')
        && str_contains($request->url(), 'program_id=prg_1'));
});

it('gets a commission', function () {
    Http::fake([
        'api.tolt.com/v1/commissions/comm_1' => Http::response(['data' => ['id' => 'comm_1']], 200),
    ]);

    expect(Tolt::getCommission('comm_1'))->toBe(['data' => ['id' => 'comm_1']]);
});

it('creates a commission with the amount in cents', function () {
    Http::fake([
        'api.tolt.com/v1/commissions' => Http::response(['data' => ['id' => 'comm_2']], 200),
    ]);

    expect(Tolt::createCommission([
        'amount' => 2999,
        'customer_id' => 'cus_1',
    ]))->toBe(['data' => ['id' => 'comm_2']]);

    Http::assertSent(fn (Request $request) => $request->method() === 'POST'
        && $request->data()['amount'] === 2999);
});

it('updates a commission', function () {
    Http::fake([
        'api.tolt.com/v1/commissions/comm_1' => Http::response(['data' => ['id' => 'comm_1']], 200),
    ]);

    expect(Tolt::updateCommission('comm_1', ['status' => 'approved']))->toBe(['data' => ['id' => 'comm_1']]);

    Http::assertSent(fn (Request $request) => $request->method() === 'PUT'
        && $request->data()['status'] === 'approved');
});

it('deletes a commission', function () {
    Http::fake([
        'api.tolt.com/v1/commissions/comm_1' => Http::response(['success' => true], 200),
    ]);

    expect(Tolt::deleteCommission('comm_1'))->toBe(['success' => true]);

    Http::assertSent(fn (Request $request) => $request->method() === 'DELETE');
});
