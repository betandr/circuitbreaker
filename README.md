CircuitBreaker (currently in development)
==============

This implementation of the CircuitBreaker pattern provide a mechanism for resilience
against a cascade of failures which could adversely affect an application's performance.
The CurcuitBreaker monitors successful and non-successful transactions then provides
feedback, by way of tripping the circuit breaker if the failures reach a certain threshold.

To construct a circuit breaker use:

```php
$breaker = Breaker::build('breakerName', new ArrayPersistence);
```
...or with parameters:
```php
Breaker::build(
    'testBreaker',
    new ArrayPersistence,
    array(
        'timeout' => 300,
        'threshold' => 1,
        'will_retry' => false
    )
);
```
...or set the values long-hand:
```php
$breaker->setThreshold(1);
$breaker->setTimeout(300);
$breaker->setWillRetryAfterTimeout(false);
```

The circuit breaker is used with the code:

```php
if ($breaker->isClosed()) {
    try {
        // YOUR EXPENSIVE CALL HERE
        $breaker->success();

    } catch (Exception $e) {
        // log a failure
        $breaker->failure();
    }
} else {
    // FALLBACK TO SOMETHING ELSE HERE
}
```

If the system reaches a threshold of failures (i.e. a back-end is not
responding) then the system will 'trip' open the circuit so the request is no
longer performed. This can prevent request from timing out and causing more issues
by continually attempting requests on services which are experiencing issues. This
way any subsequent requests can fail quickly and by handled by the client.

If the circuit breaker opens then your logical test will fail in your mainline
code. However if you subsequently register a successful transaction then it will
re-close the circuit breaker and your code will be able to execute. This is so
systems are able to recover if the upstream problem is resolved. *This will be*
*re-factored to use a 'half-open' method which will change the breaker into a*
*half-state where exploratory transactions can be completed to test to see if the*
*situation has resolved.*

Short-cut Operation
----

The circuit breaker can be immediately tripped by using:

```php
$breaker->open();
```

...and closed:
```php
$breaker->close();
```
...or reset:
```php
$breaker->reset();
```
...which re-closes the circuit breaker and zeros the failure counter.

Persistence
----

Transaction counts are stored using implementation defined by the `PersistenceInterface`
interface. Currently only an `ArrayPersistence` implementation is available which
uses a volatile array.

Tests
----

To run all tests, use:

```
phpunit --bootstrap src/autoload.php tests
```

TODO
----

* Add 'half-open' impl using time-outs to re-try
* Add persistence mechanisms
