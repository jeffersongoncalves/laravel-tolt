---
name: laravel-tolt-development
description: Build and work with the Laravel Tolt package, covering programs, partners, customers, clicks, links, commissions, transactions and promotion codes.
---

# Laravel Tolt Development

## When to use this skill

Use this skill when:
- Integrating the Tolt affiliate/referral program API into a Laravel app
- Adding new Tolt endpoints to this package
- Handling Tolt API errors

## Core Concepts

### The `Tolt` client

`JeffersonGoncalves\Tolt\Tolt` is a thin wrapper around Laravel's `Http` facade. It is registered as a singleton and resolved via the `Tolt` facade (`JeffersonGoncalves\Tolt\Facades\Tolt`). Every public method maps 1:1 to a Tolt v1 endpoint and returns the decoded JSON body as an array — no DTOs.

```php
use JeffersonGoncalves\Tolt\Facades\Tolt;

Tolt::getPartner('part_1');
```

### Authentication

Every request is authenticated with `Http::withToken(config('tolt.api_key'))`, sent as `Authorization: Bearer <API_KEY>`. The base URL comes from `config('tolt.base_url')`, defaulting to `https://api.tolt.com/v1`.

### Programs

Every list endpoint requires a `program_id`. The private `withProgram()` helper fills it in from `config('tolt.program_id')` unless the query array already carries one. `createPartner()` gets the same treatment, since the endpoint requires the program too.

```php
Tolt::listCustomers();                            // uses config('tolt.program_id')
Tolt::listCustomers(['program_id' => 'prg_2']);   // explicit wins
```

### Pagination

The API uses cursor pagination. Every `list*()` method takes an optional query array supporting `limit` (default 10, max 100), `starting_after`, `ending_before`, `order`, `expand`, `created_gte` and `created_lte`:

```php
Tolt::listCustomers(['limit' => 50, 'starting_after' => 'cus_100']);
```

### Money fields

`amount` and `revenue` on commissions and transactions are integers in cents (`4990` = $49.90). A promotion code's `value` is cents for `type => 'fixed'` and a 0–100 number for `type => 'percentage'`.

### Error handling

A non-2xx response throws `JeffersonGoncalves\Tolt\Exceptions\ToltException`, carrying:
- `getMessage()` — the API's `message` or `error` field, or the raw response body as a fallback
- `statusCode` (public readonly int) — the HTTP status code

The API allows 25 requests per second per key and answers `429` with a `rate_limit_error` beyond that.

```php
try {
    Tolt::createTransaction(['amount' => 4990, 'customer_id' => 'cus_1']);
} catch (\JeffersonGoncalves\Tolt\Exceptions\ToltException $e) {
    report($e);
}
```

## Common Patterns

### Adding a new endpoint

1. Add a public method to `src/Tolt.php` calling the private `get()`/`post()`/`put()`/`delete()` helpers. Wrap the payload in `withProgram()` if the endpoint requires a program.
2. Add a Feature test under `tests/Feature/` using `Http::fake()`.
3. Document the method in `README.md` under "Usage" and in this skill's API Reference.

```php
public function newEndpoint(string $id): array
{
    return $this->get("/new-endpoint/{$id}");
}
```

### Recording a referred sale end to end

```php
$customer = Tolt::createCustomer([
    'email' => 'john@example.com',
    'partner_id' => 'part_1',
]);

Tolt::createTransaction([
    'amount' => 4990,
    'customer_id' => $customer['data']['id'],
    'billing_type' => 'subscription',
    'interval' => 'month',
]);
```

## Troubleshooting

### Error: `ToltException` with status 401

**Cause**: The API key is missing or wrong.

**Solution**: Confirm `TOLT_API_KEY` is set and matches the key in Settings > Integrations.

### Error: `ToltException` complaining about a missing `program_id`

**Cause**: `TOLT_PROGRAM_ID` is unset and no `program_id` was passed to the list method.

**Solution**: Set `TOLT_PROGRAM_ID`, or pass `['program_id' => '...']`. Program IDs come from `Tolt::listPrograms()`.

### Error: `ToltException` with status 409 on `createPartner()`

**Cause**: A partner with that email already exists in the program.

**Solution**: The API returns the existing partner in the response body — read it from the exception's message or look the partner up with `listPartners(['email' => '...'])`.

## API Reference

All methods return `array<string, mixed>`. `$query` on every `list*()` method accepts `limit`, `starting_after`, `ending_before`, `order`, `expand`, `created_gte`, `created_lte`, plus `program_id` to override the configured default.

### Programs

| Method | Notes |
|--------|-------|
| `listPrograms()` | Takes no arguments |

### Partners

| Method | Notes |
|--------|-------|
| `listPartners(array $query = [])` | Also filters by `group_id`, `email`, `include` |
| `getPartner(string $id)` | |
| `createPartner(array $data)` | `first_name`, `last_name`, `email` required; `group_id`, `company_name`, `country_code`, `payout_method`, `payout_details`, `notify_partner` optional |
| `updatePartner(string $id, array $data)` | `first_name`, `last_name`, `email`, `group_id`, `company_name`, `country_code`, `payout_method`, `payout_details`, `status` |
| `deletePartner(string $id)` | |

### Customers

| Method | Notes |
|--------|-------|
| `listCustomers(array $query = [])` | Also filters by `partner_id`, `search`, `status` |
| `getCustomer(string $id)` | |
| `createCustomer(array $data)` | `email` and `partner_id` required; `name`, `subscription_id`, `customer_id`, `click_id`, `promotion_code`, `created_at`, `lead_at`, `active_at`, `status` optional |
| `updateCustomer(string $id, array $data)` | Same optional fields as create |
| `deleteCustomer(string $id)` | |

### Clicks

| Method | Notes |
|--------|-------|
| `createClick(array $data)` | Either `partner_id`, or the `param`/`value` pair; `link_id`, `country`, `device`, `page`, `referrer` optional |

### Links

| Method | Notes |
|--------|-------|
| `listLinks(array $query = [])` | Also filters by `partner_id`, `param`, `value` |
| `getLink(string $id)` | |
| `createLink(array $data)` | `param`, `value`, `partner_id` required |
| `updateLink(string $id, array $data)` | `param`, `value` |
| `deleteLink(string $id)` | |

### Commissions

| Method | Notes |
|--------|-------|
| `listCommissions(array $query = [])` | Also filters by `partner_id`, `customer_id`, `transaction_id` |
| `getCommission(string $id)` | |
| `createCommission(array $data)` | `amount` (cents) required; `program_id` and `partner_id` required when `customer_id` is omitted/null; `transaction_id`, `charge_id`, `source`, `status`, `revenue`, `created_at` optional |
| `updateCommission(string $id, array $data)` | `amount`, `transaction_id`, `charge_id`, `source`, `status`, `revenue`, `created_at` |
| `deleteCommission(string $id)` | |

### Transactions

| Method | Notes |
|--------|-------|
| `listTransactions(array $query = [])` | Also filters by `partner_id`, `customer_id` |
| `getTransaction(string $id)` | |
| `createTransaction(array $data)` | `amount` (cents) and `customer_id` required; `billing_type`, `charge_id`, `click_id`, `created_at`, `product_id`, `product_name`, `source`, `interval` optional |
| `updateTransaction(string $id, array $data)` | Same optional fields as create |
| `refundTransaction(string $id)` | `PUT /transactions/{id}/refund` |
| `deleteTransaction(string $id)` | |

### Promotion Codes

| Method | Notes |
|--------|-------|
| `listPromotionCodes(array $query = [])` | Also filters by `partner_id`, `search` |
| `getPromotionCode(string $id)` | |
| `createPromotionCode(array $data)` | `code`, `type` (`fixed`/`percentage`), `value`, `partner_id` all required |
| `updatePromotionCode(string $id, array $data)` | `type`, `value` |
| `deletePromotionCode(string $id)` | |
