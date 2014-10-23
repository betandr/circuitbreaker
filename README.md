CircuitBreaker (v 0.1)
==============

This implementation of the CircuitBreaker pattern provide a mechanism for resilience
against a cascade of failures which could adversely affect an application's performance.
The CircuitBreaker monitors successful and non-successful transactions then provides
feedback, by way of tripping the circuit breaker if the failures reach a certain threshold.

To construct a circuit breaker (with ArrayPersistence storage) use:

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

The default values are:
* _Breaker closed on create:_ true
* _Threshold:_ 25 failures before the circuit breaker opens
* _Timeout:_ 6000ms (100 minutes) before the isClosed starts to return a 'half-open' response
* _Will Retry:_ true/the circuit  will attempt re-tries after the timeout has expired

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

If the system reaches a threshold of registered failures (the client may me experiencing
something such as  a back-end not responding) it will 'trip' open the circuit so
subsequent calls to isClosed return false (or it's opposite 'isOpen' method returns true).
This can prevent the situation where requests time out which can cause more issues
by continually attempting requests on services which are unavailable. So any
subsequent requests can fail quickly and by handled by the client.

If the circuit breaker opens due to the threshold being reached or by an explicit
call to `open()`, the `isClosed()` method will return false. However if you
subsequently register a successful transaction then it will re-close the circuit
breaker, assuming the problems have now resolved and your code is able to execute.
This is so systems are able to recover if the upstream problem is resolved.

If 'will retry' (`set/getWillRetryAfterTimeout()`) is set to true, any calls to
`isClosed()` will return a true if the timeout (`set/getTimeout()`) has expired since the
last registered failure. This is so systems are given the chance to recover and the
circuit breaker can be re-closed if upstream services begin working again.
In this 'half-open' mode, the client can try another request, if it fails then
the timeout will be reset. If successful, the client can register a success
which will close the breaker properly. By setting 'will retry' to false, the breaker
will remain closed until explicitly closed with a successful transaction registered or
by calling the explicit `close()` method (see 'Short-cut Operation').

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
...which re-closes the circuit breaker and zeros the failure counter. Note that
this does not reset the last failure timestamp which is only ever set by an actual
failure.

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
