# bizswoop/jovvie-payments

PHP SDK for the Jovvie Payments API.

- Requires **PHP ≥ 7.4** with the `curl` and `json` extensions
- No third-party dependencies; PSR-4 autoloaded under the `JovviePayments\` namespace

Every request is signed with an HS256 JWT made from your secret key. Use the client **server-side only**.

## Install

```sh
composer require bizswoop/jovvie-payments
```

## Quick start

```php
use JovviePayments\JovviePaymentsClient;

$client = new JovviePaymentsClient(
	getenv('JOVVIE_PUBLIC_KEY'),
	getenv('JOVVIE_SECRET_KEY'),
	'my-platform', // platform provider
	'test'         // 'live' (default) or 'test'
);

$payment = $client->payments()->createOneTimePayment(
	1000, // amount in the smallest currency unit
	'usd',
	'Order #1001',
	'https://shop.example/success',
	'https://shop.example/cancel'
);

// Redirect the customer to the checkout page.
header('Location: ' . $payment->generatedData->checkoutLink);
```

## Namespaces

| Namespace                                | Contents                                                                    |
| ---------------------------------------- | --------------------------------------------------------------------------- |
| `JovviePayments`                         | `JovviePaymentsClient`, `Payments`, `Accounts`, …, `WebhookEvent`           |
| `JovviePayments\Http`                    | `Client` (base class with `request()`), `Response`                          |
| `JovviePayments\Http\Error`              | `ResponseError`, `BadRequestError`, `UnauthorizedError`, …                  |
| `JovviePayments\Schema`                  | `Account`, `SelfAccount`, `AuthorizationLink`, `ConnectionToken`            |
| `JovviePayments\Schema\Payment`          | `Payment` (base of both payment types), `LineItem`, `GeneratedData`         |
| `JovviePayments\Schema\OneTimePayment`   | `OneTimePayment`, `OneTimeGeneratedData`, `OneTimePaymentLineItem`          |
| `JovviePayments\Schema\SubscriptionPayment` | `SubscriptionPayment`, `SubscriptionGeneratedData`, …                    |
| `JovviePayments\WebhookEvents\Events`    | `BaseEvent`, event classes, `UnknownEvent`                                  |
| `JovviePayments\WebhookEvents\Error`     | `WebhookError`, `UnknownEventError`                                         |

Schema classes expose their data as public properties and implement `JsonSerializable`.

## Client options

```php
new JovviePaymentsClient(string $publicKey, string $secretKey, string $platformProvider, string $mode = 'live', ?array $config = null);
```

| Argument           | Default    | Description                                                                         |
| ------------------ | ---------- | ----------------------------------------------------------------------------------- |
| `$publicKey`       | required   | Public key.                                                                         |
| `$secretKey`       | required   | Secret key; signs the JWT and never leaves the server.                              |
| `$platformProvider`| required   | Platform provider ID, sent as `Platform-Provider`. `'debug'` allows only `'test'`.  |
| `$mode`            | `'live'`   | `'live'` or `'test'` (Stripe test mode).                                            |
| `$config`          | `null`     | Array of the options below.                                                         |

| `$config` key     | Default                           | Description                                                                                  |
| ----------------- | --------------------------------- | -------------------------------------------------------------------------------------------- |
| `hostUrl`         | `'https://payments.bizswoop.app'` | API host.                                                                                    |
| `publicAccountId` | `null`                            | Connected account to act on, sent as `Public-Account-Id`.                                    |
| `userAgent`       | –                                 | Prepended to the SDK's `User-Agent` (e.g. `MyApp/1.0 PHP/8.3.0 JovviePaymentsClient/1.2.0`). |

An invalid `$mode`, or `'live'` with the `'debug'` provider, throws `InvalidArgumentException`.

`setPublicAccountId($id)` switches the connected account **on the same client** (it mutates the instance); pass `null` to stop sending the header:

```php
$client->setPublicAccountId('acct_public_id');
$payment = $client->payments()->get('payment_id');

$client->setPublicAccountId(null); // no Public-Account-Id header
```

`JovviePaymentsClient::VERSION` holds the SDK version.

## Payments

### `createOneTimePayment($amount, $currency, $description, $returnUrl, $cancelUrl, $options = [])`

```php
$payment = $client->payments()->createOneTimePayment(
	1000,
	'USD', // lower-cased before sending
	'Order #1001',
	'https://shop.example/success',
	'https://shop.example/cancel',
	[
		// all optional
		'customer' => 'cus_123',
		'captureMethod' => 'manual', // 'automatic' | 'automatic_async' | 'manual'
		'lineItems' => [['name' => 'T-shirt', 'amount' => '10.00', 'quantity' => '1']],
		'lineTotal' => '10.00',
		'preferences' => ['terminalType' => 'web'],
		'metadata' => ['orderId' => 1001],
	]
);
$payment->generatedData->checkoutLink;
$payment->generatedData->paymentIntentId;
```

Returns a `OneTimePayment`. `$options` is sent as-is in the request body.

### `createSubscriptionsPayment($customer, $items, $currency, $description, $returnUrl, $cancelUrl, $options = [])`

```php
$subscription = $client->payments()->createSubscriptionsPayment(
	['email' => 'jane@example.com', 'name' => 'Jane'], // or an existing customer ID
	[
		['price' => 'price_123', 'quantity' => 1],
		[
			'price' => [
				'product' => ['name' => 'Pro plan'], // or an existing product ID
				'unitAmount' => 2000,
				'recurring' => ['interval' => 'month', 'intervalCount' => 1],
			],
		],
	],
	'usd',
	'Pro plan',
	'https://shop.example/success',
	'https://shop.example/cancel'
);
$subscription->generatedData->checkoutLink;
$subscription->generatedData->subscriptionId;
$subscription->generatedData->setupIntentId;
```

Returns a `SubscriptionPayment`. `lineItems`, `lineTotal`, `preferences` and `metadata` are optional `$options`, as for one-time payments; subscription line items also need an `interval` (e.g. `'Every 1 month'`).

### `get($id)`

```php
use JovviePayments\Schema\SubscriptionPayment\SubscriptionPayment;

$payment = $client->payments()->get('payment_id'); // OneTimePayment | SubscriptionPayment
if ($payment instanceof SubscriptionPayment) {
	echo $payment->generatedData->subscriptionId;
} else {
	echo $payment->generatedData->checkoutLink;
}
```

Both payment types extend `JovviePayments\Schema\Payment\Payment`, which has `id`, `type` (`'one-time'` or `'subscription'`), `mode`, `description`, `lineTotal`, `amount`, `currency`, `feeAmount`, `status`, `createdAt`, `updatedAt` and `platformProvider`.

## Accounts

```php
$self = $client->accounts()->self(); // SelfAccount: the current (or selected) account
echo $self->platformPublicKey, $self->account->stripeAccountId;

$accounts = $client->accounts()->getAll(); // Account[]
foreach ($accounts as $account) {
	echo $account->publicId, $account->name, $account->authorized ? 'yes' : 'no';
}
```

`Account` has `publicId`, `name`, `country`, `isMain`, `stripeAccountId`, `haveStripeConnect`, `detailsSubmitted` and `authorized`.

## Authorization links

Links a merchant opens to connect their Stripe account.

```php
$link = $client->authorizationLinks()->create(
	[
		'redirectUrl' => 'https://shop.example/connected',
		'locationId' => 'tml_location',
		'paymentMethodTypes' => ['card', 'terminal'], // 'card' | 'terminal' | 'request-terminal', or true
	],
	(new DateTimeImmutable('+1 hour'))->format(DATE_ATOM) // optional: expiresAt, ISO 8601
);
echo $link->authorizationLink;
```

With `paymentMethodTypes: true`, `'request-terminal'` in the payment options is replaced with `'terminal'`.

## Connection tokens

Stripe Terminal connection tokens.

```php
$token = $client->connectionTokens()->create();
$token->secret;
$token->location; // may be null
```

## Errors

| Class                                  | When                                                                                  |
| -------------------------------------- | ------------------------------------------------------------------------------------- |
| `InvalidArgumentException`             | Invalid constructor argument, missing/invalid HTTP method or URL in `request()`.      |
| `Exception`                            | No response (cURL error) or other failure; message starts with `Failed to make request:`, original in `getPrevious()`. |
| `Http\Error\ResponseError`             | HTTP status ≥ 400. Base class of the ones below.                                      |
| `Http\Error\BadRequestError`           | 400: invalid params, or unknown platform provider                                     |
| `Http\Error\UnauthorizedError`         | 401: invalid keys, keys not issued for this platform provider, or clock skew          |
| `Http\Error\ForbiddenError`            | 403                                                                                   |
| `Http\Error\NotFoundError`             | 404                                                                                   |
| `WebhookEvents\Error\WebhookError`     | Webhook parsing or signature verification failed, or a required field is missing.    |
| `WebhookEvents\Error\UnknownEventError`| Webhook event type unknown to this SDK version (a `WebhookError`). See `getEvent()`.  |

`ResponseError` has:

- `getCode()`: HTTP status code
- `getResponse()`: the `Http\Response` (`getStatusCode()`, `getHeaders()`, `getBody()`; the body is the decoded JSON as `stdClass`)
- `getMessage()`: e.g. `Resource not found, url: https://payments.bizswoop.app/api/connect/v1/stripe-account/checkout-page/payments/...`

```php
use JovviePayments\Http\Error\NotFoundError;
use JovviePayments\Http\Error\ResponseError;

try {
	$client->payments()->get('payment_id');
} catch (NotFoundError $e) {
	// payment does not exist
} catch (ResponseError $e) {
	error_log($e->getCode() . ' ' . json_encode($e->getResponse()->getBody()));
} catch (Exception $e) {
	error_log('request failed: ' . $e->getMessage());
}
```

## Webhooks

`constructEvent` needs the **raw request body**, exactly as received, and the `X-Jovvie-Signature` header. Re-serialized JSON will fail signature verification.

```php
use JovviePayments\WebhookEvent;
use JovviePayments\WebhookEvents\Error\WebhookError;
use JovviePayments\WebhookEvents\Events\PaymentCompleted;
use JovviePayments\WebhookEvents\Events\SubscriptionCancelled;

$body = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_JOVVIE_SIGNATURE'] ?? '';

try {
	$event = WebhookEvent::constructEvent($body, $signature, WEBHOOK_KEY);
} catch (WebhookError $e) {
	http_response_code(400);
	exit($e->getMessage());
}

if ($event instanceof PaymentCompleted) {
	echo $event->getPaymentId(), $event->getInvoiceAmount(), $event->getInvoicePaid();
}
switch ($event->getType()) {
	case 'jovvie.subscription.cancelled':
		echo $event->getSubscriptionId();
		break;
}
http_response_code(200);
```

Laravel: `$request->getContent()` and `$request->header('X-Jovvie-Signature')`. Symfony: `$request->getContent()` and `$request->headers->get('X-Jovvie-Signature')`.

The signature is the lowercase hex HMAC-SHA256 of `{id}.{version}.{timestamp}.{body}` with the webhook key. Events older than 300 seconds are rejected with `WebhookError`.

Supported events:

Every event has `getPaymentId()`, `getPaymentMode()` and `getPaymentStatus()`. Other getters depend on the class:

| Class                          | `getType()`                             | `getPaymentIntentId()` | `getSubscriptionId()` | Other getters                                                                 |
| ------------------------------ | --------------------------------------- | :--------------------: | :-------------------: | ----------------------------------------------------------------------------- |
| `PaymentAbandoned`             | `jovvie.payment.abandoned`              | ✓                      |                       |                                                                               |
| `PaymentCancelled`             | `jovvie.payment.cancelled`              | ✓                      |                       | `getInvoiceAmount()`, `getInvoiceCurrency()`                                  |
| `PaymentCompleted`             | `jovvie.payment.completed`              | ✓                      |                       | `getInvoiceAmount()`, `getInvoiceCurrency()`, `getInvoicePaid()`              |
| `PaymentFailed`                | `jovvie.payment.failed`                 | ✓                      |                       | `getInvoiceAmount()`, `getInvoiceCurrency()`                                  |
| `PaymentRefunded`              | `jovvie.payment.refunded`               | ✓                      |                       | `getInvoiceAmount()`, `getInvoiceCurrency()`, `getInvoiceRefund()`            |
| `SubscriptionAbandoned`        | `jovvie.subscription.abandoned`         |                        | ✓                     |                                                                               |
| `SubscriptionCancelled`        | `jovvie.subscription.cancelled`         |                        | ✓                     |                                                                               |
| `SubscriptionRenewalCompleted` | `jovvie.subscription.renewal.completed` | ✓                      | ✓                     | `getInvoiceAmount()`, `getInvoiceCurrency()`, `getInvoicePaid()`, `getInvoiceCreated()` |
| `SubscriptionRenewalFailed`    | `jovvie.subscription.renewal.failed`    |                        | ✓                     | `getInvoiceAmount()`, `getInvoiceCurrency()`, `getSubscriptionNextPaymentAttempt()` |
| `SubscriptionStatusUpdate`     | `jovvie.subscription.status.update`     |                        | ✓                     | `getSubscriptionStatus()` (deprecated: use `getPaymentStatus()`)              |

`getPaymentMode()` is `'live'` or `'test'`; `getPaymentStatus()` is the payment status after the event (e.g. `'completed'`; for `jovvie.subscription.status.update` it is the new subscription status). `getPaymentIntentId()` is the Stripe Payment Intent ID, `getSubscriptionId()` the Stripe subscription ID. Amounts are in the smallest currency unit; `getInvoiceCreated()` and `getSubscriptionNextPaymentAttempt()` are Unix timestamps in seconds. A getter whose field is missing from the payload throws `WebhookError` (e.g. `Missing invoice.amount in payload`).

Every event extends `BaseEvent` (so `$event instanceof BaseEvent` holds, for `UnknownEvent` too), implements `JsonSerializable` and has `getId()` (use it to deduplicate deliveries), `getType()` and `getPayload()` (the raw payload array).

`WebhookEvent::constructFrom($data)` builds an event from a JSON string or array **without** verifying the signature.

### Unknown event types

By default an event type this SDK version does not know (e.g. one added on the server later) throws `UnknownEventError` (a `WebhookError`, message `Unsupported event type: ...`). The error carries the event as `getEvent()`: an `UnknownEvent` with `getType()`, `getId()` and `getPayload()`. To get the `UnknownEvent` returned instead, pass `false` as the last argument (`$throwUnknownEvents`) to `constructEvent` / `constructFrom`. Either way the signature and other fields are still validated.

```php
use JovviePayments\WebhookEvents\Error\UnknownEventError;

try {
	$event = WebhookEvent::constructEvent($body, $signature, WEBHOOK_KEY);
	// ...
} catch (UnknownEventError $e) {
	error_log("Unhandled webhook {$e->getEvent()->getType()} ({$e->getEvent()->getId()})");
	http_response_code(200);
}
```

```php
use JovviePayments\WebhookEvents\Events\UnknownEvent;

$event = WebhookEvent::constructEvent($body, $signature, WEBHOOK_KEY, false);
if ($event instanceof UnknownEvent) {
	http_response_code(200);
	return;
}
```

## Low-level requests

`$client->request($method, $path, $params = [], $data = [], $config = [])` calls any endpoint under the API base URL, with the same signing, headers and error mapping as the resource methods. `$params` becomes the query string (`http_build_query`), `$data` the JSON body (for `POST`, `PUT`, `PATCH`), and `$config['headers']` adds raw header lines. Shortcuts: `get()`, `post()`, `put()`, `patch()`, `delete()`. Each also accepts a single config array (`method`, `url`, `params`, `data`, `headers`).

```php
$response = $client->request('GET', 'checkout-page/payments/payment_id');
$response->getStatusCode();
$response->getHeaders();
$response->getBody(); // decoded JSON (stdClass / array)

$client->post('connection-tokens', [], [], ['headers' => ['X-Custom: 1']]);
```
