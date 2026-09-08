<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Tolt\Facades\Tolt;

it('lists programs without a program filter', function () {
    Http::fake([
        'api.tolt.com/v1/programs*' => Http::response(['success' => true, 'data' => []], 200),
    ]);

    expect(Tolt::listPrograms())->toBe(['success' => true, 'data' => []]);

    Http::assertSent(fn (Request $request) => ! str_contains($request->url(), 'program_id'));
});

it('lists links', function () {
    Http::fake([
        'api.tolt.com/v1/links*' => Http::response(['success' => true, 'data' => []], 200),
    ]);

    expect(Tolt::listLinks(['partner_id' => 'part_1']))->toBe(['success' => true, 'data' => []]);

    Http::assertSent(fn (Request $request) => str_contains($request->url(), 'partner_id=part_1')
        && str_contains($request->url(), 'program_id=prg_1'));
});

it('gets a link', function () {
    Http::fake([
        'api.tolt.com/v1/links/lnk_1' => Http::response(['data' => ['id' => 'lnk_1']], 200),
    ]);

    expect(Tolt::getLink('lnk_1'))->toBe(['data' => ['id' => 'lnk_1']]);
});

it('creates a link', function () {
    Http::fake([
        'api.tolt.com/v1/links' => Http::response(['data' => ['id' => 'lnk_2']], 200),
    ]);

    expect(Tolt::createLink([
        'param' => 'via',
        'value' => 'jane',
        'partner_id' => 'part_1',
    ]))->toBe(['data' => ['id' => 'lnk_2']]);

    Http::assertSent(fn (Request $request) => $request->method() === 'POST'
        && $request->data()['param'] === 'via');
});

it('updates a link', function () {
    Http::fake([
        'api.tolt.com/v1/links/lnk_1' => Http::response(['data' => ['id' => 'lnk_1']], 200),
    ]);

    expect(Tolt::updateLink('lnk_1', ['value' => 'jane-new']))->toBe(['data' => ['id' => 'lnk_1']]);

    Http::assertSent(fn (Request $request) => $request->method() === 'PUT'
        && $request->data()['value'] === 'jane-new');
});

it('deletes a link', function () {
    Http::fake([
        'api.tolt.com/v1/links/lnk_1' => Http::response(['success' => true], 200),
    ]);

    expect(Tolt::deleteLink('lnk_1'))->toBe(['success' => true]);

    Http::assertSent(fn (Request $request) => $request->method() === 'DELETE');
});

it('creates a click', function () {
    Http::fake([
        'api.tolt.com/v1/clicks' => Http::response(['data' => ['id' => 'clk_1']], 200),
    ]);

    expect(Tolt::createClick([
        'partner_id' => 'part_1',
        'country' => 'BR',
        'device' => 'mobile',
    ]))->toBe(['data' => ['id' => 'clk_1']]);

    Http::assertSent(fn (Request $request) => $request->method() === 'POST'
        && $request->data()['partner_id'] === 'part_1');
});
