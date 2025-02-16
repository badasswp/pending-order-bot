# pending-order-bot
Send reminders on WooCommerce pending orders.

[![Coverage Status](https://coveralls.io/repos/github/badasswp/pending-order-bot/badge.svg?branch=chore-improve-unit-tests-and-test-coverage)](https://coveralls.io/github/badasswp/pending-order-bot?branch=chore-improve-unit-tests-and-test-coverage)

<img width="1342" alt="pending" src="https://github.com/user-attachments/assets/b1f2ca4f-1307-417e-a188-09037f2e76e3">

---

<img width="1527" alt="screenshot" src="https://github.com/user-attachments/assets/c03d318c-bfda-45df-88db-1b3f94c00b07" />

## Download

Download from [WordPress plugin repository](https://wordpress.org/plugins/pending-order-bot/).

You can also get the latest version from any of our [release tags](https://github.com/badasswp/pending-order-bot/releases).

## Why Pending Order Bot?

The average cart abandonment rate is __69.99%__, according to Baymard Institute. This is an average of 48 shopping cart abandonment studies, which range from 56% to 81%.

The middle point of just under a __70%__ cart abandonment rate means that only __three out of ten customers__ who fill their shopping carts __actually make it to checkout__ to complete their purchase.

This plugin helps remind customers of their abandoned cart orders, so they complete their purchases. It's that simple!

### Hooks

#### `pbot_reminder_interval`

This custom hook provides a way to specify the interval between sending reminders to WooCommerce users.

```php
add_filter( 'pbot_reminder_interval', [ $this, 'custom_time_interval' ], 10, 1 );

public function custom_time_interval( $interval ): integer {
    if ( $interval < DAY_IN_SECONDS ) {
        // Remind every 3 hours.
        return 3 * HOUR_IN_SECONDS;
    }

    return $interval;
}
```

**Parameters**

- interval _`{integer}`_ The time interval.
<br/>

#### `pbot_text_client`

This custom hook provides a simple way to filter the text client to use to send messages to users with pending orders.

```php
add_filter( 'pbot_text_client', [ $this, 'custom_text_client' ], 10, 1 );

public function custom_text_client( $client ): Client {
    if ( $client instanceOf Twilio ) {
        return new CustomTextClient();
    }

    return $client;
}
```

**Parameters**

- client _`{Client}`_ By default this will be an instance of the Twilio client.
<br/>
