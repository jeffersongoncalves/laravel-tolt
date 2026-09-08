<?php

use JeffersonGoncalves\Tolt\Facades\Tolt as ToltFacade;
use JeffersonGoncalves\Tolt\Tolt;

it('registers the Tolt singleton', function () {
    expect(app(Tolt::class))->toBeInstanceOf(Tolt::class);
});

it('resolves the facade to the Tolt class', function () {
    expect(ToltFacade::getFacadeRoot())->toBeInstanceOf(Tolt::class);
});

it('merges the tolt config file', function () {
    expect(config('tolt.base_url'))->toBe('https://api.tolt.com/v1');
});
