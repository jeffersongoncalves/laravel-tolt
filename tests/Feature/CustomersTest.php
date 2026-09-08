<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Tolt\Facades\Tolt;

it('lists customers', function () {
    Http::fake([
        'api.tolt.com/v1/customers*' => Http::response(['success' => true, 'data' => []], 200),
    ]);

    expect(Tolt::listCustomers(['status' => 'active']))->toBe(['success' => true, 'data' => []]);

    Http::assertSent(fn (Request $request) => str_contains($request->url(), 'status=active')
        && str_contains($request->url(), 'program_id=prg_1'));
});

it('gets a customer', function () {
    Http::fake([
        'api.tolt.com/v1/customers/cus_1' => Http::response(['data' => ['id' => 'cus_1']], 200),
    ]);

    expect(Tolt::getCustomer('cus_1'))->toBe(['data' => ['id' => 'cus_1']]);
});

it('creates a customer', function () {
    Http::fake([
        'api.tolt.com/v1/customers' => Http::response(['data' => ['id' => 'cus_2']], 200),
    ]);

    expect(Tolt::createCustomer([
        'email' => 'john@example.com',
        'partner_id' => 'part_1',
    ]))->toBe(['data' => ['id' => 'cus_2']]);

    Http::assertSent(fn (Request $request) => $request->method() === 'POST'
        && $request->data()['partner_id'] === 'part_1');
});

it('updates a customer', function () {
    Http::fake([
        'api.tolt.com/v1/customers/cus_1' => Http::response(['data' => ['id' => 'cus_1']], 200),
    ]);

    expect(Tolt::updateCustomer('cus_1', ['status' => 'active']))->toBe(['data' => ['id' => 'cus_1']]);

    Http::assertSent(fn (Request $request) => $request->method() === 'PUT'
        && $request->data()['status'] === 'active');
});

it('deletes a customer', function () {
    Http::fake([
        'api.tolt.com/v1/customers/cus_1' => Http::response(['success' => true], 200),
    ]);

    expect(Tolt::deleteCustomer('cus_1'))->toBe(['success' => true]);

    Http::assertSent(fn (Request $request) => $request->method() === 'DELETE');
});
