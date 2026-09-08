## Laravel Tolt

This package provides a fluent `Tolt` facade for the [Tolt](https://tolt.com) affiliate and referral program API: programs, partners, customers, clicks, links, commissions, transactions and promotion codes.

### Installation

@verbatim
<code-snippet name="Install the package" lang="bash">
composer require jeffersongoncalves/laravel-tolt
</code-snippet>
@endverbatim

Set `TOLT_API_KEY` and `TOLT_PROGRAM_ID` in `.env`. Optionally override `TOLT_BASE_URL`.

### Features

- **Programs**: `listPrograms()`.
- **Partners**: `listPartners()`, `getPartner()`, `createPartner()`, `updatePartner()`, `deletePartner()`.
- **Customers**: `listCustomers()`, `getCustomer()`, `createCustomer()`, `updateCustomer()`, `deleteCustomer()`.
- **Clicks**: `createClick()`.
- **Links**: `listLinks()`, `getLink()`, `createLink()`, `updateLink()`, `deleteLink()`.
- **Commissions**: `listCommissions()`, `getCommission()`, `createCommission()`, `updateCommission()`, `deleteCommission()` — amounts in cents.
- **Transactions**: `listTransactions()`, `getTransaction()`, `createTransaction()`, `updateTransaction()`, `refundTransaction()`, `deleteTransaction()` — amounts in cents.
- **Promotion Codes**: `listPromotionCodes()`, `getPromotionCode()`, `createPromotionCode()`, `updatePromotionCode()`, `deletePromotionCode()`.

@verbatim
<code-snippet name="Create a customer and a transaction" lang="php">
use JeffersonGoncalves\Tolt\Facades\Tolt;

Tolt::createCustomer([
    'email' => 'john@example.com',
    'partner_id' => 'part_1',
]);

Tolt::createTransaction([
    'amount' => 4990,
    'customer_id' => 'cus_1',
    'billing_type' => 'subscription',
]);
</code-snippet>
@endverbatim

### Configuration

@verbatim
<code-snippet name="Config example" lang="php">
// config/tolt.php
return [
    'api_key' => env('TOLT_API_KEY'),
    'program_id' => env('TOLT_PROGRAM_ID'),
    'base_url' => env('TOLT_BASE_URL', 'https://api.tolt.com/v1'),
];
</code-snippet>
@endverbatim

### Best Practices

- Every method returns the raw decoded JSON response as an array — there are no DTOs to keep the client thin.
- Every list endpoint requires a `program_id`. It is filled in from `config('tolt.program_id')`; pass `program_id` in the query array only to target another program.
- Pass query and cursor pagination options (`limit` max 100, `starting_after`, `ending_before`, `order`, `expand`, `created_gte`, `created_lte`) as the array argument of any `list*()` method.
- Money fields (`amount` and `revenue` on commissions and transactions, `value` on a `fixed` promotion code) are integers in cents — never pass a float.
- Always wrap calls in a `try`/`catch` for `\JeffersonGoncalves\Tolt\Exceptions\ToltException` — it is thrown on any non-2xx response and carries the API's message plus the HTTP status code.
- The API allows 25 requests per second per key; back off on a `429`.
