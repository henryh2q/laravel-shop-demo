<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Collection;

/**
 * Session cart: ['product_id' => quantity]. Prices are always re-read from the
 * database, never trusted from the session or the form.
 */
class Cart
{
    private const KEY = 'cart';

    public const MAX_QTY = 20;

    public function __construct(private readonly Session $session) {}

    public function add(Product $product, int $quantity = 1): void
    {
        $items = $this->raw();
        $items[$product->id] = min(self::MAX_QTY, ($items[$product->id] ?? 0) + max(1, $quantity));
        $this->store($items);
    }

    public function update(int $productId, int $quantity): void
    {
        $items = $this->raw();

        if ($quantity <= 0) {
            unset($items[$productId]);
        } else {
            $items[$productId] = min(self::MAX_QTY, $quantity);
        }

        $this->store($items);
    }

    public function clear(): void
    {
        $this->session->forget([self::KEY, CheckoutService::TOKEN_KEY]);
    }

    public function count(): int
    {
        return array_sum($this->raw());
    }

    /**
     * @return Collection<int, object{product: Product, quantity: int, line_total: int}>
     */
    public function lines(): Collection
    {
        $items = $this->raw();

        if ($items === []) {
            return collect();
        }

        return Product::active()->whereIn('id', array_keys($items))->get()
            ->map(fn (Product $product) => (object) [
                'product' => $product,
                'quantity' => $items[$product->id],
                'line_total' => $product->price * $items[$product->id],
            ])
            ->values();
    }

    public function subtotal(): int
    {
        return (int) $this->lines()->sum('line_total');
    }

    /** @return array<int, int> */
    private function raw(): array
    {
        return $this->session->get(self::KEY, []);
    }

    private function store(array $items): void
    {
        $this->session->put(self::KEY, $items);
        // Cart changed => this is a new checkout attempt with a new idempotency key.
        $this->session->forget(CheckoutService::TOKEN_KEY);
    }
}
