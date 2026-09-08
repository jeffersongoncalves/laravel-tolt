<?php

namespace JeffersonGoncalves\Tolt;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Tolt\Exceptions\ToltException;

/**
 * Thin client for the Tolt v1 REST API (https://api.tolt.com/v1). Every method
 * returns the raw decoded JSON response as an array and authenticates the
 * request with your API key sent as a Bearer token.
 *
 * Every list endpoint requires a `program_id`. It is taken from
 * `config('tolt.program_id')` unless the `$query` array carries its own.
 *
 * List endpoints accept the API's cursor pagination options as `$query`:
 * `limit` (max 100), `starting_after`, `ending_before` and `order`.
 */
class Tolt
{
    /**
     * @return array<string, mixed>
     */
    public function listPrograms(): array
    {
        return $this->get('/programs');
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function listPartners(array $query = []): array
    {
        return $this->get('/partners', $this->withProgram($query));
    }

    /**
     * @return array<string, mixed>
     */
    public function getPartner(string $id): array
    {
        return $this->get("/partners/{$id}");
    }

    /**
     * @param  array{first_name: string, last_name: string, email: string, program_id?: string, group_id?: string, company_name?: string, country_code?: string, payout_method?: string, payout_details?: array<string, mixed>, notify_partner?: bool}  $data
     * @return array<string, mixed>
     */
    public function createPartner(array $data): array
    {
        return $this->post('/partners', $this->withProgram($data));
    }

    /**
     * @param  array{first_name?: string, last_name?: string, email?: string, group_id?: string, company_name?: string, country_code?: string, payout_method?: string, payout_details?: array<string, mixed>, status?: string}  $data
     * @return array<string, mixed>
     */
    public function updatePartner(string $id, array $data): array
    {
        return $this->put("/partners/{$id}", $data);
    }

    /**
     * @return array<string, mixed>
     */
    public function deletePartner(string $id): array
    {
        return $this->delete("/partners/{$id}");
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function listCustomers(array $query = []): array
    {
        return $this->get('/customers', $this->withProgram($query));
    }

    /**
     * @return array<string, mixed>
     */
    public function getCustomer(string $id): array
    {
        return $this->get("/customers/{$id}");
    }

    /**
     * @param  array{email: string, partner_id: string, name?: string, subscription_id?: string, customer_id?: string, click_id?: string, promotion_code?: string, created_at?: string, lead_at?: string, active_at?: string, status?: string}  $data
     * @return array<string, mixed>
     */
    public function createCustomer(array $data): array
    {
        return $this->post('/customers', $data);
    }

    /**
     * @param  array{email?: string, name?: string, subscription_id?: string, customer_id?: string, click_id?: string, promotion_code?: string, created_at?: string, lead_at?: string, active_at?: string, status?: string}  $data
     * @return array<string, mixed>
     */
    public function updateCustomer(string $id, array $data): array
    {
        return $this->put("/customers/{$id}", $data);
    }

    /**
     * @return array<string, mixed>
     */
    public function deleteCustomer(string $id): array
    {
        return $this->delete("/customers/{$id}");
    }

    /**
     * Identify the click either by `partner_id` or by the `param`/`value` pair
     * of the tracking link.
     *
     * @param  array{partner_id?: string, param?: string, value?: string, link_id?: string, country?: string, device?: string, page?: string, referrer?: string}  $data
     * @return array<string, mixed>
     */
    public function createClick(array $data): array
    {
        return $this->post('/clicks', $data);
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function listLinks(array $query = []): array
    {
        return $this->get('/links', $this->withProgram($query));
    }

    /**
     * @return array<string, mixed>
     */
    public function getLink(string $id): array
    {
        return $this->get("/links/{$id}");
    }

    /**
     * @param  array{param: string, value: string, partner_id: string}  $data
     * @return array<string, mixed>
     */
    public function createLink(array $data): array
    {
        return $this->post('/links', $data);
    }

    /**
     * @param  array{param?: string, value?: string}  $data
     * @return array<string, mixed>
     */
    public function updateLink(string $id, array $data): array
    {
        return $this->put("/links/{$id}", $data);
    }

    /**
     * @return array<string, mixed>
     */
    public function deleteLink(string $id): array
    {
        return $this->delete("/links/{$id}");
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function listCommissions(array $query = []): array
    {
        return $this->get('/commissions', $this->withProgram($query));
    }

    /**
     * @return array<string, mixed>
     */
    public function getCommission(string $id): array
    {
        return $this->get("/commissions/{$id}");
    }

    /**
     * The `amount` and `revenue` are in cents. `program_id` and `partner_id`
     * are required when `customer_id` is omitted or null.
     *
     * @param  array{amount: int, customer_id?: string|null, program_id?: string, partner_id?: string, transaction_id?: string, charge_id?: string, source?: string, status?: string, revenue?: int, created_at?: string}  $data
     * @return array<string, mixed>
     */
    public function createCommission(array $data): array
    {
        return $this->post('/commissions', $data);
    }

    /**
     * The `amount` and `revenue` are in cents.
     *
     * @param  array{amount?: int, transaction_id?: string, charge_id?: string, source?: string, status?: string, revenue?: int, created_at?: string}  $data
     * @return array<string, mixed>
     */
    public function updateCommission(string $id, array $data): array
    {
        return $this->put("/commissions/{$id}", $data);
    }

    /**
     * @return array<string, mixed>
     */
    public function deleteCommission(string $id): array
    {
        return $this->delete("/commissions/{$id}");
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function listTransactions(array $query = []): array
    {
        return $this->get('/transactions', $this->withProgram($query));
    }

    /**
     * @return array<string, mixed>
     */
    public function getTransaction(string $id): array
    {
        return $this->get("/transactions/{$id}");
    }

    /**
     * The `amount` is in cents.
     *
     * @param  array{amount: int, customer_id: string, billing_type?: string, charge_id?: string, click_id?: string, created_at?: string, product_id?: string, product_name?: string, source?: string, interval?: string}  $data
     * @return array<string, mixed>
     */
    public function createTransaction(array $data): array
    {
        return $this->post('/transactions', $data);
    }

    /**
     * The `amount` is in cents.
     *
     * @param  array{amount?: int, billing_type?: string, charge_id?: string, click_id?: string, created_at?: string, product_id?: string, product_name?: string, source?: string, interval?: string}  $data
     * @return array<string, mixed>
     */
    public function updateTransaction(string $id, array $data): array
    {
        return $this->put("/transactions/{$id}", $data);
    }

    /**
     * @return array<string, mixed>
     */
    public function refundTransaction(string $id): array
    {
        return $this->put("/transactions/{$id}/refund");
    }

    /**
     * @return array<string, mixed>
     */
    public function deleteTransaction(string $id): array
    {
        return $this->delete("/transactions/{$id}");
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function listPromotionCodes(array $query = []): array
    {
        return $this->get('/promotion-codes', $this->withProgram($query));
    }

    /**
     * @return array<string, mixed>
     */
    public function getPromotionCode(string $id): array
    {
        return $this->get("/promotion-codes/{$id}");
    }

    /**
     * `value` is an amount in cents for the `fixed` type, or a number between
     * 0 and 100 for the `percentage` type.
     *
     * @param  array{code: string, type: string, value: int|float, partner_id: string}  $data
     * @return array<string, mixed>
     */
    public function createPromotionCode(array $data): array
    {
        return $this->post('/promotion-codes', $data);
    }

    /**
     * @param  array{type?: string, value?: int|float}  $data
     * @return array<string, mixed>
     */
    public function updatePromotionCode(string $id, array $data): array
    {
        return $this->put("/promotion-codes/{$id}", $data);
    }

    /**
     * @return array<string, mixed>
     */
    public function deletePromotionCode(string $id): array
    {
        return $this->delete("/promotion-codes/{$id}");
    }

    /**
     * Fills in the configured default program, without overriding an explicit one.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function withProgram(array $payload): array
    {
        $programId = config('tolt.program_id');

        if (isset($payload['program_id']) || ! is_string($programId) || $programId === '') {
            return $payload;
        }

        return $payload + ['program_id' => $programId];
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    private function get(string $uri, array $query = []): array
    {
        return $this->handle($this->http()->get($uri, $query));
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    private function post(string $uri, array $body = []): array
    {
        return $this->handle($this->http()->post($uri, $body));
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    private function put(string $uri, array $body = []): array
    {
        return $this->handle($this->http()->put($uri, $body));
    }

    /**
     * @return array<string, mixed>
     */
    private function delete(string $uri): array
    {
        return $this->handle($this->http()->delete($uri));
    }

    private function http(): PendingRequest
    {
        return Http::withToken((string) config('tolt.api_key'))
            ->baseUrl((string) config('tolt.base_url', 'https://api.tolt.com/v1'))
            ->acceptJson();
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ToltException
     */
    private function handle(Response $response): array
    {
        if ($response->failed()) {
            throw new ToltException($this->errorMessage($response), $response->status());
        }

        $data = $response->json();

        return is_array($data) ? $data : [];
    }

    private function errorMessage(Response $response): string
    {
        $data = $response->json();

        if (is_array($data)) {
            foreach (['message', 'error'] as $field) {
                if (is_string($data[$field] ?? null)) {
                    return $data[$field];
                }
            }

            if (is_array($data['error'] ?? null) && is_string($data['error']['message'] ?? null)) {
                return $data['error']['message'];
            }
        }

        return $response->body() !== ''
            ? $response->body()
            : "Tolt API request failed with status {$response->status()}.";
    }
}
