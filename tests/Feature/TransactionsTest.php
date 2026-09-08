<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Tolt\Facades\Tolt;

it('lists transactions', function () {
    Http::fake([
        'api.tolt.com/v1/transactions*' => Http::response(['success' => true, 'data' => []], 200),
    ]);

    expect(Tolt::listTransactions(['limit' => 100]))->toBe(['success' => true, 'data' => []]);

    Http::assertSent(fn (Request $request) => str_contains($request->url(), 'limit=100')
        && str_contains($request->url(), 'program_id=prg_1'));
});

it('gets a transaction', function () {
    Http::fake([
        'api.tolt.com/v1/transactions/txn_1' => Http::response(['data' => ['id' => 'txn_1']], 200),
    ]);

    expect(Tolt::getTransaction('txn_1'))->toBe(['data' => ['id' => 'txn_1']]);
});

it('creates a transaction with the amount in cents', function () {
    Http::fake([
        'api.tolt.com/v1/transactions' => Http::response(['data' => ['id' => 'txn_2']], 200),
    ]);

    expect(Tolt::createTransaction([
        'amount' => 4990,
        'customer_id' => 'cus_1',
        'billing_type' => 'subscription',
        'interval' => 'month',
    ]))->toBe(['data' => ['id' => 'txn_2']]);

    Http::assertSent(fn (Request $request) => $request->method() === 'POST'
        && $request->data()['amount'] === 4990
        && $request->data()['interval'] === 'month');
});

it('updates a transaction', function () {
    Http::fake([
        'api.tolt.com/v1/transactions/txn_1' => Http::response(['data' => ['id' => 'txn_1']], 200),
    ]);

    expect(Tolt::updateTransaction('txn_1', ['amount' => 5990]))->toBe(['data' => ['id' => 'txn_1']]);

    Http::assertSent(fn (Request $request) => $request->method() === 'PUT'
        && $request->data()['amount'] === 5990);
});

it('refunds a transaction', function () {
    Http::fake([
        'api.tolt.com/v1/transactions/txn_1/refund' => Http::response(['data' => ['status' => 'refunded']], 200),
    ]);

    expect(Tolt::refundTransaction('txn_1'))->toBe(['data' => ['status' => 'refunded']]);

    Http::assertSent(fn (Request $request) => $request->method() === 'PUT'
        && str_ends_with($request->url(), '/transactions/txn_1/refund'));
});

it('deletes a transaction', function () {
    Http::fake([
        'api.tolt.com/v1/transactions/txn_1' => Http::response(['success' => true], 200),
    ]);

    expect(Tolt::deleteTransaction('txn_1'))->toBe(['success' => true]);

    Http::assertSent(fn (Request $request) => $request->method() === 'DELETE');
});
