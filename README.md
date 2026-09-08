<div class="filament-hidden">

![Laravel Tolt](https://raw.githubusercontent.com/jeffersongoncalves/laravel-tolt/main/art/jeffersongoncalves-laravel-tolt.png)

</div>

# Laravel Tolt

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jeffersongoncalves/laravel-tolt.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-tolt)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/laravel-tolt/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/jeffersongoncalves/laravel-tolt/actions?query=workflow%3Atests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/laravel-tolt/pint.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/jeffersongoncalves/laravel-tolt/actions?query=workflow%3A%22Fix+PHP+code+styling%22+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/jeffersongoncalves/laravel-tolt.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-tolt)
[![License](https://img.shields.io/packagist/l/jeffersongoncalves/laravel-tolt.svg?style=flat-square)](LICENSE.md)

A Laravel client for the [Tolt](https://tolt.com) affiliate and referral program API. A fluent `Tolt` facade covers programs, partners, customers, clicks, links, commissions, transactions and promotion codes, authenticates every request with your API key as a Bearer token, fills in your default program on list endpoints, and throws a `ToltException` on a non-2xx response instead of returning a silent error array.

## Features

- **Programs** — `listPrograms()`
- **Partners** — `listPartners()`, `getPartner()`, `createPartner()`, `updatePartner()`, `deletePartner()`
- **Customers** — `listCustomers()`, `getCustomer()`, `createCustomer()`, `updateCustomer()`, `deleteCustomer()`
- **Clicks** — `createClick()`
- **Links** — `listLinks()`, `getLink()`, `createLink()`, `updateLink()`, `deleteLink()`
- **Commissions** — `listCommissions()`, `getCommission()`, `createCommission()`, `updateCommission()`, `deleteCommission()`
- **Transactions** — `listTransactions()`, `getTransaction()`, `createTransaction()`, `updateTransaction()`, `refundTransaction()`, `deleteTransaction()`
- **Promotion Codes** — `listPromotionCodes()`, `getPromotionCode()`, `createPromotionCode()`, `updatePromotionCode()`, `deletePromotionCode()`
- **Default program** — every list endpoint requires a `program_id`; set `TOLT_PROGRAM_ID` once and it is sent for you
- **Thin by design** — every method returns the raw decoded JSON response as an array, no DTOs
- **Fails loud** — a non-2xx API response throws `ToltException` carrying the API's error message and HTTP status code

## Installation

You can install the package via composer:

```bash
composer require jeffersongoncalves/laravel-tolt
```

Optionally publish the config file:

```bash
php artisan vendor:publish --tag="tolt-config"
```

## Configuration

Add to your `.env`:

```env
TOLT_API_KEY=your-api-key
TOLT_PROGRAM_ID=prg_your-program-id
```

The API key lives in your Tolt dashboard under **Settings > Integrations**. Program IDs come from `Tolt::listPrograms()`.

### Config Options

```php
// config/tolt.php
return [
    'api_key' => env('TOLT_API_KEY'),
    'program_id' => env('TOLT_PROGRAM_ID'),
    'base_url' => env('TOLT_BASE_URL', 'https://api.tolt.com/v1'),
];
```

## Usage

```php
use JeffersonGoncalves\Tolt\Exceptions\ToltException;
use JeffersonGoncalves\Tolt\Facades\Tolt;
```

Every `list*()` method accepts the API's query and cursor pagination options — `limit` (max 100), `starting_after`, `ending_before`, `order`, `expand`, `created_gte` and `created_lte`:

```php
Tolt::listPartners(['limit' => 50, 'order' => 'asc']);
```

The `program_id` required by every list endpoint comes from `config('tolt.program_id')`. Pass your own to override it:

```php
Tolt::listPartners(['program_id' => 'prg_other']);
```

### Programs

```php
Tolt::listPrograms();
```

### Partners

```php
Tolt::listPartners(['group_id' => 'grp_1']);
Tolt::getPartner('part_1');

Tolt::createPartner([
    'first_name' => 'Jane',
    'last_name' => 'Doe',
    'email' => 'jane@example.com',
    'payout_method' => 'paypal',
    'notify_partner' => true,
]);

Tolt::updatePartner('part_1', ['status' => 'suspended']);
Tolt::deletePartner('part_1');
```

### Customers

```php
Tolt::listCustomers(['status' => 'active']);
Tolt::getCustomer('cus_1');

Tolt::createCustomer([
    'email' => 'john@example.com',
    'partner_id' => 'part_1',
    'subscription_id' => 'sub_123',
    'status' => 'trialing',
]);

Tolt::updateCustomer('cus_1', ['status' => 'active']);
Tolt::deleteCustomer('cus_1');
```

### Clicks

Identify the click either by `partner_id` or by the `param`/`value` pair of the tracking link.

```php
Tolt::createClick([
    'partner_id' => 'part_1',
    'country' => 'BR',
    'device' => 'mobile',
    'page' => 'https://example.com/pricing',
]);
```

### Links

```php
Tolt::listLinks(['partner_id' => 'part_1']);
Tolt::getLink('lnk_1');

Tolt::createLink([
    'param' => 'via',
    'value' => 'jane',
    'partner_id' => 'part_1',
]);

Tolt::updateLink('lnk_1', ['value' => 'jane-new']);
Tolt::deleteLink('lnk_1');
```

### Commissions

Amounts are in cents. `program_id` and `partner_id` are required when `customer_id` is omitted or null.

```php
Tolt::listCommissions(['partner_id' => 'part_1']);
Tolt::getCommission('comm_1');

Tolt::createCommission([
    'amount' => 2999,
    'customer_id' => 'cus_1',
    'revenue' => 14999,
    'status' => 'pending',
]);

Tolt::updateCommission('comm_1', ['status' => 'approved']);
Tolt::deleteCommission('comm_1');
```

### Transactions

Amounts are in cents.

```php
Tolt::listTransactions(['customer_id' => 'cus_1']);
Tolt::getTransaction('txn_1');

Tolt::createTransaction([
    'amount' => 4990,
    'customer_id' => 'cus_1',
    'billing_type' => 'subscription',
    'interval' => 'month',
]);

Tolt::updateTransaction('txn_1', ['amount' => 5990]);
Tolt::refundTransaction('txn_1');
Tolt::deleteTransaction('txn_1');
```

### Promotion Codes

`value` is an amount in cents for the `fixed` type, or a number between 0 and 100 for the `percentage` type.

```php
Tolt::listPromotionCodes(['search' => 'SUMMER']);
Tolt::getPromotionCode('prc_1');

Tolt::createPromotionCode([
    'code' => 'SUMMER2026',
    'type' => 'percentage',
    'value' => 20,
    'partner_id' => 'part_1',
]);

Tolt::updatePromotionCode('prc_1', ['type' => 'fixed', 'value' => 2500]);
Tolt::deletePromotionCode('prc_1');
```

### Handling errors

The API allows 25 requests per second per key and answers `429` beyond that.

```php
try {
    $partner = Tolt::getPartner('part_1');
} catch (ToltException $e) {
    // $e->getMessage()  — the API's error message, or the raw response body
    // $e->statusCode    — the HTTP status code returned by Tolt
}
```

## Testing

```bash
composer test
```

## Static Analysis

```bash
composer analyse
```

## Code Formatting

```bash
composer format
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Jefferson Simão Gonçalves](https://github.com/jeffersongoncalves)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
