# Laravel Shop Demo

A small but complete e-commerce site built with **Laravel 13, Blade and MySQL**, made as a public portfolio piece.
It is a demo, not a real store and not client work.

**Live demo:** https://laravel-shop-demo-production.up.railway.app · Admin: `admin@demo.test` / `demo1234` · Stripe test card `4242 4242 4242 4242`

## What it shows

| Area | Details |
| --- | --- |
| Storefront | Blade + Tailwind, categories, search, product pages, session cart. Prices are always recalculated on the server. |
| Checkout | Stripe Checkout (test mode). Hosted payment page, so no card data touches the app. |
| No double charge | Each checkout attempt gets an idempotency key (stored on the order with a unique index and sent to Stripe). Double clicks, refreshes and concurrent submits reuse the same order and the same Stripe session. |
| Webhooks | Signature verified with the Stripe SDK. Every event id is stored once (`webhook_events` unique index), so retries and replays are acknowledged but not processed twice. Order state changes run in a transaction with a row lock and only move forward (`pending → paid`). |
| Refunds | Full and partial refunds from the admin panel, with an idempotency key per refund, a row lock so two admins cannot over-refund, and status synced back from `refund.*` webhooks. |
| Admin panel | Hand-built (no package): dashboard, orders with filters, refund form, product CRUD, multilingual CMS pages, webhook event log with raw payloads. |
| i18n | English, Japanese and Vietnamese for the UI (`lang/*.json`) and for content (JSON columns per locale). |
| Tests | 25 feature tests: checkout idempotency, forged tokens, stock limits, webhook signatures and replays, refunds, admin access, CRUD. |

Without Stripe keys the app switches to a built-in **offline demo gateway** that mimics a hosted checkout page and
sends HMAC-signed webhooks through the same pipeline, so the whole flow works with zero setup.

## Architecture

```
app/Payments/PaymentGateway.php   interface: createCheckout, refund, parseWebhook
app/Payments/StripeGateway.php    Stripe implementation (idempotency keys, signature check, event mapping)
app/Payments/DemoGateway.php      offline implementation used when no keys are set
app/Services/CheckoutService.php  order creation keyed by the checkout token
app/Services/PaymentService.php   gateway-neutral webhook handling, paid/expired/refund state machine
app/Services/Cart.php             session cart
```

## Run locally

```bash
composer install && npm install && npm run build
cp .env.example .env && php artisan key:generate
php artisan demo:install
php artisan serve
php artisan test
```

To use real Stripe test mode, set `STRIPE_KEY`, `STRIPE_SECRET` and `STRIPE_WEBHOOK_SECRET`, then forward webhooks:
`stripe listen --forward-to localhost:8000/webhooks/payments`.

## Deploy on Railway

1. New project → Deploy from GitHub repo → this repository. Add a **MySQL** database to the project.
2. On the web service, set the variables from `.env.example.railway` (`APP_KEY_SEED` derives a stable app key from the MySQL password reference, or set `APP_KEY` yourself).
3. Settings → Networking → Generate domain. `railway.json` runs `php artisan demo:install` before each deploy (migrate, seed when empty).
4. In the Stripe dashboard (test mode) add a webhook endpoint `https://<domain>/webhooks/payments` for
   `checkout.session.completed`, `checkout.session.async_payment_succeeded`, `checkout.session.expired`,
   `refund.created`, `refund.updated`, `refund.failed`, and copy its signing secret into `STRIPE_WEBHOOK_SECRET`.
5. Optional: reset the public demo daily with a Railway cron service running `php artisan demo:install --fresh`.
